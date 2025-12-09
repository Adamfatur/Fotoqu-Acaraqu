<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FOTOQU Photobooth - {{ $photobox->code }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-fotoku-favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo-fotoku-favicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Security Protection Layers --}}
    @include('photobox.components.stable-security')
    <script>
        // Early token presence check for better UX
        (function () {
            try {
                const u = new URL(window.location.href);
                const token = u.searchParams.get('token');
                if (!token) {
                    document.write('<div style="padding:24px;color:#111;background:#fff;max-width:720px;margin:64px auto;border-radius:12px;border:1px solid #e5e7eb;font-family:ui-sans-serif,system-ui">' +
                        '<h2 style="font-size:20px;margin:0 0 8px">Akses Terbatas</h2>' +
                        '<p style="margin:0 0 12px;color:#374151">Halaman ini membutuhkan token akses sementara yang dikeluarkan admin.</p>' +
                        '<p style="margin:0;color:#6b7280">Minta admin untuk membuatkan link dengan masa berlaku 24 jam.</p>' +
                        '</div>');
                }
            } catch (e) { }
        })();
    </script>

    {{-- Import Styles --}}
    @include('photobox.components.styles')
    <style>
        /* Hide any footer on photobox interface pages */
        footer {
            display: none !important;
        }
    </style>
</head>

<body class="h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-800">
    <div id="app" class="h-full flex flex-col">
        {{-- Header with Camera Settings --}}
        @include('photobox.components.header')

        {{-- Main Content Area --}}
        <main class="flex-1 p-6 overflow-hidden">
            {{-- Waiting State --}}
            @include('photobox.components.waiting-state')

            {{-- Camera Capture State --}}
            @include('photobox.components.capture-state')

            {{-- Photo Selection State --}}
            @include('photobox.components.selection-state')

            {{-- Frame Design Selection State --}}
            @include('photobox.components.frame-design-state')

            {{-- TEMPORARY: Photo Filter State - HIDDEN/SKIPPED (will be re-enabled in future) --}}
            {{-- TODO: Re-enable photo filter functionality when needed --}}
            @include('photobox.components.photo-filter-state')

            {{-- Processing State --}}
            @include('photobox.components.processing-state')

            {{-- Completed State --}}
            @include('photobox.components.completed-state')
        </main>
    </div>

    {{-- External Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Global Photobox Alert Modal --}}
    @include('photobox.components.alert-modal')

    {{-- EMERGENCY IMMEDIATE DEFINITION --}}
    <script>
        console.log('=== EMERGENCY startProcessing DEFINITION ===');
        window.startProcessing = function () {
            console.log('✅ Emergency startProcessing called - this should NOT happen if override works!');
            alert('Emergency startProcessing function called!');
        };
        console.log('Emergency startProcessing defined. Type:', typeof window.startProcessing);
    </script>

    {{-- Import JavaScript Components --}}
    @include('photobox.components.local-storage-js')
    @include('photobox.components.core-js')
    @include('photobox.components.state-management-js')
    @include('photobox.components.camera-js')
    @include('photobox.components.photo-selection-js')
    @include('photobox.components.frame-design-js')
    @include('photobox.components.processing-js')
    {{-- TEMPORARY: Photo filter JS - SKIPPED (will be re-enabled in future) --}}
    {{-- TODO: Re-enable when photo filter functionality is needed --}}
    @include('photobox.components.photo-filter-js')
    @include('photobox.components.completed-state-js')
    @include('photobox.components.session-management-js')
    @include('photobox.components.fullscreen-js')
    @include('photobox.components.global-functions')

    {{-- QR Code Synchronization Manager --}}
    @include('photobox.components.qr-sync-manager')

    {{-- FORCE QR REFRESH - Must be loaded last to override all QR functions --}}
    @include('photobox.components.force-qr-refresh')

    {{-- QR DEBUG CONSOLE - Only for development/testing --}}
    {{-- DEBUG TOOLS TEMPORARILY HIDDEN --}}
    {{-- @if(config('app.debug')) --}}
    {{-- @include('photobox.components.qr-debug-console') --}}
    {{-- @include('photobox.components.realtime-qr-validation') --}}
    {{-- @endif --}}

    {{-- Force cache busting for JavaScript --}}
    <script>
        console.log('=== FINAL INTERFACE SCRIPT LOADING ===');
        const loadTime = new Date().toISOString();
        console.log('JavaScript loaded at:', loadTime);
        console.log('startProcessing function type:', typeof startProcessing);
        console.log('window.startProcessing type:', typeof window.startProcessing);
        console.log('window.startProcessing value:', window.startProcessing);
        console.log('Cache bust version: {{ time() }}');

        // Test calling the function
        if (typeof window.startProcessing === 'function') {
            console.log('✅ startProcessing is callable from interface.blade.php!');
        } else {
            console.error('❌ startProcessing is NOT callable from interface.blade.php!');
            console.error('Type:', typeof window.startProcessing);
            console.error('Value:', window.startProcessing);
        }

        // Emergency fallback
        if (typeof startProcessing === 'undefined' && typeof window.startProcessing === 'undefined') {
            console.warn('startProcessing not found during initial load, will check again later');
        }
    </script>
</body>

</html>