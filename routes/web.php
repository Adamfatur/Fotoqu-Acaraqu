<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PhotoSessionController;
use App\Http\Controllers\Admin\PhotoboxController as AdminPhotoboxController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\FrameTemplateController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\PhotoboxController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('landing');
})->name('landing');

// User gallery route - public access via session code
Route::get('gallery/{session:session_code}', [PhotoboxController::class, 'userGallery'])->name('photobox.user-gallery');
// Secure photo serve for gallery
Route::get('gallery/{session:session_code}/photo/{photo}', [PhotoboxController::class, 'servePhotoForSession'])->name('photobox.gallery.serve-photo');
// Secure GIF serve for gallery
Route::get('gallery/{session:session_code}/gif/{gif}', [PhotoboxController::class, 'serveGifForSession'])->name('photobox.gallery.serve-gif');

// Public serve gif route
Route::get('/photobox/gif/{gif}', [PhotoboxController::class, 'serveGif'])->name('public.serve-gif');

// Public serve photo route
Route::get('/photobox/photo/{photo}', [PhotoboxController::class, 'servePhoto'])->name('photobox.serve-photo');

// Public serve frame route
Route::get('/photobox/frame/{frame}', [PhotoboxController::class, 'serveFrame'])->name('photobox.serve-frame');
Route::get('/photobox/frame/{frame}/download', [PhotoboxController::class, 'downloadFrame'])->name('photobox.download-frame');

// Photobox Interface (Kiosk View)
Route::get('/photobox/{photobox:code}', [PhotoboxController::class, 'show'])->name('photobox.show');
Route::post('/photobox/{photobox:code}/print-frame', [PhotoboxController::class, 'printFrame'])->name('photobox.print-frame');

// Gallery download routes - public access
Route::get('/gallery/{session:session_code}/download-all', [PhotoboxController::class, 'downloadAllPhotos'])->name('gallery.download.all');
Route::post('/gallery/{session:session_code}/download-selected', [PhotoboxController::class, 'downloadSelectedPhotos'])->name('gallery.download.selected');

// Admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard AJAX endpoints
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/revenue-chart', [DashboardController::class, 'getRevenueChart'])->name('dashboard.revenue-chart');
    Route::get('/dashboard/recent-sessions', [DashboardController::class, 'getRecentSessions'])->name('dashboard.recent-sessions');
    Route::get('/dashboard/photoboxes', [DashboardController::class, 'getPhotoboxes'])->name('dashboard.photoboxes');
    Route::get('/dashboard/activities', [DashboardController::class, 'getActivities'])->name('dashboard.activities');
    Route::get('/dashboard/emergency-data', [DashboardController::class, 'getEmergencyData'])->name('dashboard.emergency-data');

    // Search endpoint
    Route::post('/search', [DashboardController::class, 'search'])->name('search');

    // Photo Sessions
    Route::resource('sessions', PhotoSessionController::class);
    // Handle numeric ID access for legacy/JS calls
    Route::get('sessions/{id}/detail', function ($id) {
        $session = \App\Models\PhotoSession::find($id);
        if (!$session)
            abort(404);
        return app(PhotoSessionController::class)->detail($session);
    })->where('id', '[0-9]+')->name('sessions.detail.by-id');

    Route::get('sessions/{session}/detail', [PhotoSessionController::class, 'detail'])->name('sessions.detail');
    Route::get('sessions/{session}/frame-preview', [PhotoSessionController::class, 'getFramePreview'])->name('sessions.frame-preview');
    Route::post('sessions/{session}/payment', [PhotoSessionController::class, 'processPayment'])->name('sessions.payment');
    Route::post('sessions/{session}/approve', [PhotoSessionController::class, 'approve'])->name('sessions.approve');
    Route::post('sessions/{session}/cancel', [PhotoSessionController::class, 'cancel'])->name('sessions.cancel');
    Route::post('sessions/{session}/simulate', [PhotoSessionController::class, 'simulate'])->name('sessions.simulate');
    Route::post('sessions/{session}/select-photos', [PhotoSessionController::class, 'selectPhotos'])->name('sessions.select-photos');
    Route::post('sessions/{session}/create-frame', [PhotoSessionController::class, 'createFrame'])->name('sessions.create-frame');
    Route::post('sessions/{session}/resend-email', [PhotoSessionController::class, 'resendEmail'])->name('sessions.resend-email');
    Route::post('sessions/{session}/retry-processing', [PhotoSessionController::class, 'retryProcessing'])->name('sessions.retry-processing');
    Route::match(['get', 'post'], 'sessions/{session}/print', [PhotoSessionController::class, 'print'])->name('sessions.print');
    Route::post('sessions/{session}/purge-assets', [PhotoSessionController::class, 'purgeAssets'])->name('sessions.purge-assets');
    Route::get('sessions/{session}/gif-progress', [PhotoSessionController::class, 'getGifProgress'])->name('sessions.gif-progress');

    // Photobox management
    Route::resource('photoboxes', AdminPhotoboxController::class);
    Route::post('photoboxes/{photobox}/toggle-status', [AdminPhotoboxController::class, 'toggleStatus'])->name('photoboxes.toggle-status');
    Route::post('photoboxes/{photobox}/generate-token', [AdminPhotoboxController::class, 'generateAccessToken'])->name('photoboxes.generate-token');
    Route::post('photoboxes/{photobox}/revoke-token', [AdminPhotoboxController::class, 'revokeAccessToken'])->name('photoboxes.revoke-token');
    Route::post('photoboxes/{photobox}/test', [DashboardController::class, 'testPhotobox'])->name('photoboxes.test');
    Route::post('photoboxes/{photobox}/force-stop', [DashboardController::class, 'forceStop'])->name('photoboxes.force-stop');

    // Emergency photobox actions
    Route::post('photoboxes/{photobox}/emergency-reset', [DashboardController::class, 'emergencyReset'])->name('photoboxes.emergency-reset');
    Route::post('photoboxes/{photobox}/force-complete', [DashboardController::class, 'forceComplete'])->name('photoboxes.force-complete');
    Route::post('photoboxes/{photobox}/reset-waiting', [DashboardController::class, 'resetWaiting'])->name('photoboxes.reset-waiting');
    Route::delete('photoboxes/{photobox}/delete-session', [DashboardController::class, 'deleteSession'])->name('photoboxes.delete-session');

    // Emergency system actions
    Route::post('emergency/stop-all-sessions', [DashboardController::class, 'stopAllSessions'])->name('emergency.stop-all');
    Route::post('emergency/system-reset', [DashboardController::class, 'systemReset'])->name('emergency.system-reset');
    Route::post('photo-sessions/{session}/force-stop', [DashboardController::class, 'forceStopSession'])->name('emergency.force-stop-session');

    // Package management
    Route::resource('packages', PackageController::class);
    Route::post('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

    // Event Mode Management
    Route::resource('events', \App\Http\Controllers\Admin\PhotoboothEventController::class);
    Route::post('events/{event}/stop', [\App\Http\Controllers\Admin\PhotoboothEventController::class, 'stop'])->name('events.stop');
    Route::post('events/{event}/create-session', [\App\Http\Controllers\Admin\PhotoboothEventController::class, 'createSession'])->name('events.create-session');

    // Frame Templates management
    Route::resource('frame-templates', FrameTemplateController::class);
    Route::post('frame-templates/{frame_template}/toggle-status', [FrameTemplateController::class, 'toggleStatus'])->name('frame-templates.toggle-status');
    Route::post('frame-templates/{frame_template}/set-default', [FrameTemplateController::class, 'setDefault'])->name('frame-templates.set-default');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Notifications (Keep only API endpoints for navbar)
    Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('notifications', [NotificationController::class, 'getNotifications'])->name('notifications.index');
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Profile Management
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/generate-password', [AdminProfileController::class, 'generatePassword'])->name('profile.generate-password');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');

    // Download App
    Route::get('/app-download', function () {
        return view('admin.download.index');
    })->name('download');

    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy'
        ]);
        Route::patch('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::patch('users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
    });

    // Customer Management (Admin & Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export');
        Route::get('customers-emails', [CustomerController::class, 'emailList'])->name('customers.emails');
    });
});

// Redirect /dashboard to admin dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Error Testing Routes (Development Only)
if (config('app.debug')) {
    Route::prefix('test-errors')->group(function () {
        Route::get('404', function () {
            abort(404);
        });

        Route::get('403', function () {
            abort(403);
        });

        Route::get('419', function () {
            throw new \Illuminate\Session\TokenMismatchException();
        });

        Route::get('500', function () {
            throw new \Exception('Test 500 error for development');
        });

        Route::get('503', function () {
            abort(503);
        });

        Route::get('database', function () {
            // Force a database error
            \DB::table('non_existent_table')->get();
        });

        Route::get('auth', function () {
            throw new \Illuminate\Auth\AuthenticationException();
        });
    });
}

require __DIR__ . '/auth.php';
