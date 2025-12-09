<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\Photobox;
use App\Models\ActivityLog;
use App\Services\PhotoSessionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected PhotoSessionService $photoSessionService;

    public function __construct(PhotoSessionService $photoSessionService)
    {
        $this->photoSessionService = $photoSessionService;
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = $this->photoSessionService->getDashboardStats(30);

        // Get recent sessions
        $recentSessions = PhotoSession::with(['photobox', 'user', 'admin'])
            ->latest()
            ->take(5)
            ->get();

        // Get active photoboxes
        $activePhotoboxes = Photobox::with(['activePhotoSessions.photos', 'photoSessions'])->where('status', 'active')->get();

        // Add today's session count to each photobox
        $activePhotoboxes->each(function ($photobox) {
            $photobox->today_sessions_count = $photobox->photoSessions()
                ->whereDate('created_at', today())
                ->count();
        });

        // Get recent activities
        $recentActivities = ActivityLog::with(['user', 'photoSession'])
            ->latest()
            ->take(10)
            ->get();

        // Get revenue chart data (last 7 days)
        $revenueChart = $this->getRevenueChartData();

        // Get session status distribution
        $sessionStatusChart = $this->getSessionStatusChart();

        // Get active sessions for emergency panel
        $activeSessions = PhotoSession::with(['photobox', 'user', 'photos'])
            ->whereIn('session_status', ['approved', 'in_progress', 'capturing'])
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentSessions',
            'activePhotoboxes',
            'recentActivities',
            'revenueChart',
            'sessionStatusChart',
            'activeSessions'
        ));
    }

    /**
     * Get real-time dashboard statistics for AJAX updates
     */
    public function getStats()
    {
        $stats = $this->photoSessionService->getDashboardStats(30);

        return response()->json([
            'today' => [
                'sessions' => PhotoSession::whereDate('created_at', today())->count(),
                'today_free_sessions' => PhotoSession::whereDate('created_at', today())
                    ->whereHas('paymentLogs', function ($q) {
                        $q->where('status', 'completed')->where('payment_method', 'free');
                    })
                    ->count(),
                'today_paid_sessions' => PhotoSession::whereDate('created_at', today())
                    ->whereHas('paymentLogs', function ($q) {
                        $q->where('status', 'completed')->where('payment_method', '!=', 'free');
                    })
                    ->count(),
                'today_revenue' => PhotoSession::whereDate('created_at', today())
                    ->where('payment_status', 'paid')->sum('total_price'),
                'activeBoxes' => Photobox::where('status', 'active')
                    ->whereHas('photoSessions', function ($query) {
                        $query->whereIn('session_status', ['approved', 'in_progress']);
                    })->count(),
                'completionRate' => $this->calculateCompletionRate()
            ],
            'charts' => [
                'avgDaily' => PhotoSession::where('created_at', '>=', now()->subDays(7))
                    ->where('payment_status', 'paid')
                    ->avg('total_price') ?? 0,
                'highest' => PhotoSession::where('created_at', '>=', now()->subDays(7))
                    ->where('payment_status', 'paid')
                    ->max('total_price') ?? 0,
                'growth' => $this->calculateGrowthRate()
            ],
            'pending_approvals' => PhotoSession::where('session_status', 'pending')->count(),
            'total_sessions' => $stats['total_sessions'],
            'completed_sessions' => $stats['completed_sessions'],
            'total_revenue' => $stats['total_revenue'],
            'active_sessions' => $stats['active_sessions']
        ]);
    }

    /**
     * Get recent sessions HTML for AJAX refresh
     */
    public function getRecentSessions()
    {
        $recentSessions = PhotoSession::with(['photobox', 'user', 'admin', 'photos'])
            ->latest()
            ->take(5)
            ->get();

        $html = '';
        foreach ($recentSessions as $session) {
            $statusClass = match ($session->session_status) {
                'completed' => 'bg-teal-100 text-teal-800',
                'in_progress' => 'bg-sky-100 text-sky-800',
                'approved' => 'bg-indigo-100 text-indigo-800',
                default => 'bg-amber-100 text-amber-800'
            };

            $progressBar = '';
            if ($session->session_status === 'in_progress') {
                $photosCount = $session->photos->count();
                $progressPercentage = min(($photosCount / 10) * 100, 100);
                $progressBar = "
                    <div class=\"mt-3 pt-3 border-t border-gray-200\">
                        <div class=\"flex items-center justify-between text-xs text-gray-600 mb-1\">
                            <span>Progress</span>
                            <span>{$photosCount}/10 foto</span>
                        </div>
                        <div class=\"w-full bg-gray-200 rounded-full h-2\">
                            <div class=\"bg-gradient-to-r from-indigo-500 to-teal-400 h-2 rounded-full transition-all duration-1000\" 
                                 style=\"width: {$progressPercentage}%\"></div>
                        </div>
                    </div>
                ";
            }

            $html .= "
                <div class=\"group p-4 bg-gray-50 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-teal-50 rounded-2xl border border-gray-200 hover:border-indigo-200 transition-all duration-300 cursor-pointer hover-lift\"
                     onclick=\"showSessionDetail('{$session->id}')\">
                    <div class=\"flex items-center justify-between\">
                        <div class=\"flex items-center space-x-3\">
                            <div class=\"w-12 h-12 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center text-indigo-50 font-bold shadow-lg\">
                                " . substr($session->customer_name, 0, 1) . "
                            </div>
                            <div>
                                <p class=\"font-semibold text-gray-800 group-hover:text-indigo-800 transition-colors\">{$session->customer_name}</p>
                                <div class=\"flex items-center space-x-2 text-sm text-gray-600\">
                                    <span>{$session->session_code}</span>
                                    <span>•</span>
                                    <span>{$session->frame_slots} slot</span>
                                </div>
                            </div>
                        </div>
                        <div class=\"text-right\">
                            <span class=\"inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {$statusClass}\">
                                <i class=\"fas fa-circle text-xs mr-1\"></i>
                                " . ucfirst(str_replace('_', ' ', $session->session_status)) . "
                            </span>
                            <p class=\"text-xs text-gray-500 mt-1\">{$session->created_at->diffForHumans()}</p>
                            <p class=\"text-sm font-semibold text-indigo-700\">Rp " . number_format($session->total_price, 0, ',', '.') . "</p>
                        </div>
                    </div>
                    {$progressBar}
                </div>
            ";
        }

        if (empty($html)) {
            $html = "
                <div class=\"text-center py-12\">
                    <div class=\"w-20 h-20 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4\">
                        <i class=\"fas fa-camera text-indigo-100 text-3xl\"></i>
                    </div>
                    <p class=\"text-gray-700 font-semibold\">Belum ada sesi foto</p>
                    <p class=\"text-gray-500 text-sm mt-1\">Sesi foto baru akan muncul di sini.</p>
                    <a href=\"" . route('admin.sessions.create') . "\" 
                       class=\"inline-flex items-center mt-4 px-6 py-3 bg-gradient-to-r from-indigo-700 to-indigo-500 text-indigo-50 rounded-xl hover:shadow-lg transition-all font-semibold\">
                        <i class=\"fas fa-plus mr-2\"></i>
                        Buat Sesi Baru
                    </a>
                </div>
            ";
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get photoboxes status HTML for AJAX refresh
     */
    public function getPhotoboxes()
    {
        $activePhotoboxes = Photobox::with(['activePhotoSessions.photos'])->where('status', 'active')->get();

        $html = '';
        foreach ($activePhotoboxes as $photobox) {
            $isAvailable = $photobox->isAvailable();
            $statusClass = $isAvailable ? 'bg-teal-500 animate-pulse' : 'bg-amber-400';
            $statusText = $isAvailable ? 'Available' : 'In Use';
            $statusTextClass = $isAvailable ? 'text-teal-700' : 'text-amber-600';

            $progressSection = '';
            $currentSession = $photobox->activePhotoSessions->first();
            if ($currentSession) {
                $photosCount = $currentSession->photos->count();
                $progressPercentage = min(($photosCount / 10) * 100, 100);
                $progressSection = "
                    <div class=\"mt-3 pt-3 border-t border-gray-200\">
                        <div class=\"flex items-center justify-between text-xs text-gray-600 mb-1\">
                            <span>Session Progress</span>
                            <span>{$photosCount}/10</span>
                        </div>
                        <div class=\"w-full bg-gray-200 rounded-full h-1.5\">
                            <div class=\"bg-gradient-to-r from-indigo-500 to-teal-400 h-1.5 rounded-full transition-all\" 
                                 style=\"width: {$progressPercentage}%\"></div>
                        </div>
                    </div>
                ";
            }

            $actionButtons = '';
            if ($isAvailable) {
                $actionButtons = "<button onclick=\"event.stopPropagation(); startTestSession('{$photobox->id}')\" class=\"px-3 py-1 bg-teal-600 text-teal-50 rounded-lg text-xs hover:bg-teal-700 transition-colors font-medium\"><i class=\"fas fa-play mr-1\"></i>Test</button>";
            } else {
                $actionButtons = "<button onclick=\"event.stopPropagation(); forceStop('{$photobox->id}')\" class=\"px-3 py-1 bg-rose-600 text-rose-50 rounded-lg text-xs hover:bg-rose-700 transition-colors font-medium\"><i class=\"fas fa-stop mr-1\"></i>Stop</button>";
                if ($currentSession && ($currentSession->session_status === 'error' || $currentSession->created_at->diffInMinutes() > 30)) {
                    $actionButtons .= " <button onclick=\"event.stopPropagation(); emergencyReset('{$photobox->id}')\" class=\"px-3 py-1 bg-amber-600 text-amber-50 rounded-lg text-xs hover:bg-amber-700 transition-colors animate-pulse font-medium ml-1\"><i class=\"fas fa-exclamation-triangle mr-1\"></i>Reset</button>";
                }
            }

            $todaySessions = $photobox->photoSessions()->whereDate('created_at', today())->count();

            $sessionActiveText = '';
            if ($currentSession) {
                $sessionActiveText = '<span class="text-xs text-gray-700 font-medium">Session Active</span>';
            }

            $html .= "
                <div class=\"p-6 border border-gray-200 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer bg-gray-50 hover:bg-white group\"
                     onclick=\"showPhotoboxDetail('{$photobox->id}')\">
                    <div class=\"flex items-center justify-between mb-4\">
                        <div class=\"flex items-center space-x-3\">
                            <div class=\"w-12 h-12 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform\">
                                <span class=\"text-gray-100 text-sm font-bold\">" . substr($photobox->code, -2) . "</span>
                            </div>
                            <div>
                                <span class=\"font-semibold text-gray-800 group-hover:text-indigo-800 transition-colors\">{$photobox->code}</span>
                                <p class=\"text-xs text-gray-500\">{$photobox->name}</p>
                            </div>
                        </div>
                        <div class=\"flex flex-col items-end space-y-1\">
                            <div class=\"flex items-center space-x-2\">
                                <div class=\"w-2 h-2 {$statusClass} rounded-full\"></div>
                                <span class=\"text-xs {$statusTextClass} font-semibold\">{$statusText}</span>
                            </div>
                            {$sessionActiveText}
                        </div>
                    </div>
                    <div class=\"space-y-2\">
                        <div class=\"flex items-center justify-between text-sm\">
                            <span class=\"text-gray-600\">Lokasi:</span>
                            <span class=\"font-medium text-gray-800\">{$photobox->location}</span>
                        </div>
                        <div class=\"flex items-center justify-between text-sm\">
                            <span class=\"text-gray-600\">Sesi Hari Ini:</span>
                            <span class=\"font-semibold text-indigo-700\">{$todaySessions}</span>
                        </div>
                        {$progressSection}
                    </div>
                    
                    <div class=\"mt-4 pt-3 border-t border-gray-200 transition-opacity duration-300\">
                        <div class=\"flex items-center justify-between\">
                            <div class=\"flex space-x-2\">
                                {$actionButtons}
                            </div>
                            <span class=\"text-xs text-gray-500\">
                                {$photobox->updated_at->diffForHumans()}
                            </span>
                        </div>
                    </div>
                </div>
            ";
        }

        if (empty($html)) {
            $html = "
                <div class=\"col-span-full text-center py-12\">
                    <div class=\"w-20 h-20 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4\">
                        <i class=\"fas fa-box text-indigo-100 text-3xl\"></i>
                    </div>
                    <p class=\"text-gray-700 font-semibold\">Tidak ada photobox aktif</p>
                    <p class=\"text-gray-500 text-sm mt-1\">Setup photobox untuk mulai menggunakan sistem.</p>
                    <button class=\"inline-flex items-center mt-4 px-6 py-3 bg-gradient-to-r from-indigo-700 to-indigo-500 text-indigo-50 rounded-xl hover:shadow-lg transition-all font-semibold\">
                        <i class=\"fas fa-plus mr-2\"></i>
                        Setup Photobox
                    </button>
                </div>
            ";
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get recent activities HTML for AJAX refresh
     */
    public function getActivities()
    {
        $recentActivities = ActivityLog::with(['user', 'photoSession'])
            ->latest()
            ->take(10)
            ->get();

        $html = '';
        foreach ($recentActivities as $activity) {
            $html .= "
                <div class=\"flex items-start space-x-3 p-3 hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 rounded-lg transition-all duration-300 group\">
                    <div class=\"w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform\">
                        <i class=\"fas fa-bolt text-white text-xs\"></i>
                    </div>
                    <div class=\"flex-1 min-w-0\">
                        <p class=\"text-sm font-medium text-gray-800 group-hover:text-blue-700 transition-colors\">{$activity->description}</p>
                        <div class=\"flex items-center mt-1 space-x-2\">
                            " . ($activity->user ? "<span class=\"text-xs text-gray-600\">oleh {$activity->user->name}</span>" : "") . "
                            <span class=\"text-xs text-gray-500\">{$activity->created_at->diffForHumans()}</span>
                        </div>
                    </div>
                    <div class=\"text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity\">
                        <i class=\"fas fa-external-link-alt\"></i>
                    </div>
                </div>
            ";
        }

        if (empty($html)) {
            $html = "
                <div class=\"text-center py-12\">
                    <div class=\"w-16 h-16 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4\">
                        <i class=\"fas fa-history text-gray-400 text-2xl\"></i>
                    </div>
                    <p class=\"text-gray-600 font-medium\">Belum ada aktivitas</p>
                    <p class=\"text-gray-500 text-sm mt-1\">Aktivitas sistem akan muncul di sini</p>
                </div>
            ";
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Search sessions, customers, and photoboxes
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if (strlen($query) >= 3) {
            // Search sessions
            $sessions = PhotoSession::where('customer_name', 'LIKE', "%{$query}%")
                ->orWhere('session_code', 'LIKE', "%{$query}%")
                ->orWhere('customer_email', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();

            foreach ($sessions as $session) {
                $results[] = [
                    'title' => $session->customer_name,
                    'subtitle' => $session->session_code . ' • ' . ucfirst($session->session_status),
                    'url' => route('admin.sessions.show', $session),
                    'icon' => 'fa-camera'
                ];
            }

            // Search photoboxes
            $photoboxes = Photobox::where('code', 'LIKE', "%{$query}%")
                ->orWhere('name', 'LIKE', "%{$query}%")
                ->orWhere('location', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();

            foreach ($photoboxes as $photobox) {
                $results[] = [
                    'title' => $photobox->code,
                    'subtitle' => $photobox->name . ' • ' . $photobox->location,
                    'url' => '#',
                    'icon' => 'fa-box'
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Test photobox functionality
     */
    public function testPhotobox(Photobox $photobox)
    {
        // Logic to start test session
        return response()->json(['success' => true, 'message' => 'Test session dimulai']);
    }

    /**
     * Force stop photobox session
     */
    public function forceStop(Photobox $photobox)
    {
        $activeSession = $photobox->activePhotoSessions()->first();
        if ($activeSession) {
            $activeSession->update(['session_status' => 'cancelled']);

            // Log the activity
            ActivityLog::create([
                'action' => 'force_stop_session',
                'description' => "Session {$activeSession->id} was force stopped on photobox {$photobox->code}",
                'metadata' => [
                    'session_id' => $activeSession->id,
                    'photobox_id' => $photobox->id,
                    'photobox_code' => $photobox->code
                ]
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Sesi berhasil dihentikan']);
    }

    /**
     * Export daily/weekly/monthly reports
     */
    public function exportReport()
    {
        // Logic to generate and download report
        return response()->json(['success' => true, 'message' => 'Laporan sedang dipersiapkan']);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        // Logic to mark notification as read
        return response()->json(['success' => true]);
    }

    /**
     * Get revenue chart data for AJAX updates
     */
    public function getRevenueChart(Request $request)
    {
        $period = $request->input('period', 'week');
        $days = match ($period) {
            'month' => 30,
            'year', '90days' => 90, // Changed 1 year to 90 days as requested, keeping legacy 'year' for safety
            default => 7
        };

        $chartData = $this->getRevenueChartData($days);

        // Calculate summary stats for the selected period
        $summary = [
            'avgDaily' => collect($chartData)->avg('revenue'),
            'highest' => collect($chartData)->max('revenue'),
            'growth' => $this->calculateGrowthRate($days)
        ];

        return response()->json([
            'chart' => $chartData,
            'summary' => $summary
        ]);
    }

    private function getRevenueChartData($days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = PhotoSession::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_price');

            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue
            ];
        }
        return $data;
    }

    private function getSessionStatusChart()
    {
        return PhotoSession::selectRaw('session_status, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('session_status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->session_status => $item->count];
            });
    }

    /**
     * Get emergency data for real-time monitoring
     */
    public function getEmergencyData()
    {
        $activeSessions = PhotoSession::with(['photobox', 'user', 'photos'])
            ->whereIn('session_status', ['approved', 'in_progress', 'capturing'])
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'customer_name' => $session->customer_name,
                    'session_code' => $session->session_code,
                    'session_status' => $session->session_status,
                    'photobox_id' => $session->photobox_id,
                    'photobox' => [
                        'code' => $session->photobox->code ?? 'N/A'
                    ],
                    'photos_count' => $session->photos->count(),
                    'created_at' => $session->created_at,
                    'updated_at' => $session->updated_at,
                    'duration_minutes' => $session->created_at->diffInMinutes(),
                    'is_stuck' => $session->created_at->diffInMinutes() > 30
                ];
            });

        return response()->json([
            'activeSessions' => $activeSessions,
            'totalActive' => $activeSessions->count(),
            'stuckSessions' => $activeSessions->where('is_stuck', true)->count(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Emergency: Stop all active sessions
     */
    public function stopAllSessions()
    {
        try {
            $activeSessions = PhotoSession::whereIn('session_status', ['approved', 'in_progress', 'capturing', 'photo_selection'])
                ->get();

            $stoppedCount = 0;
            foreach ($activeSessions as $session) {
                $session->update([
                    'session_status' => 'cancelled',
                    'completed_at' => now()
                ]);

                // Log emergency action
                ActivityLog::create([
                    'action' => 'emergency_stop',
                    'description' => "Emergency stop session {$session->session_code}",
                    'photobox_id' => $session->photobox_id,
                    'photo_session_id' => $session->id,
                    'user_id' => auth()->id(),
                    'metadata' => [
                        'reason' => 'Emergency stop all sessions',
                        'admin_user' => auth()->user()->name
                    ]
                ]);

                $stoppedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully stopped {$stoppedCount} active sessions",
                'stopped_count' => $stoppedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Emergency stop all sessions failed', [
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Emergency: System reset all photoboxes
     */
    public function systemReset()
    {
        try {
            // Stop all active sessions first
            $activeSessions = PhotoSession::whereIn('session_status', ['approved', 'in_progress', 'capturing', 'photo_selection'])
                ->get();

            foreach ($activeSessions as $session) {
                $session->update([
                    'session_status' => 'cancelled',
                    'completed_at' => now()
                ]);
            }

            // Reset all photoboxes to inactive status
            $photoboxes = Photobox::where('status', 'active')->get();
            $resetCount = 0;

            foreach ($photoboxes as $photobox) {
                $photobox->update(['status' => 'inactive']);

                // Log emergency action
                ActivityLog::create([
                    'action' => 'emergency_system_reset',
                    'description' => "Emergency system reset - photobox {$photobox->code} reset to standby",
                    'photobox_id' => $photobox->id,
                    'user_id' => auth()->id(),
                    'metadata' => [
                        'reason' => 'System emergency reset',
                        'admin_user' => auth()->user()->name,
                        'active_sessions_stopped' => $activeSessions->where('photobox_id', $photobox->id)->count()
                    ]
                ]);

                $resetCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "System reset completed. {$activeSessions->count()} sessions stopped, {$resetCount} photoboxes reset to standby",
                'sessions_stopped' => $activeSessions->count(),
                'photoboxes_reset' => $resetCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Emergency system reset failed', [
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'System reset failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Emergency: Reset specific photobox
     */
    public function emergencyReset(Photobox $photobox)
    {
        try {
            // Stop any active sessions on this photobox
            $activeSessions = $photobox->photoSessions()
                ->whereIn('session_status', ['approved', 'in_progress', 'capturing', 'photo_selection'])
                ->get();

            foreach ($activeSessions as $session) {
                $session->update([
                    'session_status' => 'cancelled',
                    'completed_at' => now()
                ]);
            }

            // Reset photobox status
            $photobox->update(['status' => 'inactive']);

            // Log emergency action
            ActivityLog::create([
                'action' => 'emergency_photobox_reset',
                'description' => "Emergency reset photobox {$photobox->code}",
                'photobox_id' => $photobox->id,
                'user_id' => auth()->id(),
                'metadata' => [
                    'reason' => 'Emergency photobox reset',
                    'admin_user' => auth()->user()->name,
                    'sessions_stopped' => $activeSessions->count()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Photobox {$photobox->code} reset successfully. {$activeSessions->count()} sessions stopped",
                'sessions_stopped' => $activeSessions->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Emergency photobox reset failed', [
                'photobox_id' => $photobox->id,
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Photobox reset failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Emergency: Force stop individual session
     */
    public function forceStopSession(PhotoSession $session)
    {
        try {
            $session->update([
                'session_status' => 'cancelled',
                'completed_at' => now()
            ]);

            // Log emergency action
            ActivityLog::create([
                'action' => 'emergency_force_stop',
                'description' => "Emergency force stop session {$session->session_code}",
                'photobox_id' => $session->photobox_id,
                'photo_session_id' => $session->id,
                'user_id' => auth()->id(),
                'metadata' => [
                    'reason' => 'Emergency force stop individual session',
                    'admin_user' => auth()->user()->name,
                    'customer_name' => $session->customer_name
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Session {$session->session_code} berhasil dihentikan"
            ]);

        } catch (\Exception $e) {
            \Log::error('Emergency force stop session failed', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghentikan sesi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateCompletionRate()
    {
        $total = PhotoSession::whereDate('created_at', today())->count();
        $completed = PhotoSession::whereDate('created_at', today())->where('session_status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function calculateGrowthRate($days = 7)
    {
        $currentPeriodStart = now()->subDays($days);
        $previousPeriodStart = now()->subDays($days * 2);

        $currentRevenue = PhotoSession::where('created_at', '>=', $currentPeriodStart)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $previousRevenue = PhotoSession::whereBetween('created_at', [$previousPeriodStart, $currentPeriodStart])
            ->where('payment_status', 'paid')
            ->sum('total_price');

        if ($previousRevenue > 0) {
            return round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1);
        }

        return $currentRevenue > 0 ? 100 : 0;
    }
}
