<?php

use App\Http\Controllers\PhotoboxController;
use App\Http\Controllers\Api\SecurePhotoboxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API routes for photobox (no CSRF protection)
// Desktop App API Routes
Route::prefix('v1/desktop')->group(function () {
    // Check for available session (polling)
    Route::get('/check-session', [PhotoboxController::class, 'checkSession']);

    // Start session (lock it for this device)
    Route::post('/start-session', [PhotoboxController::class, 'startDesktopSession']);

    // Upload captured photo (1 by 1)
    Route::post('/upload-photo', [PhotoboxController::class, 'uploadPhoto']);

    // Upload final composite frame
    Route::post('/upload-frame', [PhotoboxController::class, 'uploadFrame']);

    // Upload generated GIF
    Route::post('/upload-gif', [PhotoboxController::class, 'uploadGif']);

    // Complete session and get QR code URL
    Route::post('/complete-session', [PhotoboxController::class, 'completeSession']);

    // Get available frame templates
    Route::get('/frames', [PhotoboxController::class, 'getDesktopFrameTemplates']);

    // Serve frame template asset (CORS friendly)
    Route::get('/frame-assets/{template}', [PhotoboxController::class, 'serveFrameTemplateAsset']);

    // Get System Settings
    Route::get('/settings', [PhotoboxController::class, 'getSettings']);
});
