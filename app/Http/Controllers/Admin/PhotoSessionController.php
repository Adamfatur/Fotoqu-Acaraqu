<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\Photobox;
use App\Models\Package;
use App\Services\PhotoSessionService;
use App\Services\PhotoService;
use App\Services\FrameService;
use App\Jobs\SendFrameEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class PhotoSessionController extends Controller
{
    protected PhotoSessionService $sessionService;
    protected PhotoService $photoService;
    protected FrameService $frameService;

    public function __construct(
        PhotoSessionService $sessionService,
        PhotoService $photoService,
        FrameService $frameService
    ) {
        $this->sessionService = $sessionService;
        $this->photoService = $photoService;
        $this->frameService = $frameService;
    }

    public function index(Request $request)
    {
        try {
            $query = PhotoSession::with(['photobox', 'user', 'admin', 'frame', 'package'])
                ->whereNull('photobooth_event_id')
                ->latest();

            // Filter by status
            if ($request->filled('status')) {
                $query->where('session_status', $request->status);
            }

            // Filter by photobox
            if ($request->filled('photobox_id')) {
                $query->where('photobox_id', $request->photobox_id);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('session_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            }

            $sessions = $query->paginate(15);
            $photoboxes = Photobox::all();

            return view('admin.sessions.index', compact('sessions', 'photoboxes'));
        } catch (\Exception $e) {
            \Log::error("PhotoSessionController index error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat sessions: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $photoboxes = $this->sessionService->getAvailablePhotoboxes();
        $packages = Package::active()->ordered()->get();
        return view('admin.sessions.create', compact('photoboxes', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photobox_id' => 'required|exists:photoboxes,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'send_email' => 'boolean',
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'required|in:free,qris,edc',
            'payment_amount' => 'required|numeric|min:0',
            'payment_notes' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Create session
            $sessionData = $request->only(['photobox_id', 'customer_name', 'customer_email', 'package_id', 'notes']);

            // Set email to null if user doesn't want to receive email
            if (!$request->boolean('send_email')) {
                $sessionData['customer_email'] = null;
            }

            $session = $this->sessionService->createSession($sessionData, auth()->user());

            // Process payment immediately
            if ($request->payment_method === 'free') {
                // For free sessions, process a zero payment explicitly through the service
                $this->sessionService->processPayment(
                    $session,
                    [
                        'amount' => 0,
                        'payment_method' => $request->payment_method, // Gunakan nilai dari request untuk konsistensi
                        'notes' => $request->payment_notes ?? 'Free session'
                    ],
                    auth()->user()
                );
            } else {
                // For paid sessions, use the numeric value if available, otherwise parse the formatted value
                $paymentAmount = $request->filled('payment_amount_numeric')
                    ? $request->payment_amount_numeric
                    : preg_replace('/[^\d]/', '', $request->payment_amount);

                $this->sessionService->processPayment(
                    $session,
                    [
                        'amount' => $paymentAmount,
                        'payment_method' => $request->payment_method,
                        'notes' => $request->payment_notes
                    ],
                    auth()->user()
                );
            }

            // Refresh session to get updated payment_status
            $session->refresh();

            // Debug session state before approval
            \Log::info('Session before approval', [
                'id' => $session->id,
                'session_code' => $session->session_code,
                'payment_status' => $session->payment_status,
                'session_status' => $session->session_status,
                'total_price' => $session->total_price,
                'payment_method' => $request->payment_method
            ]);

            // Auto approve session after payment
            $this->sessionService->approveSession($session, auth()->user());

            DB::commit();

            return redirect()
                ->route('admin.sessions.show', $session)
                ->with('success', 'Sesi foto berhasil dibuat dan disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat sesi foto: ' . $e->getMessage());
        }
    }

    public function show(PhotoSession $session)
    {
        try {
            $session->load([
                'photobox',
                'user',
                'admin',
                'photos' => function ($q) {
                    $q->orderBy('sequence_number');
                },
                'selectedPhotos',
                'frame'
            ]);

            // Load these relations safely
            try {
                $session->load(['paymentLogs.admin']);
            } catch (\Exception $e) {
                \Log::warning("Could not load paymentLogs.admin relation", [
                    'session_id' => $session->id,
                    'error' => $e->getMessage()
                ]);
            }

            try {
                $session->load(['activityLogs.user']);
            } catch (\Exception $e) {
                \Log::warning("Could not load activityLogs.user relation", [
                    'session_id' => $session->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Queue context (for queue position display)
            $queuePosition = null;
            $queueTotal = 0;
            $runningSession = null;

            if ($session->photobox) {
                $photoboxId = $session->photobox->id;

                // Currently running session (if any)
                $runningSession = PhotoSession::where('photobox_id', $photoboxId)
                    ->whereIn('session_status', ['in_progress', 'photo_selection', 'processing'])
                    ->orderBy('started_at')
                    ->orderBy('id')
                    ->first();

                // Total approved in queue
                $queueTotal = PhotoSession::where('photobox_id', $photoboxId)
                    ->where('session_status', 'approved')
                    ->count();

                if ($session->session_status === 'approved') {
                    // Position among approved sessions (1-based), ordered by approved_at then id
                    $aheadCount = PhotoSession::where('photobox_id', $photoboxId)
                        ->where('session_status', 'approved')
                        ->where(function ($q) use ($session) {
                            $q->where('approved_at', '<', $session->approved_at)
                                ->orWhere(function ($q2) use ($session) {
                                    $q2->where('approved_at', $session->approved_at)
                                        ->where('id', '<', $session->id);
                                });
                        })
                        ->count();
                    $queuePosition = $aheadCount + 1;
                } elseif (in_array($session->session_status, ['in_progress', 'photo_selection', 'processing'])) {
                    // Currently running
                    $queuePosition = 0;
                }
            }

            return view('admin.sessions.show', compact('session', 'queuePosition', 'queueTotal', 'runningSession'));
        } catch (\Exception $e) {
            \Log::error("PhotoSessionController show error", [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal memuat session: ' . $e->getMessage());
        }
    }

    public function processPayment(Request $request, PhotoSession $session)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:free,qris,edc',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $this->sessionService->processPayment(
                $session,
                $request->only(['amount', 'payment_method', 'notes']),
                auth()->user()
            );

            return back()->with('success', 'Pembayaran berhasil diproses!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function approve(PhotoSession $session)
    {
        try {
            $this->sessionService->approveSession($session, auth()->user());
            return back()->with('success', 'Sesi foto berhasil disetujui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui sesi: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, PhotoSession $session)
    {
        // Make reason optional
        $reason = $request->input('reason', 'Dibatalkan oleh admin');

        try {
            $this->sessionService->cancelSession($session, auth()->user(), $reason);
            return back()->with('success', 'Sesi foto berhasil dibatalkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan sesi: ' . $e->getMessage());
        }
    }

    public function simulate(PhotoSession $session)
    {
        try {
            // Simulate photo capture for demo
            $photos = $this->photoService->simulatePhotoCapture($session);

            // Mark session as completed
            $this->sessionService->completeSession($session);

            return back()->with('success', 'Simulasi foto berhasil! ' . count($photos) . ' foto telah dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mensimulasi foto: ' . $e->getMessage());
        }
    }

    public function selectPhotos(Request $request, PhotoSession $session)
    {
        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:photos,id'
        ]);

        try {
            $this->photoService->selectPhotosForFrame($session, $request->photo_ids);
            return response()->json(['success' => true, 'message' => 'Foto berhasil dipilih!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createFrame(PhotoSession $session)
    {
        try {
            $frame = $this->frameService->createFrame($session);

            // Send email with frame (if customer provided email)
            SendFrameEmail::dispatch($frame);

            // Customize success message based on whether email is available
            $successMessage = $session->customer_email
                ? 'Frame berhasil dibuat dan email sedang dikirim!'
                : 'Frame berhasil dibuat! (Email tidak dikirim karena pelanggan tidak menyediakan email)';

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat frame: ' . $e->getMessage());
        }
    }

    /**
     * Retry processing for a session (Frame & GIF).
     * Useful for sessions that have photos but failed to complete processing.
     */
    public function retryProcessing(PhotoSession $session)
    {
        try {
            // 1. Validate photos exist
            if ($session->photos()->count() === 0) {
                return back()->with('error', 'Tidak dapat memproses: Sesi ini tidak memiliki foto.');
            }

            // 2. Ensure enough photos are selected
            $selectedCount = $session->selectedPhotos()->count();
            $requiredSlots = $session->frame_slots ?? 3; // Default to 3 if null

            if ($selectedCount < $requiredSlots) {
                // Auto-select the first N photos
                $photosToSelect = $session->photos()
                    ->orderBy('sequence_number')
                    ->take($requiredSlots)
                    ->get();

                if ($photosToSelect->count() < $requiredSlots) {
                    // If we still don't have enough photos (e.g. captured 2 but need 3), select what we have
                    // FrameService might still complain, but at least we try
                    \Log::warning("Retry Processing: Not enough photos to fill slots", [
                        'session_id' => $session->id,
                        'available' => $photosToSelect->count(),
                        'required' => $requiredSlots
                    ]);
                }

                foreach ($photosToSelect as $photo) {
                    $photo->update(['is_selected' => true]);
                }

                // Refresh relation
                $session->load('selectedPhotos');
            }

            // 3. Ensure Frame exists or create it (Force Recreate for Rescue)
            if ($session->frame) {
                try {
                    // Delete existing frame record so we can generate a fresh one
                    // We don't necessarily need to delete the S3 file as the new one will have a new filename
                    // But deleting the record allows createFrame to make a new one associated with the session
                    $session->frame->delete();
                    $session->refresh(); // Refresh relationship
                } catch (\Exception $e) {
                    \Log::warning("Retry Processing: Failed to delete old frame", ['session_id' => $session->id, 'error' => $e->getMessage()]);
                    // Continue anyway
                }
            }

            try {
                $this->frameService->createFrame($session);
            } catch (\Exception $e) {
                \Log::error("Retry Processing: Failed to create frame", ['session_id' => $session->id, 'error' => $e->getMessage()]);
                throw new \Exception("Gagal membuat frame: " . $e->getMessage());
            }

            // 4. Dispatch GIF generation
            // We use the job directly to ensure it runs
            \App\Jobs\GenerateSessionGif::dispatch($session);

            // 5. Update status if needed
            if ($session->session_status !== 'completed') {
                $session->update(['session_status' => 'completed']);
            }

            return back()->with('success', 'Proses ulang berhasil dimulai! Frame lama dihapus dan dibuat ulang, GIF sedang diproses.');
        } catch (\Throwable $e) {
            \Log::error("Retry Processing Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal memproses ulang sesi: ' . $e->getMessage());
        }
    }

    public function print(Request $request, PhotoSession $session)
    {
        if (!$session->frame) {
            return back()->with('error', 'Frame belum tersedia untuk dicetak.');
        }

        try {
            // Check if high-res frame exists in S3
            if (!Storage::disk('s3')->exists($session->frame->s3_path)) {
                return back()->with('error', 'File frame tidak ditemukan di storage.');
            }

            // Only mark printed on POST to avoid GET side-effects
            if ($request->isMethod('post')) {
                $session->frame->update([
                    'is_printed' => true,
                    'printed_at' => now()
                ]);
            }

            // Log activity
            \Log::info("Admin accessed frame for printing", [
                'session_id' => $session->id,
                'session_code' => $session->session_code,
                'frame_id' => $session->frame->id
            ]);

            // Load session with frame
            $session->load('frame');

            // Return print view
            // Pass through simple view params (paper, autoprint)
            return view('admin.sessions.print', compact('session'));

        } catch (\Exception $e) {
            \Log::error("Failed to access frame for printing", [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengakses frame: ' . $e->getMessage());
        }
    }

    /**
     * Get session detail for AJAX modal
     */
    public function detail(PhotoSession $session)
    {
        $session->load(['photobox', 'user', 'admin', 'photos', 'frame', 'paymentLogs']);

        $html = view('admin.sessions.detail-modal', compact('session'))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Get frame preview for a session
     */
    public function getFramePreview(PhotoSession $session): JsonResponse
    {
        try {
            $frame = $session->frame;

            if (!$frame) {
                return response()->json([
                    'success' => false,
                    'error' => 'Frame not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'frame_url' => route('photobox.serve-frame', ['frame' => $frame->id]),
                'frame' => [
                    'id' => $frame->id,
                    'filename' => $frame->filename,
                    'status' => $frame->status,
                    'email_sent_at' => $frame->email_sent_at,
                    'created_at' => $frame->created_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get frame preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get GIF generation progress for a session (AJAX)
     */
    public function getGifProgress(PhotoSession $session): JsonResponse
    {
        try {
            $gif = $session->sessionGif()->first();
            if (!$gif) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'status' => 'none',
                ]);
            }
            return response()->json([
                'success' => true,
                'exists' => true,
                'status' => $gif->status,
                'progress' => (int) ($gif->progress ?? 0),
                'step' => $gif->step,
                'error' => $gif->error_message,
                'gif_url' => $gif->status === 'completed' ? route('public.serve-gif', ['gif' => $gif->id]) : null,
                'download_url' => $gif->status === 'completed' ? route('public.serve-gif', ['gif' => $gif->id, 'download' => 1]) : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend frame email to customer
     */
    public function resendEmail(PhotoSession $session)
    {
        try {
            $frame = $session->frame;

            if (!$frame) {
                return back()->with('error', 'Frame belum tersedia untuk dikirim email.');
            }

            // Check if customer email is provided
            if (!$session->customer_email) {
                return back()->with('error', 'Customer tidak menyediakan email untuk pengiriman frame.');
            }

            // Send email immediately instead of dispatching job for admin feedback
            Mail::to($session->customer_email)->send(new \App\Mail\FrameReadyMail($frame)); // FrameReadyMail will auto-increment FOTOKU counter

            // Update sent timestamp on frame
            $frame->update(['email_sent_at' => now()]);

            return back()->with('success', "Email frame berhasil dikirim ulang (#{$frame->email_count}) pada " . now()->format('d M Y H:i:s'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim ulang email: ' . $e->getMessage());
        }
    }

    /**
     * Purge all photos and frame assets for a cancelled/failed session.
     * Deletes local files, S3 objects, and DB rows for photos and final frame.
     * Session record remains for audit.
     */
    public function purgeAssets(Request $request, PhotoSession $session)
    {
        // Only allow purge for cancelled or failed sessions
        if (!in_array($session->session_status, ['cancelled', 'failed'])) {
            return back()->with('error', 'Hanya sesi yang dibatalkan/gagal yang bisa dihapus fotonya.');
        }

        try {
            DB::beginTransaction();

            // Precompute expected directories/prefixes for this session
            $localSessionDir = storage_path("app/private/photobox/{$session->session_code}");
            $photosPrefix = "photos/{$session->session_code}/";
            $framesPrefix = "frames/{$session->session_code}/";

            // Delete photos (local + S3 + DB)
            foreach ($session->photos as $photo) {
                try {
                    if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                        // Safety: ensure local file is inside this session's directory
                        $real = realpath($photo->local_path);
                        $expected = realpath($localSessionDir) ?: $localSessionDir;
                        if ($real && str_starts_with($real, $expected)) {
                            @unlink($photo->local_path);
                        } else {
                            \Log::warning('Purge skipped local file outside session dir', ['photo_id' => $photo->id, 'path' => $photo->local_path, 'expected_dir' => $expected]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Purge: gagal hapus file lokal foto', ['photo_id' => $photo->id, 'error' => $e->getMessage()]);
                }

                try {
                    if (!empty($photo->s3_path)) {
                        // Safety: ensure S3 key belongs to this session
                        if (str_starts_with($photo->s3_path, $photosPrefix)) {
                            Storage::disk('s3')->delete($photo->s3_path);
                        } else {
                            \Log::warning('Purge skipped S3 photo outside session prefix', ['photo_id' => $photo->id, 's3_path' => $photo->s3_path, 'expected_prefix' => $photosPrefix]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Purge: gagal hapus file S3 foto', ['photo_id' => $photo->id, 's3_path' => $photo->s3_path, 'error' => $e->getMessage()]);
                }

                // Delete DB record only if it belongs to this session
                if ((int) ($photo->photo_session_id ?? $photo->photo_session ?? $photo->photo_session_id) === (int) $session->id) {
                    $photo->delete();
                } else {
                    \Log::warning('Purge skipped DB delete for photo not owned by session', [
                        'photo_id' => $photo->id,
                        'photo_session_id' => $photo->photo_session_id ?? null,
                        'expected_session_id' => $session->id,
                    ]);
                }
            }

            // Delete frame if exists
            if ($session->frame) {
                $frame = $session->frame;
                try {
                    if (!empty($frame->s3_path)) {
                        if (str_starts_with($frame->s3_path, $framesPrefix)) {
                            Storage::disk('s3')->delete($frame->s3_path);
                        } else {
                            \Log::warning('Purge skipped S3 frame outside session prefix', ['frame_id' => $frame->id, 's3_path' => $frame->s3_path, 'expected_prefix' => $framesPrefix]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Purge: gagal hapus file S3 frame', ['frame_id' => $frame->id, 's3_path' => $frame->s3_path, 'error' => $e->getMessage()]);
                }

                // Delete DB record only if owned by this session
                if ((int) ($frame->photo_session_id ?? $frame->photo_session ?? $frame->photo_session_id) === (int) $session->id) {
                    $frame->delete();
                } else {
                    \Log::warning('Purge skipped DB delete for frame not owned by session', [
                        'frame_id' => $frame->id,
                        'frame_session_id' => $frame->photo_session_id ?? null,
                        'expected_session_id' => $session->id,
                    ]);
                }
            }

            // Delete bonus GIF if exists
            try {
                $gif = $session->sessionGif()->first();
                if ($gif) {
                    $localSessionDir = storage_path("app/private/photobox/{$session->session_code}");
                    if (!empty($gif->local_path)) {
                        $real = realpath($gif->local_path);
                        $expected = realpath($localSessionDir) ?: $localSessionDir;
                        if ($real && str_starts_with($real, $expected)) {
                            @unlink($real);
                        }
                    }
                    if (!empty($gif->s3_path)) {
                        $gifPrefix = 'gifs/' . $session->session_code . '/';
                        if (str_starts_with($gif->s3_path, $gifPrefix)) {
                            Storage::disk('s3')->delete($gif->s3_path);
                        }
                    }
                    if ($gif->photo_session_id === $session->id) {
                        $gif->delete();
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Purge: failed to delete session GIF', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Delete bonus GIF if exists (local, S3, DB) with safety checks
            try {
                $gif = $session->sessionGif()->first();
                if ($gif) {
                    $localSessionDir = storage_path("app/private/photobox/{$session->session_code}");
                    if (!empty($gif->local_path)) {
                        $real = realpath($gif->local_path);
                        $expected = realpath($localSessionDir) ?: $localSessionDir;
                        if ($real && str_starts_with($real, $expected)) {
                            @unlink($real);
                        }
                    }
                    if (!empty($gif->s3_path)) {
                        $gifPrefix = 'gifs/' . $session->session_code . '/';
                        if (str_starts_with($gif->s3_path, $gifPrefix)) {
                            \Storage::disk('s3')->delete($gif->s3_path);
                        }
                    }
                    if ((int) $gif->photo_session_id === (int) $session->id) {
                        $gif->delete();
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Purge: failed to delete session GIF', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Also try removing the session directory locally if empty
            try {
                $dir = storage_path("app/private/photobox/{$session->session_code}");
                if (is_dir($dir)) {
                    // Attempt to remove leftover files and directory
                    foreach (glob($dir . '/*') as $file) {
                        @unlink($file);
                    }
                    @rmdir($dir);
                }
            } catch (\Exception $e) {
                \Log::debug('Purge: gagal hapus direktori lokal sesi', ['dir' => $dir, 'error' => $e->getMessage()]);
            }

            DB::commit();

            return back()->with('success', 'Semua foto dan frame untuk sesi ini telah dihapus. Data sesi tetap disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purge assets error', ['session_id' => $session->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus aset sesi: ' . $e->getMessage());
        }
    }
}
