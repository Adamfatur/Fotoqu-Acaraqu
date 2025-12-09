<?php

namespace App\Services;

use App\Models\PhotoSession;
use App\Models\ActivityLog;
use App\Models\PaymentLog as ModelsPaymentLog;
use App\Models\Photobox;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhotoSessionService
{
    /**
     * Create a new photo session.
     */
    public function createSession(array $data, User $admin): PhotoSession
    {
        return DB::transaction(function () use ($data, $admin) {
            // Get package details
            $package = Package::findOrFail($data['package_id']);

            // Create user if email is provided
            $user = null;
            if (!empty($data['customer_email'])) {
                $user = User::firstOrCreate(
                    ['email' => $data['customer_email']],
                    ['name' => $data['customer_name'], 'password' => bcrypt(Str::random(16))]
                );
            }

            // Calculate price from package (use discount price if available)
            $price = $package->discount_price ?? $package->price;

            // Create session
            $session = PhotoSession::create([
                'session_code' => PhotoSession::generateSessionCode(),
                'photobox_id' => $data['photobox_id'],
                'user_id' => $user?->id,
                'admin_id' => $admin->id,
                'package_id' => $package->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'frame_slots' => $package->frame_slots,
                'total_price' => $price,
                'notes' => $data['notes'] ?? null,
            ]);

            // Log activity
            ActivityLog::log(
                'session_created',
                "Photo session {$session->session_code} created by {$admin->name}",
                ['session_id' => $session->id, 'price' => $price],
                $session,
                $admin
            );

            return $session;
        });
    }

    /**
     * Process payment for a session.
     */
    public function processPayment(PhotoSession $session, array $paymentData, User $admin): ModelsPaymentLog
    {
        return DB::transaction(function () use ($session, $paymentData, $admin) {
            // Normalize amount to plain number (IDR)
            $rawAmount = $paymentData['amount'] ?? 0;
            $amount = $this->normalizeAmount($rawAmount);

            // Create payment log
            $paymentLog = ModelsPaymentLog::create([
                'photo_session_id' => $session->id,
                'admin_id' => $admin->id,
                'amount' => $amount,
                'payment_method' => $paymentData['payment_method'],
                'status' => 'completed',
                'notes' => $paymentData['notes'] ?? null,
                'metadata' => $paymentData['metadata'] ?? null,
            ]);

            // Update session payment status
            $session->update(['payment_status' => 'paid']);

            // Log activity
            ActivityLog::log(
                'payment_received',
                "Payment of Rp " . number_format($amount) . " received via {$paymentData['payment_method']}",
                ['payment_log_id' => $paymentLog->id],
                $session,
                $admin
            );

            return $paymentLog;
        });
    }

    /**
     * Normalize an IDR currency input into integer/float amount.
     * Accepts formats like "175.000", "Rp 175.000", 175000, etc.
     */
    private function normalizeAmount($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            // Remove currency symbols and thousand separators
            $clean = preg_replace('/[^0-9]/', '', $value);
            if ($clean === '' || $clean === null) {
                return 0.0;
            }
            return (float) $clean;
        }

        return 0.0;
    }

    /**
     * Approve a session for photobox use.
     */
    public function approveSession(PhotoSession $session, User $admin): PhotoSession
    {
        if (!$session->canBeApproved()) {
            throw new \Exception('Session cannot be approved. Check payment status.');
        }

        return DB::transaction(function () use ($session, $admin) {
            $session->update([
                'session_status' => 'approved',
                'approved_at' => now(),
            ]);

            ActivityLog::log(
                'session_approved',
                "Session {$session->session_code} approved for photobox use",
                [],
                $session,
                $admin
            );

            return $session;
        });
    }

    /**
     * Start a photo session.
     */
    public function startSession(PhotoSession $session): PhotoSession
    {
        if (!$session->canBeStarted()) {
            throw new \Exception('Session cannot be started. Check approval status.');
        }

        return DB::transaction(function () use ($session) {
            $session->update([
                'session_status' => 'in_progress',
                'started_at' => now(),
            ]);

            ActivityLog::log(
                'session_started',
                "Photo session {$session->session_code} started",
                [],
                $session
            );

            return $session;
        });
    }

    /**
     * Complete a photo session.
     */
    public function completeSession(PhotoSession $session): PhotoSession
    {
        return DB::transaction(function () use ($session) {
            $session->update([
                'session_status' => 'completed',
                'completed_at' => now(),
            ]);

            ActivityLog::log(
                'session_completed',
                "Photo session {$session->session_code} completed",
                ['total_photos' => $session->photos()->count()],
                $session
            );

            // Create placeholder GIF row if not exists and dispatch background generation
            try {
                if (!$session->sessionGif()->exists()) {
                    \App\Models\SessionGif::create([
                        'photo_session_id' => $session->id,
                        'filename' => 'fotoku-' . $session->session_code . '-anim.gif',
                        'status' => 'processing',
                    ]);
                }
                $gifQueue = config('fotoku.gif.queue', 'media');
                \App\Jobs\GenerateSessionGif::dispatch($session->id)->onQueue($gifQueue);
            } catch (\Throwable $e) {
                \Log::warning('Failed to seed/dispatch GIF generation on completeSession', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $session;
        });
    }

    /**
     * Cancel a photo session.
     */
    public function cancelSession(PhotoSession $session, User $admin, ?string $reason = null): PhotoSession
    {
        return DB::transaction(function () use ($session, $admin, $reason) {
            $session->update(['session_status' => 'cancelled']);

            ActivityLog::log(
                'session_cancelled',
                "Session {$session->session_code} cancelled by {$admin->name}",
                ['reason' => $reason],
                $session,
                $admin
            );

            return $session;
        });
    }

    /**
     * Calculate price based on frame slots.
     */
    public function calculatePrice(int $frameSlots): float
    {
        return match ($frameSlots) {
            4 => config('fotoku.frame_price_4_slots', 25000),
            6 => config('fotoku.frame_price_6_slots', 35000),
            8 => config('fotoku.frame_price_8_slots', 45000),
            default => throw new \Exception("Invalid frame slots: {$frameSlots}"),
        };
    }

    /**
     * Get available photoboxes.
     */
    public function getAvailablePhotoboxes()
    {
        // Allow selecting active photoboxes even if they already have approved/in_progress sessions (queueing)
        return Photobox::where('status', 'active')->get();
    }

    /**
     * Get session statistics for dashboard.
     */
    public function getDashboardStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        // Revenue should be based on completed payments only (exclude free)
        $totalRevenue = PhotoSession::where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $todayPaidRevenue = PhotoSession::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Today free/paid session counts
        $todayFreeSessions = PhotoSession::whereDate('created_at', today())
            ->whereHas('paymentLogs', function ($q) {
                $q->where('status', 'completed')->where('payment_method', 'free');
            })->count();

        $todayPaidSessions = PhotoSession::whereDate('created_at', today())
            ->whereHas('paymentLogs', function ($q) {
                $q->where('status', 'completed')->where('payment_method', '!=', 'free');
            })->count();

        return [
            'total_sessions' => PhotoSession::where('created_at', '>=', $startDate)->count(),
            'completed_sessions' => PhotoSession::where('created_at', '>=', $startDate)
                ->where('session_status', 'completed')->count(),
            'total_revenue' => $totalRevenue,
            'active_sessions' => PhotoSession::whereIn('session_status', ['approved', 'in_progress'])->count(),
            'today_sessions' => PhotoSession::whereDate('created_at', today())->count(),
            'today_revenue' => $todayPaidRevenue,
            'today_free_sessions' => $todayFreeSessions,
            'today_paid_sessions' => $todayPaidSessions,
            'active_photoboxes' => Photobox::where('status', 'active')
                ->whereHas('photoSessions', function ($query) {
                    $query->whereIn('session_status', ['approved', 'in_progress']);
                })->count(),
            'completion_rate' => $this->calculateCompletionRate($days),
            'pending_approvals' => PhotoSession::where('session_status', 'pending')->count(),
            'avg_daily_revenue' => PhotoSession::where('created_at', '>=', now()->subDays(7))
                ->where('payment_status', 'paid')->avg('total_price') ?? 0,
            'highest_daily_revenue' => PhotoSession::where('created_at', '>=', now()->subDays(7))
                ->where('payment_status', 'paid')->max('total_price') ?? 0,
            'revenue_growth' => $this->calculateRevenueGrowth(),
            'sessions_growth' => $this->calculateSessionsGrowth(),
        ];
    }

    /**
     * Calculate completion rate for given period
     */
    private function calculateCompletionRate(int $days): float
    {
        $startDate = now()->subDays($days);
        $total = PhotoSession::where('created_at', '>=', $startDate)->count();
        $completed = PhotoSession::where('created_at', '>=', $startDate)
            ->where('session_status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    /**
     * Calculate revenue growth rate
     */
    private function calculateRevenueGrowth(): float
    {
        $thisWeek = PhotoSession::where('created_at', '>=', now()->startOfWeek())
            ->where('payment_status', 'paid')->sum('total_price');
        $lastWeek = PhotoSession::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->where('payment_status', 'paid')->sum('total_price');

        if ($lastWeek > 0) {
            return round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1);
        }

        return 0;
    }

    /**
     * Calculate sessions growth rate
     */
    private function calculateSessionsGrowth(): float
    {
        $today = PhotoSession::whereDate('created_at', today())->count();
        $yesterday = PhotoSession::whereDate('created_at', today()->subDay())->count();

        if ($yesterday > 0) {
            return round((($today - $yesterday) / $yesterday) * 100, 1);
        }

        return $today > 0 ? 100 : 0; // If no sessions yesterday but have today, 100% growth
    }
}
