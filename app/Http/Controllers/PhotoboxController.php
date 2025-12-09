<?php

namespace App\Http\Controllers;

use App\Models\Photobox;
use App\Models\PhotoSession;
use App\Models\ActivityLog;
use App\Models\Photo;
use App\Services\PhotoService;
use App\Services\FrameService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Exception;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoboxController extends Controller
{
    protected PhotoService $photoService;
    protected FrameService $frameService;

    public function __construct(PhotoService $photoService, FrameService $frameService)
    {
        $this->photoService = $photoService;
        $this->frameService = $frameService;
    }

    /**
     * Show photobox interface
     */
    public function show(Photobox $photobox): View
    {
        // Optional token gating: allow interface only with valid token when enabled
        $requireToken = true; // enforce token to view interface
        if ($requireToken) {
            $tokenValue = request()->query('token') ?: request()->header('X-Photobox-Token');
            if (!$tokenValue) {
                abort(403, 'Token diperlukan untuk mengakses Photobox');
            }
            $token = \App\Models\PhotoboxAccessToken::where('token', $tokenValue)
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->first();
            if (!$token || $token->photobox_id !== $photobox->id) {
                abort(403, 'Token tidak valid atau telah kedaluwarsa');
            }
        }

        // Check if photobox is active
        if ($photobox->status !== 'active') {
            abort(503, 'Photobox sedang tidak aktif');
        }

        // Get current active session for this photobox
        $activeSession = $photobox->activePhotoSessions()->first();

        // Get photobox settings
        $settings = $photobox->settings ?? [];
        $defaultSettings = [
            'countdown_seconds' => \App\Models\Setting::get('countdown_duration', 3),
            'photo_interval_seconds' => \App\Models\Setting::get('photo_interval', 3),
            'total_photos' => \App\Models\Setting::get('photo_count', 3),
            // Pass through JPEG quality for client capture. Support both 0-1 and 1-100 inputs.
            'photo_jpeg_quality' => \App\Models\Setting::get('frame_quality', 300) >= 100 ? 0.99 : 0.9,
        ];
        $settings = array_merge($defaultSettings, $settings);

        return view('photobox.interface', compact('photobox', 'activeSession', 'settings'));
    }

    /**
     * Get current session status for real-time updates
     */
    /**
     * Get current session status for real-time updates
     */
    public function getStatus(Photobox $photobox): JsonResponse
    {
        try {
            // 1. Check for specific active session (manual mode)
            // IMPORTANT: Exclude sessions from inactive/completed events to prevent "ghost" sessions
            $activeSession = $photobox->activePhotoSessions()
                ->with(['package', 'photoboothEvent'])
                ->where(function ($query) {
                    $query->whereNull('photobooth_event_id')
                        ->orWhereHas('photoboothEvent', function ($q) {
                            $q->where('status', 'active');
                        });
                })
                ->first();

            // 2. If no specific session, check for Active Event (Event Mode)
            $activeEvent = null;
            if (!$activeSession) {
                $activeEvent = \App\Models\PhotoboothEvent::active()
                    ->where('photobox_id', $photobox->id)
                    ->with('package')
                    ->first();
            }

            // 3. If still nothing, check for recently completed sessions (for preview)
            if (!$activeSession && !$activeEvent) {
                $activeSession = $photobox->photoSessions()
                    ->with(['package', 'photoboothEvent'])
                    ->where('session_status', 'completed')
                    ->whereHas('frame')
                    ->where('updated_at', '>=', now()->subMinutes(5))
                    ->orderBy('updated_at', 'desc')
                    ->first();
            }

            $response = ['success' => true];

            if ($activeSession) {
                $response['session'] = [
                    'id' => $activeSession->id,
                    'session_code' => $activeSession->session_code,
                    'customer_name' => $activeSession->customer_name,
                    'customer_email' => $activeSession->customer_email,
                    'frame_slots' => $activeSession->frame_slots,
                    'session_status' => $activeSession->session_status,
                    'payment_status' => $activeSession->payment_status,
                    'total_price' => $activeSession->total_price,
                    'created_at' => $activeSession->created_at,
                    'updated_at' => $activeSession->updated_at,
                    'package' => $activeSession->package ? [
                        'name' => $activeSession->package->name,
                        'print_type' => $activeSession->package->print_type ?? 'strip',
                        'print_count' => $activeSession->package->print_count ?? 1,
                    ] : null,
                ];

                // Override for Event Mode if quota is 0 or exceeded
                if ($activeSession->photoboothEvent) {
                    $evt = $activeSession->photoboothEvent;
                    if (
                        (!is_null($evt->print_quota) && $evt->prints_used >= $evt->print_quota) ||
                        (is_null($evt->print_quota) || $evt->print_quota == 0)
                    ) {
                        if (isset($response['session']['package'])) {
                            $response['session']['package']['print_type'] = 'none';
                        }
                    }
                }
            } elseif ($activeEvent) {
                // If event is active but no session found (should be rare as we auto-create), 
                // we can return empty or wait for next poll.
                // But to be safe, if for some reason no session exists, we could create one here or just return null.
                // For now, let's return null and rely on the auto-creation logic in completeSession / store.
                // Or better, trigger creation if missing?

                // Let's check if there is ANY approved session for this event.
                $nextSession = $photobox->photoSessions()
                    ->where('photobooth_event_id', $activeEvent->id)
                    ->where('session_status', 'approved')
                    ->first();

                if ($nextSession) {
                    $response['session'] = [
                        'id' => $nextSession->id,
                        'session_code' => $nextSession->session_code,
                        'customer_name' => $nextSession->customer_name,
                        'customer_email' => $nextSession->customer_email,
                        'frame_slots' => $nextSession->frame_slots,
                        'session_status' => $nextSession->session_status,
                        'payment_status' => $nextSession->payment_status,
                        'total_price' => $nextSession->total_price,
                        'created_at' => $nextSession->created_at,
                        'updated_at' => $nextSession->updated_at,
                        'is_event_mode' => true,
                        'event_name' => $activeEvent->name,
                        'package' => $nextSession->package ? [
                            'name' => $nextSession->package->name,
                            'print_type' => $nextSession->package->print_type ?? 'strip',
                            'print_count' => $nextSession->package->print_count ?? 1,
                        ] : null,
                    ];

                    // Override print type if quota exceeded OR if quota is 0 (Digital Only)
                    if (
                        (!is_null($activeEvent->print_quota) && $activeEvent->prints_used >= $activeEvent->print_quota) ||
                        (is_null($activeEvent->print_quota) || $activeEvent->print_quota == 0)
                    ) {
                        $response['session']['package']['print_type'] = 'none';
                    }
                } else {
                    // If absolutely no session exists (e.g. first one deleted?), create one immediately
                    $newSession = \App\Models\PhotoSession::create([
                        'photobox_id' => $photobox->id,
                        'photobooth_event_id' => $activeEvent->id,
                        'package_id' => $activeEvent->package_id,
                        'admin_id' => 1, // System admin ID
                        'customer_name' => 'Event Guest',
                        'customer_email' => 'guest@event.com',
                        'frame_slots' => $activeEvent->package->frame_slots ?? 6,
                        'session_status' => 'approved',
                        'payment_status' => 'paid',
                        'total_price' => 0,
                        'approved_at' => now(),
                    ]);

                    // Recursive call or construct response manually? Let's construct manually to avoid recursion depth issues
                    $response['session'] = [
                        'id' => $newSession->id,
                        'session_code' => $newSession->session_code,
                        'customer_name' => $newSession->customer_name,
                        'customer_email' => $newSession->customer_email,
                        'frame_slots' => $newSession->frame_slots,
                        'session_status' => $newSession->session_status,
                        'payment_status' => $newSession->payment_status,
                        'total_price' => $newSession->total_price,
                        'created_at' => $newSession->created_at,
                        'updated_at' => $newSession->updated_at,
                        'is_event_mode' => true,
                        'event_name' => $activeEvent->name,
                        'package' => [
                            'name' => $activeEvent->package->name,
                            'print_type' => $activeEvent->package->print_type ?? 'strip',
                            'print_count' => $activeEvent->package->print_count ?? 1,
                        ],
                    ];

                    if (
                        (!is_null($activeEvent->print_quota) && $activeEvent->prints_used >= $activeEvent->print_quota) ||
                        (is_null($activeEvent->print_quota) || $activeEvent->print_quota == 0)
                    ) {
                        $response['session']['package']['print_type'] = 'none';
                    }
                }
            } else {
                $response['session'] = null;
            }

            return response()->json($response);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get session status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start photo session
     */
    public function start(Request $request, Photobox $photobox): JsonResponse
    {
        try {
            // Check if photobox is active
            if ($photobox->status !== 'active') {
                return response()->json(['error' => 'Photobox tidak aktif'], 400);
            }

            // Determine which session to run deterministically
            // If already in_progress, keep using it. Otherwise, start the earliest approved session.
            $activeSession = null;
            \DB::transaction(function () use ($photobox, &$activeSession) {
                // Prefer an existing in_progress session if any
                $inProgress = $photobox->photoSessions()
                    ->with('package')
                    ->where('session_status', 'in_progress')
                    ->orderBy('started_at')
                    ->orderBy('id')
                    ->first();

                if ($inProgress) {
                    $activeSession = $inProgress;
                    return;
                }

                // Otherwise pick the earliest approved session in queue
                // IMPORTANT: Exclude sessions from inactive/completed events
                $approved = $photobox->photoSessions()
                    ->with('package')
                    ->where('session_status', 'approved')
                    ->where(function ($query) {
                        $query->whereNull('photobooth_event_id')
                            ->orWhereHas('photoboothEvent', function ($q) {
                                $q->where('status', 'active');
                            });
                    })
                    ->orderBy('approved_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if ($approved) {
                    $approved->update([
                        'session_status' => 'in_progress',
                        'started_at' => now(),
                    ]);
                    $activeSession = $approved->fresh(['package']);
                    return;
                }

                // If no manual session, check for Active Event (Event Mode)
                $activeEvent = \App\Models\PhotoboothEvent::active()
                    ->where('photobox_id', $photobox->id)
                    ->with('package')
                    ->first();
                if ($activeEvent) {
                    // Create a new session on the fly for this event
                    $newSession = \App\Models\PhotoSession::create([
                        'photobox_id' => $photobox->id,
                        'photobooth_event_id' => $activeEvent->id,
                        'package_id' => $activeEvent->package_id,
                        'customer_name' => 'Event Guest', // Can be prompted later if needed
                        'customer_email' => 'guest@event.com',
                        'frame_slots' => $activeEvent->package->frame_slots ?? 6, // Use package default
                        'session_status' => 'in_progress',
                        'payment_status' => 'paid', // Events are pre-paid or free
                        'total_price' => 0,
                        'started_at' => now(),
                        'approved_at' => now(),
                    ]);

                    $activeSession = $newSession->fresh(['package']);
                }
            });

            if (!$activeSession) {
                return response()->json(['error' => 'Tidak ada sesi aktif'], 400);
            }

            // Log activity
            ActivityLog::create([
                'action' => 'session_started',
                'description' => "Sesi foto dimulai di photobox {$photobox->code}",
                'photobox_id' => $photobox->id,
                'photo_session_id' => $activeSession->id,
                'metadata' => [
                    'photobox_code' => $photobox->code,
                    'session_code' => $activeSession->session_code,
                    'is_event_mode' => !!$activeSession->photobooth_event_id
                ]
            ]);

            // Prepare merged settings
            $defaultSettings = [
                'countdown_seconds' => \App\Models\Setting::get('countdown_duration', 3),
                'photo_interval_seconds' => \App\Models\Setting::get('photo_interval', 3),
                'total_photos' => \App\Models\Setting::get('photo_count', 3),
                'photo_jpeg_quality' => \App\Models\Setting::get('frame_quality', 300) >= 100 ? 0.99 : 0.9,
            ];
            $finalSettings = array_merge($defaultSettings, $photobox->settings ?? []);

            return response()->json([
                'success' => true,
                'session' => $activeSession,
                'settings' => $finalSettings
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memulai sesi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Capture photo
     */
    public function capture(Request $request, Photobox $photobox): JsonResponse
    {
        try {
            $request->validate([
                'photo_data' => 'required|string', // Base64 encoded image
                'sequence_number' => 'required|integer|min:1'
            ]);

            // Get current active session
            $activeSession = $photobox->activePhotoSessions()->with('package')->first();

            if (!$activeSession || $activeSession->session_status !== 'in_progress') {
                return response()->json(['error' => 'Tidak ada sesi aktif'], 400);
            }

            // Save photo using PhotoService
            $photo = $this->photoService->savePhotoFromBase64(
                $activeSession,
                $request->photo_data,
                $request->sequence_number
            );

            // Check if all photos are captured
            $totalPhotos = \App\Models\Setting::get('photo_count', 3);
            $capturedPhotos = $activeSession->photos()->count();

            $response = [
                'success' => true,
                'photo' => $photo,
                'captured_count' => $capturedPhotos,
                'total_photos' => $totalPhotos,
                'is_complete' => $capturedPhotos >= $totalPhotos
            ];

            // If all photos captured, move to photo selection phase
            if ($capturedPhotos >= $totalPhotos) {
                $activeSession->update(['session_status' => 'photo_selection']);
                $response['next_phase'] = 'photo_selection';

                ActivityLog::create([
                    'action' => 'photos_captured',
                    'description' => "Semua {$totalPhotos} foto berhasil diambil di photobox {$photobox->code}",
                    'photobox_id' => $photobox->id,
                    'photo_session_id' => $activeSession->id,
                    'metadata' => [
                        'total_photos' => $totalPhotos,
                        'photobox_code' => $photobox->code
                    ]
                ]);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil foto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Select photos for frame
     */
    public function selectPhotos(Request $request, Photobox $photobox): JsonResponse
    {
        try {
            // Increase execution time limit for frame processing
            set_time_limit(120); // 2 minutes

            $request->validate([
                'selected_photos' => 'required|array',
                'selected_photos.*' => 'integer|exists:photos,id',
                'frame_design' => 'nullable|string',
                'photo_filters' => 'nullable|array'
            ]);

            // Get current active session
            $activeSession = $photobox->activePhotoSessions()->with('package')->first();

            // Debug logging untuk status session
            \Log::info("PhotoboxController selectPhotos debug", [
                'photobox_code' => $photobox->code,
                'active_session_found' => !!$activeSession,
                'session_id' => $activeSession ? $activeSession->id : null,
                'session_status' => $activeSession ? $activeSession->session_status : null,
                'expected_status' => 'photo_selection',
                'request_data' => $request->all()
            ]);

            if (!$activeSession) {
                return response()->json(['error' => 'Tidak ada sesi aktif'], 400);
            }

            // Allow photo_selection or in_progress status (in case photos were just captured)
            if (!in_array($activeSession->session_status, ['photo_selection', 'in_progress'])) {
                return response()->json([
                    'error' => 'Sesi tidak dalam fase pemilihan foto',
                    'current_status' => $activeSession->session_status,
                    'expected_status' => 'photo_selection or in_progress'
                ], 400);
            }

            // Auto-update status to photo_selection if still in_progress but has photos
            if ($activeSession->session_status === 'in_progress') {
                $totalPhotos = \App\Models\Setting::get('photo_count', 3);
                $capturedPhotos = $activeSession->photos()->count();

                if ($capturedPhotos >= $totalPhotos) {
                    $activeSession->update(['session_status' => 'photo_selection']);
                    \Log::info("Auto-updated session status to photo_selection", [
                        'session_id' => $activeSession->id,
                        'captured_photos' => $capturedPhotos,
                        'total_photos' => $totalPhotos
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Belum semua foto selesai diambil',
                        'captured_photos' => $capturedPhotos,
                        'total_photos' => $totalPhotos
                    ], 400);
                }
            }

            // Validate number of selected photos matches user selection requirement (3 photos for 6-slot frame)
            // Validate number of selected photos matches user selection requirement (3 photos for 6-slot frame)
            // Use frame_slots from session OR default from config/db
            $requiredSelection = $activeSession->frame_slots > 0 ? ($activeSession->frame_slots / 2) : 3;
            // NOTE: For 2-slot 4R, we select 2 photos. For 4-slot, 4. For 6-slot (2 strips), we traditionally select 3 and duplicate.
            // But logic above assumes simple division. 
            // Let's refine based on slot count:
            if ($activeSession->frame_slots == 2)
                $requiredSelection = 2;
            elseif ($activeSession->frame_slots == 4)
                $requiredSelection = 4;
            elseif ($activeSession->frame_slots == 6)
                $requiredSelection = 3; // 3 photos duplicated
            else
                $requiredSelection = $activeSession->frame_slots ?? 3;

            if (count($request->selected_photos) !== $requiredSelection) {
                return response()->json(['error' => "Pilih tepat {$requiredSelection} foto"], 400);
            }

            // Validate that all selected photos exist and belong to this session
            $existingPhotos = $activeSession->photos()->whereIn('id', $request->selected_photos)->pluck('id')->toArray();
            if (count($existingPhotos) !== count($request->selected_photos)) {
                \Log::error("PhotoboxController: Invalid photo selection", [
                    'session_id' => $activeSession->id,
                    'requested_photos' => $request->selected_photos,
                    'existing_photos' => $existingPhotos
                ]);
                return response()->json(['error' => 'Beberapa foto yang dipilih tidak valid'], 400);
            }

            // Store frame design and photo filters in session
            $frameDesign = $request->frame_design ?? 'default';
            $photoFilters = $request->photo_filters ?? [];

            // Debug logging for frame design
            \Log::info("PhotoboxController: Frame selection debugging", [
                'session_id' => $activeSession->id,
                'raw_frame_design' => $request->frame_design,
                'processed_frame_design' => $frameDesign,
                'is_numeric' => is_numeric($frameDesign),
                'frame_design_type' => gettype($frameDesign)
            ]);

            $activeSession->update([
                'frame_design' => $frameDesign,
                'photo_filters' => $photoFilters  // Remove json_encode - model handles this with casting
            ]);

            // Verify what was actually saved to database
            $activeSession->refresh();
            \Log::info("PhotoboxController: Frame design and filters stored and verified", [
                'session_id' => $activeSession->id,
                'stored_frame_design' => $activeSession->frame_design,
                'stored_photo_filters' => $activeSession->photo_filters,
                'frame_design_matches' => $activeSession->frame_design === $frameDesign
            ]);

            // Mark selected photos
            $activeSession->photos()->update(['is_selected' => false]);
            $updated = $activeSession->photos()->whereIn('id', $request->selected_photos)->update(['is_selected' => true]);

            if ($updated !== $requiredSelection) {
                \Log::error("PhotoboxController: Failed to mark photos as selected", [
                    'session_id' => $activeSession->id,
                    'expected_updates' => $requiredSelection,
                    'actual_updates' => $updated
                ]);
                return response()->json(['error' => 'Gagal menandai foto yang dipilih'], 500);
            }

            // Update session status to processing
            $activeSession->update(['session_status' => 'processing']);

            // Generate frame
            try {
                $frame = $this->frameService->createFrame($activeSession);
            } catch (\Exception $e) {
                // Log the full error for debugging
                \Log::error("Frame creation failed", [
                    'session_id' => $activeSession->id,
                    'session_code' => $activeSession->session_code,
                    'photobox_code' => $photobox->code,
                    'selected_photos' => $request->selected_photos,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Reset session status to allow retry
                $activeSession->update(['session_status' => 'photo_selection']);

                return response()->json([
                    'error' => 'Gagal memproses foto: ' . $e->getMessage()
                ], 500);
            }

            // Update session status to completed FIRST
            $activeSession->update(['session_status' => 'completed']);

            // After completion, upload captured photos to S3 in the background (QUEUED)
            try {
                // Dispatch job to upload photos asynchronously
                \App\Jobs\UploadSessionPhotosToS3::dispatch($activeSession->id);
                \Log::info("Dispatched background S3 upload job for session {$activeSession->session_code}");
            } catch (\Exception $e) {
                \Log::error('Failed to dispatch S3 upload job', [
                    'session_id' => $activeSession->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Ensure placeholder GIF row exists, then dispatch background bonus GIF generation (non-blocking)
            try {
                if (!$activeSession->sessionGif()->exists()) {
                    \App\Models\SessionGif::create([
                        'photo_session_id' => $activeSession->id,
                        'filename' => 'fotoku-' . $activeSession->session_code . '-anim.gif',
                        'status' => 'processing',
                    ]);
                }
                $gifQueue = config('fotoku.gif.queue', 'media');
                \App\Jobs\GenerateSessionGif::dispatch($activeSession->id)->onQueue($gifQueue);
            } catch (\Throwable $e) {
                \Log::warning('Failed to dispatch GenerateSessionGif job', [
                    'session_id' => $activeSession->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send email immediately (not via queue for now to ensure delivery)
            try {
                Mail::to($activeSession->customer_email)->send(new \App\Mail\FrameReadyMail($frame));

                // Update frame to mark email as sent
                $frame->update(['email_sent_at' => now()]);

                \Log::info("Frame email sent successfully", [
                    'frame_id' => $frame->id,
                    'email' => $activeSession->customer_email,
                    'session_code' => $activeSession->session_code
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to send frame email", [
                    'frame_id' => $frame->id,
                    'email' => $activeSession->customer_email,
                    'error' => $e->getMessage()
                ]);

                // Still dispatch to queue as backup
                \App\Jobs\SendFrameEmail::dispatch($frame);
            }

            ActivityLog::create([
                'action' => 'photos_selected_and_frame_created',
                'description' => "Foto terpilih dan frame berhasil dibuat untuk sesi di photobox {$photobox->code}",
                'photobox_id' => $photobox->id,
                'photo_session_id' => $activeSession->id,
                'metadata' => [
                    'selected_photos_count' => count($request->selected_photos),
                    'frame_id' => $frame->id,
                    'photobox_code' => $photobox->code
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Frame berhasil dibuat!',
                'frame' => $frame,
                'session' => $activeSession
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses foto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get photos for current session with signed URLs
     */
    public function getPhotos(Photobox $photobox): JsonResponse
    {
        try {
            // Get current active session
            $activeSession = $photobox->activePhotoSessions()->with('package')->first();

            if (!$activeSession) {
                return response()->json(['error' => 'Tidak ada sesi aktif'], 400);
            }

            // Get photos with signed URLs
            $photos = $this->photoService->getSessionPhotosWithUrls($activeSession, 60);

            return response()->json([
                'success' => true,
                'photos' => $photos,
                'session' => $activeSession
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil foto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Force stop the current session and release the photobox
     */
    public function forceStop(Photobox $photobox): JsonResponse
    {
        try {
            // Get current active session
            $activeSession = $photobox->activePhotoSessions()->with('package')->first();

            if (!$activeSession) {
                return response()->json([
                    'success' => true,
                    'message' => 'No active session to stop'
                ]);
            }

            // Force cancel the session
            $activeSession->update([
                'session_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_reason' => 'Emergency stop by admin'
            ]);

            // DO NOT delete photos here. We want to keep them for potential rescue/recovery.
            // $activeSession->photos()->delete();

            // Update photobox status
            $photobox->update([
                'status' => 'idle',
                'current_session_id' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session force stopped successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to force stop session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve photo file from S3 through Laravel
     */
    public function servePhoto(Photo $photo)
    {
        try {
            $mimeType = $photo->metadata['mime_type'] ?? 'image/jpeg';
            $disposition = request()->boolean('download') ? 'attachment' : 'inline';

            if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                $content = file_get_contents($photo->local_path);
            } else {
                // Fallback to S3
                $content = Storage::disk('s3')->get($photo->s3_path);
            }

            // Apply watermark for gallery/user serving (all user photos; frames are served via serveFrame)
            [$content, $mimeType] = $this->applyGalleryWatermark($content, $photo, $mimeType);

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Content-Disposition', $disposition . '; filename="' . $photo->filename . '"');

        } catch (Exception $e) {
            abort(404, 'Photo not found');
        }
    }

    /**
     * Securely serve a photo but scoped to a session code so users cannot increment IDs.
     * URL: /photobox/gallery/{session_code}/photo/{photo}
     */
    public function servePhotoForSession(PhotoSession $session, Photo $photo)
    {
        // Only allow access if the photo belongs to the given session
        if ($photo->photo_session_id !== $session->id) {
            abort(404);
        }

        try {
            $mimeType = $photo->metadata['mime_type'] ?? 'image/jpeg';
            $disposition = request()->boolean('download') ? 'attachment' : 'inline';

            if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                $content = file_get_contents($photo->local_path);
            } else {
                $content = Storage::disk('s3')->get($photo->s3_path);
            }

            // Apply same gallery watermarking
            [$content, $mimeType] = $this->applyGalleryWatermark($content, $photo, $mimeType);

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Content-Disposition', $disposition . '; filename="' . $photo->filename . '"');
        } catch (Exception $e) {
            abort(404, 'Photo not found');
        }
    }

    /**
     * Apply Fotoku brand watermark to a photo binary for gallery use.
     * Returns an array [content, mimeType]. Falls back to original on error.
     */
    private function applyGalleryWatermark(string $content, Photo $photo, string $originalMime = 'image/jpeg'): array
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($content);

            // Load watermark image
            $logoPath = public_path('watermark-fotoku.png');
            if (!file_exists($logoPath)) {
                $logoPath = null; // Fallback to text if not found
            }

            if ($logoPath) {
                $logo = $manager->read($logoPath);
                // Scale watermark to ~12% of image width (PNG alpha preserved)
                $targetWidth = max(80, min((int) round($image->width() * 0.12), 300));
                $logo = $logo->scaleDown(width: $targetWidth);

                $margin = max(24, (int) round(min($image->width(), $image->height()) * 0.02));
                $image->place($logo, 'bottom-right', $margin, $margin);
            } else {
                // Text fallback if logo not found
                $margin = max(24, (int) round(min($image->width(), $image->height()) * 0.02));
                $fontSize = max(20, (int) round($image->width() * 0.025));
                $image->text('FOTOKU', $image->width() - $margin - 10, $image->height() - $margin - 10, function ($font) use ($fontSize) {
                    $font->size($fontSize);
                    $font->color('#ffffff');
                    $font->align('right');
                    $font->valign('bottom');
                });
            }

            // NOTE: second/prospek watermark removed per request - only Fotoku watermark is applied here

            // Encode to JPEG for consistent delivery
            $newContent = $image->toJpeg(90);
            $mimeType = 'image/jpeg';
            return [$newContent, $mimeType];
        } catch (\Throwable $e) {
            \Log::warning('Watermarking failed, serving original photo', [
                'photo_id' => $photo->id,
                'error' => $e->getMessage(),
            ]);
            return [$content, $originalMime];
        }
    }

    /**
     * Serve frame file from S3 through Laravel
     */
    public function serveFrame(\App\Models\Frame $frame)
    {
        try {
            // Get frame content from S3
            $content = Storage::disk('s3')->get($frame->s3_path);

            // Default to PNG for frames
            $mimeType = 'image/png';

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Content-Disposition', 'inline; filename="' . $frame->filename . '"');

        } catch (Exception $e) {
            abort(404, 'Frame not found');
        }
    }

    /**
     * Serve session bonus GIF via Laravel.
     */
    public function serveGif(\App\Models\SessionGif $gif)
    {
        try {
            $disposition = request()->boolean('download') ? 'attachment' : 'inline';
            if (!empty($gif->local_path) && file_exists($gif->local_path)) {
                $content = file_get_contents($gif->local_path);
            } else {
                $content = Storage::disk('s3')->get($gif->s3_path);
            }

            // If the generated GIF predates the watermarking change (no step suffix),
            // apply a lightweight serve-time watermark as a fallback.
            $shouldServeTimeWM = is_string($gif->step ?? null) ? (str_contains($gif->step, 'completed-wm1') === false) : true;
            if ($shouldServeTimeWM && class_exists(\Imagick::class)) {
                try {
                    $wmPath = public_path('watermark-fotoku.png');
                    if (file_exists($wmPath)) {
                        $imagick = new \Imagick();
                        $imagick->readImageBlob($content);
                        $imagick = $imagick->coalesceImages();
                        $wmBlob = file_get_contents($wmPath);
                        foreach ($imagick as $frame) {
                            $wm = new \Imagick();
                            $wm->readImageBlob($wmBlob);
                            // Scale watermark to 20% of width
                            $targetWidth = max(1, (int) round($frame->getImageWidth() * 0.2));
                            $wm->resizeImage($targetWidth, 0, \Imagick::FILTER_LANCZOS, 1);
                            $margin = 16;
                            $posX = $frame->getImageWidth() - $wm->getImageWidth() - $margin;
                            $posY = $frame->getImageHeight() - $wm->getImageHeight() - $margin;
                            $frame->compositeImage($wm, \Imagick::COMPOSITE_OVER, $posX, $posY);
                            $wm->clear();
                            $wm->destroy();

                            // NOTE: second/prospek watermark removed per request
                        }
                        $imagick = $imagick->deconstructImages();
                        $content = $imagick->getImagesBlob();
                        $imagick->clear();
                        $imagick->destroy();
                    }
                } catch (\Throwable $e) {
                    \Log::warning('serveGif: fallback watermark failed', ['gif_id' => $gif->id, 'error' => $e->getMessage()]);
                }
            }

            return response($content)
                ->header('Content-Type', 'image/gif')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Content-Disposition', $disposition . '; filename="' . $gif->filename . '"');
        } catch (Exception $e) {
            abort(404, 'GIF not found');
        }
    }

    /**
     * Securely serve GIF for a given session code, 404 if not owned by session
     */
    public function serveGifForSession(PhotoSession $session, \App\Models\SessionGif $gif)
    {
        if ($gif->photo_session_id !== $session->id) {
            abort(404);
        }
        // Delegate to the existing serve implementation
        return $this->serveGif($gif);
    }

    /**
     * Download frame file with proper headers for downloading
     */
    public function downloadFrame(\App\Models\Frame $frame)
    {
        try {
            // Get frame content from S3
            $content = Storage::disk('s3')->get($frame->s3_path);

            // Generate proper filename with customer name if available
            $customerName = $frame->photoSession->customer_name ?? 'Customer';
            $cleanCustomerName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $customerName);
            $timestamp = $frame->created_at->format('Y-m-d_H-i-s');
            $downloadFilename = "Fotoku_Frame_{$cleanCustomerName}_{$timestamp}.jpg";

            // Default to JPEG for frames (better for photos)
            $mimeType = 'image/jpeg';

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Content-Disposition', 'attachment; filename="' . $downloadFilename . '"')
                ->header('Content-Length', strlen($content));

        } catch (Exception $e) {
            abort(404, 'Frame not found');
        }
    }

    /**
     * Trigger print for a session frame (Kiosk action)
     */
    public function printFrame(Request $request, Photobox $photobox): JsonResponse
    {
        try {
            // Get current active or just completed session
            $activeSession = $photobox->activePhotoSessions()->with('package', 'frame')->first();

            // If not found, try finding by specific ID if passed (in case it became 'completed')
            if (!$activeSession && $request->filled('session_id')) {
                $activeSession = $photobox->photoSessions()
                    ->with('package', 'frame')
                    ->find($request->session_id);
            }

            if (!$activeSession) {
                return response()->json(['error' => 'Sesi tidak ditemukan'], 404);
            }

            if (!$activeSession->frame) {
                return response()->json(['error' => 'Frame belum tersedia'], 400);
            }

            // Check package print settings
            $package = $activeSession->package;
            $printType = $package->print_type ?? 'strip';

            if ($printType === 'none') {
                return response()->json(['error' => 'Paket ini tidak termasuk cetak foto'], 403);
            }

            // Check if already printed (limit to 1x printing session per button click flow)
            // If "Print 1" (strip) or "Custom" is set, usually we allow printing ONCE per session to avoid abuse?
            // "jika dia paket print 1 maka dia hanya bisa print 1x"
            if ($activeSession->frame->is_printed) {
                return response()->json(['error' => 'Kuota cetak sudah habis (sudah dicetak)'], 403);
            }

            // Determine print count
            $copies = 1;
            if ($printType === 'custom' && isset($package->print_count)) {
                $copies = max(1, $package->print_count);
            }
            // For 'strip' (default), copies is 1.

            // Here we would trigger the actual print job.
            // Since we don't have a real printer integration shown, we assume marking it as printed is the logic
            // or we might queue a job. For now, we update the DB.

            $activeSession->frame->update([
                'is_printed' => true,
                'printed_at' => now()
            ]);

            // Log activity
            ActivityLog::create([
                'action' => 'frame_printed_kiosk',
                'description' => "Frame dicetak ({$copies} kopi) di photobox {$photobox->code}",
                'photobox_id' => $photobox->id,
                'photo_session_id' => $activeSession->id,
                'metadata' => [
                    'copies' => $copies,
                    'print_type' => $printType
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Mencetak {$copies} kopi...",
                'copies' => $copies
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mencetak: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download all photos from a session
     */
    public function downloadAllPhotos(PhotoSession $session, Request $request)
    {
        // Verify signature and expiry for security if provided
        $expires = $request->get('expires');
        $signature = $request->get('signature');

        // If signature & expires are provided, verify them
        if ($expires && $signature) {
            if ($expires < time()) {
                abort(403, 'Link has expired');
            }

            $expectedSignature = hash_hmac('sha256', $session->id . $expires, config('app.key'));
            if (!hash_equals($expectedSignature, $signature)) {
                abort(403, 'Invalid signature');
            }
        }
        // Public access without signature for direct sharing
        // This allows access directly via session ID

        $photos = $session->photos()->orderBy('sequence_number')->get();

        if ($photos->isEmpty()) {
            abort(404, 'No photos found for this session');
        }

        // For gallery routes, always create and download ZIP directly
        $isGalleryRoute = $request->route()->getName() === 'gallery.download.all';

        if ($isGalleryRoute || $request->has('zip')) {
            // Create a zip file
            $zipFileName = 'fotoku-' . $session->session_code . '-photos.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            // Create directory if not exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Create new zip archive
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                abort(500, 'Could not create zip file');
            }

            // Add files to zip (prefer local-first during deferred upload). Apply watermark for gallery use.
            foreach ($photos as $photo) {
                try {
                    // Prefer local file if still present, else fallback to S3
                    if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                        $photoContent = file_get_contents($photo->local_path);
                    } else {
                        $photoContent = Storage::disk('s3')->get($photo->s3_path);
                    }
                    // Apply watermark
                    [$photoContent, $mime] = $this->applyGalleryWatermark($photoContent, $photo);

                    // Add content directly to ZIP from memory
                    $filename = 'fotoku-' . $session->session_code . '-photo-' . $photo->sequence_number . '.jpg';
                    $zip->addFromString($filename, $photoContent);

                } catch (Exception $e) {
                    // Log error but continue with other photos
                    \Log::error("Error adding photo {$photo->id} to ZIP: " . $e->getMessage());
                }
            }

            $zip->close();

            // Download then delete the zip
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return view('photobox.download-all', compact('session', 'photos'));
    }

    /**
     * Download selected photos from a session
     */
    public function downloadSelectedPhotos(PhotoSession $session, Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'integer|exists:photos,id'
        ]);

        $photoIds = $request->input('photos');
        $photos = $session->photos()->whereIn('id', $photoIds)->orderBy('sequence_number')->get();

        if ($photos->isEmpty()) {
            return response()->json(['error' => 'No valid photos found'], 404);
        }

        // Create a zip file
        $zipFileName = 'fotoku-' . $session->session_code . '-selected-photos.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Create directory if not exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Create new zip archive
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'Could not create zip file'], 500);
        }

        // Add files to zip (prefer local-first during deferred upload). Apply watermark for gallery use.
        foreach ($photos as $photo) {
            try {
                // Prefer local file if still present, else fallback to S3
                if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                    $photoContent = file_get_contents($photo->local_path);
                } else {
                    $photoContent = Storage::disk('s3')->get($photo->s3_path);
                }
                // Apply watermark
                [$photoContent, $mime] = $this->applyGalleryWatermark($photoContent, $photo);

                // Add content directly to ZIP from memory
                $filename = 'fotoku-' . $session->session_code . '-photo-' . $photo->sequence_number . '.jpg';
                $zip->addFromString($filename, $photoContent);

            } catch (Exception $e) {
                // Log error but continue with other photos
                \Log::error("Error adding photo {$photo->id} to ZIP: " . $e->getMessage());
            }
        }

        $zip->close();

        // Download then delete the zip
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Debug photobox issues
     */
    public function debug(Photobox $photobox): View
    {
        $activeSession = $photobox->activePhotoSessions()->with('photos')->first();

        return view('photobox.debug', compact('photobox', 'activeSession'));
    }

    /**
     * Get frame preview for photobox interface
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
     * Get available frame templates for photobox interface
     */
    public function getFrameTemplates(Photobox $photobox): JsonResponse
    {
        try {
            $templates = \App\Models\FrameTemplate::where('status', 'active')
                ->select('id', 'name', 'description', 'slots', 'preview_path', 'background_color', 'is_default', 'is_recommended', 'created_at')
                // Order: default first, then recommended, then newest first
                ->orderByDesc('is_default')
                ->orderByDesc('is_recommended')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($template) {
                    return [
                        'id' => $template->id,
                        'name' => $template->name,
                        'description' => $template->description,
                        'slots' => $template->slots,
                        'preview_url' => $template->preview_url, // This uses the accessor
                        'background_color' => $template->background_color,
                        'is_default' => $template->is_default,
                        'is_recommended' => (bool) ($template->is_recommended),
                        'created_at' => $template->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load frame templates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug method to check current session status
     */
    public function debugStatus(Photobox $photobox): JsonResponse
    {
        $activeSession = $photobox->activePhotoSessions()->first();

        $debugData = [
            'photobox_code' => $photobox->code,
            'photobox_status' => $photobox->status,
            'has_active_session' => !!$activeSession,
            'session_data' => null,
            'photos_count' => 0,
            'total_photos_required' => config('fotoku.total_photos', 3)
        ];

        if ($activeSession) {
            $photosCount = $activeSession->photos()->count();
            $debugData['session_data'] = [
                'id' => $activeSession->id,
                'session_code' => $activeSession->session_code,
                'session_status' => $activeSession->session_status,
                'customer_name' => $activeSession->customer_name,
                'frame_slots' => $activeSession->frame_slots,
                'payment_status' => $activeSession->payment_status,
                'created_at' => $activeSession->created_at,
                'updated_at' => $activeSession->updated_at
            ];
            $debugData['photos_count'] = $photosCount;
        }

        return response()->json($debugData);
    }

    /**
     * Create test session for debugging (only in debug mode)
     */
    public function createTestSession(Photobox $photobox): JsonResponse
    {
        if (!config('app.debug')) {
            return response()->json(['error' => 'Only available in debug mode'], 403);
        }

        try {
            // Create test session
            $session = PhotoSession::create([
                'photobox_id' => $photobox->id,
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'frame_slots' => 6,
                'total_price' => 35000,
                'payment_status' => 'paid',
                'session_status' => 'photo_selection', // Set to photo_selection for testing
                'approved_at' => now(),
                'started_at' => now(),
            ]);

            // Create some test photos
            for ($i = 1; $i <= 10; $i++) {
                Photo::create([
                    'photo_session_id' => $session->id,
                    'filename' => "test_photo_{$i}.jpg",
                    'file_path' => "photos/test_photo_{$i}.jpg",
                    'file_size' => 1024000,
                    'sequence_number' => $i,
                ]);
            }

            \Log::info("Test session created for debugging", [
                'session_id' => $session->id,
                'photobox_code' => $photobox->code
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test session created successfully',
                'session' => $session,
                'photos_count' => 10
            ]);

        } catch (\Exception $e) {
            \Log::error("Failed to create test session", [
                'photobox_code' => $photobox->code,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to create test session: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display user gallery with all photos and frames from a session
     */
    public function userGallery(PhotoSession $session)
    {
        try {
            // Check for expiration (30 days from creation)
            // storage fotonya akan berakhir... akses kesini ditutup
            if ($session->created_at->lt(now()->subDays(30))) {
                return response()->view('errors.gallery-error', [
                    'session' => $session,
                    'error_message' => 'Masa berlaku penyimpanan foto (30 hari) telah berakhir. Akses galeri ini telah ditutup.'
                ], 410);
            }

            // Public access - no authentication check needed
            // Make gallery accessible regardless of status for more reliability
            // Only check if session exists (model binding already ensures this)

            // Get all photos from session
            $photos = $session->photos()->orderBy('sequence_number')->get();

            // Get frame if available - using frame() method (singular) not frames()
            $frame = $session->frame()->whereIn('status', ['ready', 'completed'])->first();

            // Generate QR code data for sharing
            $qrCodeUrl = route('photobox.user-gallery', ['session' => $session->session_code]);

            // Bonus GIF if exists
            $gif = $session->sessionGif()->where('status', 'completed')->first();

            return view('gallery.main', [
                'photoSession' => $session,
                'photos' => $photos,
                'frame' => $frame,
                'qrCodeUrl' => $qrCodeUrl,
                'gif' => $gif,
            ]);

        } catch (\Exception $e) {
            // Log detailed error information
            \Log::error("Error displaying user gallery", [
                'session_code' => $session->session_code ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide friendly error page instead of generic 500
            return response()->view('errors.gallery-error', [
                'session' => $session,
                'error_message' => 'Gallery tidak dapat ditampilkan saat ini.'
            ], 500);
        }
    }
    /**
     * Desktop App: Check for available session (Polling)
     */
    public function checkSession(Request $request): JsonResponse
    {
        // Expect Bearer token or 'token' query param
        $token = $request->bearerToken() ?? $request->query('token');

        if (!$token) {
            return response()->json(['error' => 'Access token required'], 401);
        }

        $accessToken = \App\Models\PhotoboxAccessToken::where('token', $token)->first();

        if (!$accessToken || !$accessToken->isValid()) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $photobox = $accessToken->photobox;

        // Find the earliest approved or in_progress session
        // Fix: Exclude sessions from inactive/completed events to prevent "ghost" sessions
        $session = $photobox->photoSessions()
            ->whereIn('session_status', ['approved', 'in_progress'])
            ->where(function ($query) {
                // Allow manual sessions (no event)
                $query->whereNull('photobooth_event_id')
                    // OR sessions from ACTIVE events only
                    ->orWhereHas('photoboothEvent', function ($q) {
                    $q->where('status', 'active');
                });
            })
            ->orderBy('approved_at', 'asc')
            ->first();

        if (!$session) {
            return response()->json([
                'available' => false,
                'photobox' => [
                    'name' => $photobox->name,
                    'settings' => $photobox->settings
                ]
            ]);
        }

        // Helper to formatting session response with package info
        $formatSession = function ($s) {
            $data = [
                'id' => $s->id,
                'session_code' => $s->session_code,
                'customer_name' => $s->customer_name,
                'frame_slots' => $s->frame_slots,
                'status' => $s->session_status,
                'package' => $s->package ? [
                    'name' => $s->package->name,
                    'print_type' => $s->package->print_type ?? 'strip',
                    'print_count' => $s->package->print_count ?? 1,
                ] : null
            ];

            // Check Event Quota overrides
            if ($s->photoboothEvent) {
                $evt = $s->photoboothEvent;
                if (
                    (!is_null($evt->print_quota) && $evt->prints_used >= $evt->print_quota) ||
                    (is_null($evt->print_quota) || $evt->print_quota == 0)
                ) {
                    if (isset($data['package'])) {
                        $data['package']['print_type'] = 'none';
                    }
                }
            }
            return $data;
        };

        return response()->json([
            'available' => true,
            'session' => $formatSession($session),
            'photobox' => [
                'name' => $photobox->name,
                'settings' => $photobox->settings
            ]
        ]);
    }

    /**
     * Desktop App: Start a session
     */
    public function startDesktopSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_code' => 'required|string'
        ]);

        $token = $request->bearerToken() ?? $request->query('token');
        if (!$token) {
            return response()->json(['error' => 'Access token required'], 401);
        }

        $accessToken = \App\Models\PhotoboxAccessToken::where('token', $token)->first();
        if (!$accessToken || !$accessToken->isValid()) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $photobox = $accessToken->photobox;

        $session = PhotoSession::where('session_code', $request->session_code)
            ->where('photobox_id', $photobox->id)
            ->with(['package', 'photoboothEvent'])
            ->firstOrFail();

        // Allow resuming if already in_progress
        if ($session->session_status === 'in_progress') {
            // Apply logic to override print_type in the returned object
            $s = $session;
            if ($s->photoboothEvent) {
                $evt = $s->photoboothEvent;
                if (
                    (!is_null($evt->print_quota) && $evt->prints_used >= $evt->print_quota) ||
                    (is_null($evt->print_quota) || $evt->print_quota == 0)
                ) {
                    if ($s->package) {
                        $s->package->print_type = 'none';
                    }
                }
            }
            return response()->json(['success' => true, 'session' => $s, 'resumed' => true]);
        }

        if ($session->session_status !== 'approved') {
            return response()->json(['error' => 'Session cannot be started (Status: ' . $session->session_status . ')'], 400);
        }

        $session->update([
            'session_status' => 'in_progress',
            'started_at' => now()
        ]);

        ActivityLog::create([
            'action' => 'session_started_desktop',
            'description' => "Sesi dimulai dari Desktop App (Token: " . substr($token, 0, 8) . "...)",
            'photobox_id' => $photobox->id,
            'photo_session_id' => $session->id
        ]);

        // Logic to override print_type based on event quota
        if ($session->photoboothEvent) {
            $evt = $session->photoboothEvent;
            if (
                (!is_null($evt->print_quota) && $evt->prints_used >= $evt->print_quota) ||
                (is_null($evt->print_quota) || $evt->print_quota == 0)
            ) {
                if ($session->package) {
                    $session->package->print_type = 'none';
                }
            }
        }

        return response()->json([
            'success' => true,
            'session' => $session,
            'photobox' => $photobox
        ]);
    }

    /**
     * Desktop App: Upload a single raw photo
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'session_code' => 'required|string',
            'photo' => 'required|file|image',
            'sequence' => 'required|integer'
        ]);

        $session = PhotoSession::where('session_code', $request->session_code)->firstOrFail();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = "raw_{$session->session_code}_{$request->sequence}.jpg";

            // 1. Store locally (Backup/Cache)
            $localPathRel = $file->storeAs("sessions/{$session->session_code}", $filename, 'public');
            $localPathAbs = Storage::disk('public')->path($localPathRel);

            // 2. Store to S3 (Primary Storage)
            // Use 'photos/' prefix to match purgeAssets logic
            $s3Path = "photos/{$session->session_code}/{$filename}";
            try {
                Storage::disk('s3')->put($s3Path, file_get_contents($file));
            } catch (\Exception $e) {
                \Log::error("Failed to upload photo to S3", ['error' => $e->getMessage()]);
            }

            // Check for existing photo with same sequence and delete it to prevent duplicates
            Photo::where('photo_session_id', $session->id)
                ->where('sequence_number', $request->sequence)
                ->delete();

            // Create Photo record
            $photo = new Photo([
                'photo_session_id' => $session->id,
                'sequence_number' => $request->sequence,
                'filename' => $filename,
                'local_path' => $localPathAbs,
                's3_path' => $s3Path,
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
                'metadata' => ['mime_type' => $file->getMimeType()]
            ]);
            $photo->save();

            return response()->json(['success' => true, 'photo_id' => $photo->id]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Desktop App: Upload the final composite frame
     */
    public function uploadFrame(Request $request): JsonResponse
    {
        $request->validate([
            'session_code' => 'required|string',
            'frame' => 'required|file|image'
        ]);

        $session = PhotoSession::where('session_code', $request->session_code)->firstOrFail();

        if ($request->hasFile('frame')) {
            $file = $request->file('frame');
            $filename = "frame_{$session->session_code}.jpg";

            // 1. Store locally (Backup/Cache)
            $localPathRel = $file->storeAs("sessions/{$session->session_code}", $filename, 'public');
            $localPathAbs = Storage::disk('public')->path($localPathRel);

            // 2. Store to S3 (Primary Storage)
            // Use 'frames/' prefix to match purgeAssets logic
            $s3Path = "frames/{$session->session_code}/{$filename}";
            try {
                Storage::disk('s3')->put($s3Path, file_get_contents($file));
            } catch (\Exception $e) {
                \Log::error("Failed to upload frame to S3", ['error' => $e->getMessage()]);
            }

            // Create or update Frame record
            $frame = $session->frame()->updateOrCreate(
                ['photo_session_id' => $session->id],
                [
                    'filename' => $filename,
                    's3_path' => $s3Path,
                    'local_path' => $localPathAbs,
                    'status' => 'completed'
                ]
            );

            return response()->json(['success' => true, 'frame_url' => asset('storage/' . $localPathRel)]);
        }

        return response()->json(['error' => 'No frame uploaded'], 400);
    }

    /**
     * Desktop App: Upload the generated GIF
     */
    public function uploadGif(Request $request): JsonResponse
    {
        $request->validate([
            'session_code' => 'required|string',
            'gif' => 'required|file|mimes:gif'
        ]);

        $session = PhotoSession::where('session_code', $request->session_code)->firstOrFail();

        if ($request->hasFile('gif')) {
            $file = $request->file('gif');
            $filename = "fotoku-{$session->session_code}-anim.gif";

            // 1. Store locally
            $localPathRel = $file->storeAs("sessions/{$session->session_code}", $filename, 'public');

            // 2. Store to S3
            $s3Path = "gifs/{$session->session_code}/{$filename}";
            try {
                Storage::disk('s3')->put($s3Path, file_get_contents($file));
            } catch (\Exception $e) {
                \Log::error("Failed to upload GIF to S3", ['error' => $e->getMessage()]);
            }

            // Create or update SessionGif record
            $session->sessionGif()->updateOrCreate(
                ['photo_session_id' => $session->id],
                [
                    'filename' => $filename,
                    'path' => $s3Path, // S3 path is primary
                    'local_path' => Storage::disk('public')->path($localPathRel),
                    'file_size' => $file->getSize(),
                    'status' => 'completed',
                    'completed_at' => now()
                ]
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'No GIF uploaded'], 400);
    }

    /**
     * Desktop App: Complete session
     */
    /**
     * Desktop App: Complete session
     */
    public function completeSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_code' => 'required|string',
            'printed_count' => 'nullable|integer|min:0' // Optional: track actual prints if sent by desktop
        ]);

        $session = PhotoSession::where('session_code', $request->session_code)->firstOrFail();

        $session->update([
            'session_status' => 'completed',
            'completed_at' => now()
        ]);

        // Handle Event Mode Logic
        if ($session->photobooth_event_id) {
            $event = $session->photoboothEvent;

            // Determine if a print was likely consumed
            // If desktop sent 'printed_count', use it.
            // Otherwise, assume 1 print if package is not digital-only.
            $printsConsumed = 0;

            // Only count prints if the event actually allows printing (quota > 0)
            // If quota is 0 or null, it's a Digital Only event, so no prints should be counted/allowed.
            if (!is_null($event->print_quota) && $event->print_quota > 0) {
                if ($request->has('printed_count')) {
                    $printsConsumed = $request->printed_count;
                } else {
                    $printType = $session->package->print_type ?? 'strip';
                    if ($printType !== 'none') {
                        $printsConsumed = 1;
                    }
                }
            }

            if ($printsConsumed > 0) {
                $event->increment('prints_used', $printsConsumed);
            }

            // Create NEXT session immediately if event is still active
            if ($event->status === 'active' && ($event->active_until === null || $event->active_until > now())) {
                \App\Models\PhotoSession::create([
                    'photobox_id' => $event->photobox_id,
                    'photobooth_event_id' => $event->id,
                    'package_id' => $event->package_id,
                    'admin_id' => 1, // System admin ID for auto-created sessions
                    'customer_name' => 'Event Guest',
                    'customer_email' => 'guest@event.com',
                    'frame_slots' => $event->package->frame_slots ?? 6,
                    'session_status' => 'approved', // Ready to start
                    'payment_status' => 'paid',
                    'total_price' => 0,
                    'approved_at' => now(),
                ]);
            }
        }

        // Note: GIF generation is now handled by the desktop app and uploaded via uploadGif

        // Generate QR Code URL (link to the user gallery)
        $galleryUrl = route('photobox.user-gallery', ['session' => $session->session_code]);

        return response()->json([
            'success' => true,
            'qr_code_url' => $galleryUrl
        ]);
    }

    /**
     * Desktop App: Get available frame templates
     */
    /**
     * Desktop App: Get available frame templates
     */
    public function getDesktopFrameTemplates(Request $request): JsonResponse
    {
        // Return list of active frame templates
        $templates = \App\Models\FrameTemplate::where('status', 'active')->get();

        // Map to include full URLs for the template images
        $data = $templates->map(function ($t) {
            // Use the API route for serving assets to ensure CORS headers are present
            // The 'template' parameter expects the ID
            $assetUrl = url('api/v1/desktop/frame-assets/' . $t->id);

            return [
                'id' => $t->id,
                'name' => $t->name,
                'image_url' => $assetUrl . '?type=main',
                'preview_url' => $assetUrl . '?type=preview',
                'layout' => $t->slots,
                'config' => $t->layout_config
            ];
        });

        return response()->json(['success' => true, 'templates' => $data]);
    }

    /**
     * Desktop App: Serve frame template asset with CORS
     */
    public function serveFrameTemplateAsset(\App\Models\FrameTemplate $template, Request $request)
    {
        $type = $request->query('type', 'main');

        // Determine which file to serve
        $path = ($type === 'preview' && $template->preview_path)
            ? $template->preview_path
            : $template->template_path;

        if (!$path) {
            abort(404);
        }

        // Handle both local and S3
        if (Storage::disk('public')->exists($path)) {
            $content = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path);
        } elseif (Storage::disk('s3')->exists($path)) {
            $content = Storage::disk('s3')->get($path);
            $mime = Storage::disk('s3')->mimeType($path);
        } else {
            // Try absolute path if stored that way
            if (file_exists(storage_path('app/public/' . $path))) {
                $content = file_get_contents(storage_path('app/public/' . $path));
                $mime = mime_content_type(storage_path('app/public/' . $path));
            } else {
                abort(404, 'File not found');
            }
        }

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get system settings for desktop app
     */
    public function getSettings(): JsonResponse
    {
        try {
            // Retrieve all settings grouped
            $photoSettings = [
                'countdown_seconds' => \App\Models\Setting::get('countdown_duration', 3),
                'photo_interval_seconds' => \App\Models\Setting::get('photo_interval', 3),
                'photo_count' => \App\Models\Setting::get('photo_count', 3),
                'frame_quality' => \App\Models\Setting::get('frame_quality', 300),
            ];

            $paymentSettings = \App\Models\Setting::getGroup('payment');
            $generalSettings = \App\Models\Setting::getGroup('general');

            return response()->json([
                'success' => true,
                'settings' => [
                    'photo' => $photoSettings,
                    'payment' => $paymentSettings,
                    'general' => $generalSettings
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil pengaturan: ' . $e->getMessage()], 500);
        }
    }
}
