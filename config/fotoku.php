<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fotoku Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings specific to the Fotoku photobox application.
    |
    */

    // Pricing configuration for 4x6 fotostrip format
    'frame_price_6_slots' => env('FOTOKU_FRAME_PRICE_6_SLOTS', 35000), // Only 6 slots supported

    // Photo session configuration
    'photo_countdown_seconds' => env('FOTOKU_PHOTO_COUNTDOWN_SECONDS', 3),
    'photo_interval_seconds' => env('FOTOKU_PHOTO_INTERVAL_SECONDS', 3),
        'total_photos' => env('FOTOKU_TOTAL_PHOTOS', 3),
    // JPEG quality for client-side capture (accepts 0-1 or 1-100). Example: 0.99 or 99.
    'photo_jpeg_quality' => env('FOTOKU_PHOTO_JPEG_QUALITY', 0.99),

    // AWS S3 configuration
    'presigned_url_days' => env('FOTOKU_PRESIGNED_URL_DAYS', 30),

    // GIF generation configuration
    'gif' => [
        // Frames per second for the animated GIF. Lower value slows the loop so it doesn't feel rushed.
        'fps' => env('FOTOKU_GIF_FPS', 3),
        // Reserved for future use if we want to extend loop length without changing pace
        'target_duration_ms' => env('FOTOKU_GIF_TARGET_DURATION_MS', 1500),
    // Queue name to dispatch GIF generation jobs to (ensure your worker listens to this)
    'queue' => env('FOTOKU_GIF_QUEUE', 'media'),
    ],

    // Frame layout configuration for KP 108 IN (100x148mm) Canon Selphy CP1500
    'frame' => [
        'width' => 1181,  // 100mm width at 300 DPI (3.937 inch)
        'height' => 1748, // 148mm height at 300 DPI (5.827 inch)
        'quality' => 100, // Maximum JPEG quality for best results
        'background_color' => '#ffffff',
        'padding' => 30,  // Padding for KP 108 IN format
        'spacing' => 20,  // Spacing between photos
        'branding_height' => 80, // Reduced branding area for compact KP 108 IN format
        
        // KP 108 IN Fotostrip Layout (Brand di atas, 3 foto pilihan di 6 slot dengan duplikasi)
        'fotostrip' => [
            'total_slots' => 6,     // 6 slot frame (3 kiri + 3 kanan duplikat)
            'user_selection' => 3,  // User pilih 3 foto terbaik dari 3 foto
            'strips' => 2,          // 2 strip (kiri dan kanan)
            'photos_per_strip' => 3,
            'strip_width' => 530,   // Adjusted for KP 108 IN width (1181px)
            'strip_height' => 1688, // Adjusted for KP 108 IN height (1748px)
            'photo_width' => 470,   // 3:2 landscape width (adjusted)
            'photo_height' => 313,  // 3:2 landscape height (adjusted)
            'photo_spacing' => 40,  // Spacing between photos (reduced)
            'logo_height' => 180,   // Logo/Brand area height (adjusted)
            'logo_margin' => 40,    // Margin between logo and photos (reduced)
            'side_margin' => 30,    // Margin from sides (reduced)
            'top_margin' => 30,     // Top margin (reduced)
            'bottom_margin' => 80,  // Bottom margin (reduced)
            'strip_spacing' => 60,  // Space between strips
        ]
    ],

    // Camera simulation settings (for development)
    'simulate_camera' => env('FOTOKU_SIMULATE_CAMERA', true),
    'simulation_image_width' => 800,
    'simulation_image_height' => 600,

    // Photobox status options
    'photobox_statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'maintenance' => 'Under Maintenance',
    ],

    // Session status options
    'session_statuses' => [
        'created' => 'Created',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    // Payment method options
    'payment_methods' => [
        'free' => 'Free',
        'qris' => 'QRIS',
        'edc' => 'EDC/Card',
    ],

    // Activity log types
    'activity_types' => [
        'session_created' => 'Session Created',
        'payment_received' => 'Payment Received',
        'session_approved' => 'Session Approved',
        'session_started' => 'Session Started',
        'session_completed' => 'Session Completed',
        'session_cancelled' => 'Session Cancelled',
        'frame_created' => 'Frame Created',
        'frame_printed' => 'Frame Printed',
        'email_sent' => 'Email Sent',
    ],

    // Email configuration
    'email' => [
        'from_name' => env('MAIL_FROM_NAME', 'Fotoku'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@fotoku.com'),
        'reply_to' => env('FOTOKU_REPLY_TO_EMAIL', 'support@fotoku.com'),
    ],

    // Frame templates configuration
    'frame_templates' => [
        'fotostrip' => [
            'total_slots' => 6,      // 6 slot frame (3 kiri + 3 kanan duplikat)
                'user_selection' => 3,   // User pilih 3 foto terbaik dari 3 foto
            'strips' => 2,           // 2 strip (kiri dan kanan)
            'photos_per_strip' => 3,
            'photo_width' => 480,    // 3:2 landscape width (updated for better layout)
            'photo_height' => 320,   // 3:2 landscape height (updated for better layout)
            'spacing' => 25,         // Spacing between photos
            'margin_x' => 30,        // Side margin (updated for proper layout)
            'margin_y' => 30,        // Top margin (updated for proper layout)
        ]
    ],

    // UI configuration
    'ui' => [
        'primary_color' => '#F8BBD9',      // Pastel pink
        'secondary_color' => '#C3E9FF',    // Pastel blue
        'accent_color' => '#D4F1A5',       // Pastel green
        'warning_color' => '#FFE5A3',      // Pastel yellow
        'success_color' => '#B8E6B8',      // Pastel green
        'background_color' => '#FFF5F8',   // Very light pink
    ],

    // Admin dashboard settings
    'dashboard' => [
        'items_per_page' => 20,
        'recent_sessions_count' => 10,
        'stats_days' => 30,
    ],

    // Security settings
    'security' => [
        'max_failed_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'session_timeout' => 1440, // 24 hours
    ],
];
