<?php

namespace App\Services;

use App\Models\Frame;
use App\Models\PhotoSession;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FrameService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Create a frame from selected photos in a session.
     */
    public function createFrame(PhotoSession $photoSession): Frame
    {
        $selectedPhotos = $photoSession->selectedPhotos()->orderBy('sequence_number')->get();

        // For 6-slot fotostrip frame, we expect 3 selected photos (with duplication)
        $requiredSelection = config('fotoku.frame_templates.fotostrip.user_selection', 3);
        if ($selectedPhotos->count() !== $requiredSelection) {
            throw new \Exception("Number of selected photos ({$selectedPhotos->count()}) does not match required selection ({$requiredSelection})");
        }

        // Get frame design and photo filters from session
        $frameDesign = $photoSession->frame_design ?? 'default';
        $photoFilters = $photoSession->photo_filters ?? [];

        \Log::info("FrameService: Creating frame with design and filters", [
            'session_id' => $photoSession->id,
            'frame_design' => $frameDesign,
            'photo_filters' => $photoFilters,
            'photo_filters_type' => gettype($photoFilters),
            'photo_filters_is_array' => is_array($photoFilters)
        ]);

        // Ensure photoFilters is an array (handle potential string from old data)
        if (is_string($photoFilters)) {
            $photoFilters = json_decode($photoFilters, true) ?? [];
            \Log::info("FrameService: Converted string photoFilters to array", [
                'session_id' => $photoSession->id,
                'converted_filters' => $photoFilters
            ]);
        }

        // Create frame layout based on slot count and design (no extra branding/watermark on final frame)
        $frameImage = $this->createFrameLayout($selectedPhotos, $photoSession->frame_slots, $frameDesign, $photoFilters);

        // Generate filename and save to S3
        $filename = $this->generateFrameFilename($photoSession);
        $s3Path = "frames/{$photoSession->session_code}/{$filename}";

        // Try multiple temp directory options
        $tempDirs = [
            storage_path("app/temp"),
            sys_get_temp_dir() . "/fotoku_frames",
            "/tmp/fotoku_frames"
        ];

        $tempPath = null;
        $tempDir = null;

        foreach ($tempDirs as $dir) {
            try {
                // Ensure all parent directories exist with proper permissions
                if (!is_dir($dir)) {
                    $oldUmask = umask(0);
                    $created = mkdir($dir, 0755, true);
                    umask($oldUmask);

                    if (!$created) {
                        continue; // Try next directory
                    }
                }

                // Ensure directory is writable
                if (!is_writable($dir)) {
                    // Try to fix permissions
                    if (!chmod($dir, 0755)) {
                        continue; // Try next directory
                    }
                }

                // Test if we can actually write to this directory
                $testFile = $dir . '/test_' . uniqid() . '.tmp';
                if (file_put_contents($testFile, 'test') !== false) {
                    unlink($testFile);
                    $tempDir = $dir;
                    $tempPath = $dir . "/{$filename}";
                    break;
                }

            } catch (\Exception $e) {
                \Log::warning("FrameService: Failed to use temp directory", [
                    'dir' => $dir,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        if (!$tempPath) {
            throw new \Exception("Cannot find a writable temp directory for frame creation");
        }

        // Ensure we can write to the specific file path
        if (file_exists($tempPath) && !is_writable($tempPath)) {
            if (!chmod($tempPath, 0644)) {
                throw new \Exception("Cannot make temp file writable: {$tempPath}");
            }
        }

        // Save image with enhanced error handling (maximum quality for best results)
        try {
            $quality = config('fotoku.frame.quality', 100); // Use config or default to 100%
            $frameImage->save($tempPath, $quality);

            // Verify the file was actually created and has content
            if (!file_exists($tempPath)) {
                throw new \Exception("Frame image file was not created at: {$tempPath}");
            }

            if (filesize($tempPath) === 0) {
                throw new \Exception("Frame image file is empty at: {$tempPath}");
            }

        } catch (\Exception $e) {
            \Log::error("FrameService: Failed to save frame image", [
                'temp_path' => $tempPath,
                'temp_dir' => $tempDir,
                'temp_dir_exists' => is_dir($tempDir),
                'temp_dir_writable' => is_writable($tempDir),
                'disk_space' => disk_free_space($tempDir),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("Failed to save frame image to temp path: " . $e->getMessage());
        }

        // Verify file was created
        if (!file_exists($tempPath)) {
            throw new \Exception("Frame image was not saved to temp path: {$tempPath}");
        }

        // Upload to S3 without ACL (bucket uses bucket policy for public access)
        try {
            Storage::disk('s3')->put($s3Path, file_get_contents($tempPath));
        } catch (\Exception $e) {
            // Clean up temp file before throwing
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            throw new \Exception("Failed to upload frame to S3: " . $e->getMessage());
        }

        // Clean up temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // Create frame record
        $frame = Frame::create([
            'photo_session_id' => $photoSession->id,
            'filename' => $filename,
            's3_path' => $s3Path,
            'status' => 'completed',
            'layout_data' => [
                'slots' => $photoSession->frame_slots,
                'photos' => $selectedPhotos->pluck('id')->toArray(),
            ],
        ]);

        // Generate presigned URL
        $frame->generatePresignedUrl(config('fotoku.presigned_url_days', 30));

        return $frame;
    }

    /**
     * Create frame layout based on photo slots.
     */
    private function createFrameLayout($photos, int $slots, string $frameDesign = 'default', array $photoFilters = []): \Intervention\Image\Image
    {
        // KP 108 IN size (100x148mm) at 300 DPI: 1181 x 1748 pixels
        $frameWidth = config('fotoku.frame.width', 1181);
        $frameHeight = config('fotoku.frame.height', 1748);

        \Log::info("FrameService: Creating KP 108 IN fotostrip layout", [
            'slots' => $slots,
            'frame_width' => $frameWidth,
            'frame_height' => $frameHeight,
            'frame_design' => $frameDesign
        ]);

        // Create frame layout based on slot count and design (no extra branding/watermark on final frame)
        $originalSlots = $slots; // Save original requested slots (e.g. 3)

        // Force 6 slots for KP 108 IN fotostrip format (3 photos + 3 duplicates)
        if ($slots !== 6) {
            \Log::warning("FrameService: Invalid slot count for KP 108 IN format, forcing to 6", [
                'requested_slots' => $slots,
                'forced_slots' => 6
            ]);
            $slots = 6;
        }

        // Try to load custom frame template first
        $frameTemplate = null;
        if ($frameDesign !== 'default' && !empty($frameDesign)) {
            try {
                // Handle both numeric ID and string-based frame design
                if (is_numeric($frameDesign)) {
                    $frameTemplate = \App\Models\FrameTemplate::find((int) $frameDesign);
                } else {
                    // For backwards compatibility with string-based designs
                    $frameTemplate = \App\Models\FrameTemplate::where('name', $frameDesign)->first();
                }

                if ($frameTemplate && $frameTemplate->template_path) {
                    \Log::info("FrameService: Using custom frame template", [
                        'frame_design_input' => $frameDesign,
                        'template_id' => $frameTemplate->id,
                        'template_name' => $frameTemplate->name,
                        'template_path' => $frameTemplate->template_path,
                        'template_slots' => $frameTemplate->slots
                    ]);
                } else {
                    \Log::warning("FrameService: Frame template not found or has no template path", [
                        'frame_design' => $frameDesign,
                        'template_found' => !!$frameTemplate,
                        'has_template_path' => $frameTemplate ? !!$frameTemplate->template_path : false
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("FrameService: Error loading frame template", [
                    'frame_design' => $frameDesign,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            \Log::info("FrameService: Using default frame design", [
                'frame_design' => $frameDesign
            ]);
        }

        // Create frame background
        $frame = null;

        // 1. Try Custom Template (if selected)
        if ($frameTemplate && $frameTemplate->template_path) {
            try {
                $templatePath = storage_path('app/public/' . $frameTemplate->template_path);
                if (file_exists($templatePath)) {
                    $frame = $this->imageManager->read($templatePath);
                    $frame = $frame->resize($frameWidth, $frameHeight);

                    \Log::info("FrameService: Successfully loaded custom template", [
                        'template_id' => $frameTemplate->id,
                        'path' => $templatePath
                    ]);
                } else {
                    \Log::warning("FrameService: Custom template file missing, trying default", ['path' => $templatePath]);
                }
            } catch (\Exception $e) {
                \Log::error("FrameService: Error loading custom template", ['error' => $e->getMessage()]);
            }
        }

        // 2. Try Default Template (if no custom template or custom failed)
        if (!$frame) {
            $frame = $this->loadDefaultTemplate([$originalSlots, $slots], $frameWidth, $frameHeight);
        }

        // Calculate photo positions for 4x6 fotostrip layout (6 slots)
        $photoPositions = $this->calculateFotostripPositions($frameWidth, $frameHeight);

        // Convert photos collection to array for easier indexing
        $photosArray = $photos->values()->all();

        // Process all 6 positions, using the correct photo based on photo_index
        foreach ($photoPositions as $index => $position) {
            $photoIndex = $position['photo_index']; // Which of the 3 selected photos to use

            // Ensure we have a photo at this index
            if (!isset($photosArray[$photoIndex])) {
                \Log::warning("FrameService: Photo not found at index", [
                    'slot' => $index,
                    'photo_index' => $photoIndex,
                    'available_photos' => count($photosArray)
                ]);
                continue;
            }

            $photo = $photosArray[$photoIndex];

            // Get filter for this photo position (if any)
            $filter = $photoFilters[$photoIndex] ?? 'none';

            \Log::info("FrameService: Adding photo to frame", [
                'slot' => $index + 1,
                'photo_index' => $photoIndex,
                'photo_id' => $photo->id,
                'is_duplicate' => $position['is_duplicate'],
                'position' => $position,
                'filter' => $filter
            ]);

            $this->addPhotoToFrame($frame, $photo, $position, $filter);
        }

        return $frame;
    }

    /**
     * Calculate photo positions for KP 108 IN fotostrip layout (6 slots: 3 left + 3 right duplicate).
     */
    private function calculateFotostripPositions(int $frameWidth, int $frameHeight): array
    {
        $config = config('fotoku.frame_templates.fotostrip');

        // Layout configuration based on admin template editor
        // Frame: 1181x1748 (100x148mm KP 108 IN at 300 DPI)
        // 2 strips side by side, each strip has logo area + 3 photos

        $stripWidth = 530;      // Width of each strip (adjusted for 1181px width)
        $stripHeight = 1688;    // Height of each strip (adjusted for 1748px height)
        $stripSpacing = 60;     // Space between strips
        $marginX = 30;          // Side margin
        $marginY = 30;          // Top margin

        // Logo/Brand area at top of each strip 
        $logoHeight = 180;      // Space for brand (reduced)
        $logoMargin = 40;       // Margin below logo (reduced)

        // Photo dimensions (3:2 landscape aspect ratio) 
        $photoWidth = 470;      // Width of each photo (adjusted)
        $photoHeight = 313;     // Height of each photo (3:2 ratio, adjusted)
        $photoSpacing = 40;     // Spacing between photos (reduced)

        // Bottom margin for text
        $bottomMargin = 80;     // Space for bottom text (reduced)

        // AGGRESSIVE positioning - start photos much lower
        // Frame total: 1748px height (KP 108 IN)
        // Logo area: 180px + 40px = 220px (top)
        // Bottom text: 80px (bottom)
        // Available for photos: 1748 - 30 - 220 - 80 = 1418px
        // 3 photos: 3*313 = 939px
        // Remaining space: 1418 - 939 = 479px
        // Start at position 550px from top for better layout

        $photoStartY = 550; // Start position adjusted for KP 108 IN

        $positions = [];

        // Generate positions for 6 slots in 2 strips (3 photos each)
        for ($i = 0; $i < 6; $i++) {
            $strip = floor($i / 3);     // 0 for left strip, 1 for right strip
            $posInStrip = $i % 3;       // Position within strip (0,1,2)

            // Calculate strip position
            $stripX = $marginX + ($strip * ($stripWidth + $stripSpacing));

            // Center photo horizontally within strip
            $photoX = $stripX + ($stripWidth - $photoWidth) / 2;

            // Calculate photo Y position - much more aggressive
            $photoY = $photoStartY + ($posInStrip * ($photoHeight + $photoSpacing));

            // Map to original photo index (3 selected photos)
            $photoIndex = $posInStrip; // Photos 0,1,2 are used for both strips

            $positions[] = [
                'x' => $photoX,
                'y' => $photoY,
                'width' => $photoWidth,
                'height' => $photoHeight,
                'strip' => $strip,
                'position_in_strip' => $posInStrip,
                'photo_index' => $photoIndex,    // Which of the 3 selected photos to use
                'is_duplicate' => $strip === 1,  // Mark right strip as duplicates
                'slot_number' => $i + 1
            ];
        }

        \Log::info("FrameService: Generated 6-slot fotostrip positions for KP 108 IN", [
            'frame_size' => [$frameWidth, $frameHeight],
            'strip_dimensions' => [$stripWidth, $stripHeight],
            'photo_dimensions' => [$photoWidth, $photoHeight],
            'logo_area' => $logoHeight,
            'logo_margin' => $logoMargin,
            'photo_start_y' => $photoStartY,
            'total_slots' => 6,
            'selected_photos' => 3,
            'strips' => 2,
            'paper_format' => 'KP 108 IN (100x148mm)',
            'positions' => $positions
        ]);

        return $positions;
    }

    /**
     * Calculate photo positions for different slot configurations (DEPRECATED - kept for compatibility).
     */
    private function calculatePhotoPositions(int $slots, int $frameWidth, int $frameHeight): array
    {
        // Redirect to fotostrip layout for any slot count
        return $this->calculateFotostripPositions($frameWidth, $frameHeight);

        // Old code below kept for reference but not used
        $padding = 60;
        $spacing = 40;
        $brandingHeight = 220; // Space for large FOTOKU text only (no tagline)

        $availableWidth = $frameWidth - (2 * $padding);
        $availableHeight = $frameHeight - (2 * $padding) - $brandingHeight;

        switch ($slots) {
            case 4:
                // 2x2 grid
                $photoWidth = ($availableWidth - $spacing) / 2;
                $photoHeight = ($availableHeight - $spacing) / 2;

                return [
                    ['x' => $padding, 'y' => $padding + $brandingHeight, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding + $photoWidth + $spacing, 'y' => $padding + $brandingHeight, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding, 'y' => $padding + $brandingHeight + $photoHeight + $spacing, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding + $photoWidth + $spacing, 'y' => $padding + $brandingHeight + $photoHeight + $spacing, 'width' => $photoWidth, 'height' => $photoHeight],
                ];

            case 6:
                // 2x3 grid
                $photoWidth = ($availableWidth - $spacing) / 2;
                $photoHeight = ($availableHeight - (2 * $spacing)) / 3;

                return [
                    ['x' => $padding, 'y' => $padding + $brandingHeight, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding + $photoWidth + $spacing, 'y' => $padding + $brandingHeight, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding, 'y' => $padding + $brandingHeight + $photoHeight + $spacing, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding + $photoWidth + $spacing, 'y' => $padding + $brandingHeight + $photoHeight + $spacing, 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding, 'y' => $padding + $brandingHeight + (2 * $photoHeight) + (2 * $spacing), 'width' => $photoWidth, 'height' => $photoHeight],
                    ['x' => $padding + $photoWidth + $spacing, 'y' => $padding + $brandingHeight + (2 * $photoHeight) + (2 * $spacing), 'width' => $photoWidth, 'height' => $photoWidth],
                ];

            case 8:
                // 2x4 grid
                $photoWidth = ($availableWidth - $spacing) / 2;
                $photoHeight = ($availableHeight - (3 * $spacing)) / 4;

                $positions = [];
                for ($row = 0; $row < 4; $row++) {
                    for ($col = 0; $col < 2; $col++) {
                        $positions[] = [
                            'x' => $padding + ($col * ($photoWidth + $spacing)),
                            'y' => $padding + $brandingHeight + ($row * ($photoHeight + $spacing)),
                            'width' => $photoWidth,
                            'height' => $photoHeight,
                        ];
                    }
                }
                return $positions;

            default:
                throw new \Exception("Unsupported slot count: {$slots}");
        }
    }

    /**
     * Add a photo to the frame at specified position.
     */
    private function addPhotoToFrame($frame, $photo, array $position, string $filter): void
    {
        try {
            // Prefer local file if available (local-first flow)
            $photoContent = null;
            if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                \Log::info("FrameService: Using local photo for frame", [
                    'photo_id' => $photo->id,
                    'local_path' => $photo->local_path
                ]);
                $photoContent = file_get_contents($photo->local_path);
            } else {
                // Fallback to S3
                if (!$photo->s3_path || !Storage::disk('s3')->exists($photo->s3_path)) {
                    \Log::warning("Photo not found locally or in S3", ['photo_id' => $photo->id, 's3_path' => $photo->s3_path]);
                    // Placeholder
                    $placeholderImage = $this->imageManager->create($position['width'], $position['height'])->fill('#f3f4f6');
                    $placeholderImage->text("Photo #{$photo->sequence_number}", $position['width'] / 2, $position['height'] / 2, function ($font) {
                        $font->size(24);
                        $font->color('#6b7280');
                        $font->align('center');
                        $font->valign('middle');
                    });
                    $frame->place($placeholderImage, 'top-left', $position['x'], $position['y']);
                    return;
                }
                \Log::info("FrameService: Downloading photo from S3", [
                    'photo_id' => $photo->id,
                    's3_path' => $photo->s3_path
                ]);
                $photoContent = Storage::disk('s3')->get($photo->s3_path);
                if (!$photoContent) {
                    throw new \Exception("Empty photo content from S3");
                }
            }

            $photoImage = $this->imageManager->read($photoContent);

            // Resize and crop to fit position
            $photoImage->cover($position['width'], $position['height']);

            // Apply filter to photo
            $photoImage = $this->applyPhotoFilter($photoImage, $filter);

            // Add photo to frame
            $frame->place($photoImage, 'top-left', $position['x'], $position['y']);

        } catch (\Exception $e) {
            \Log::error("Failed to add photo to frame", [
                'photo_id' => $photo->id,
                's3_path' => $photo->s3_path,
                'error' => $e->getMessage()
            ]);

            // Create error placeholder
            $errorImage = $this->imageManager->create($position['width'], $position['height'])->fill('#fee2e2');
            $errorImage->text("Error loading\nPhoto #{$photo->sequence_number}", $position['width'] / 2, $position['height'] / 2, function ($font) {
                $font->size(20);
                $font->color('#dc2626');
                $font->align('center');
                $font->valign('middle');
            });

            $frame->place($errorImage, 'top-left', $position['x'], $position['y']);
        }
    }

    /**
     * Add Fotoku branding to the frame.
     */
    private function addBranding($frame): void
    {
        // Intentionally left empty: final frames must not be altered.
        return;
    }

    /**
     * Generate unique frame filename.
     */
    private function generateFrameFilename(PhotoSession $photoSession): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        return "{$photoSession->session_code}_frame_{$timestamp}_{$random}.jpg";
    }

    /**
     * Get background color based on frame design
     */
    private function getFrameBackgroundColor(string $frameDesign): string
    {
        if ($frameDesign === 'default') {
            return '#ffffff'; // White default
        }

        // Try to get from frame template
        try {
            $template = \App\Models\FrameTemplate::find($frameDesign);
            if ($template && $template->background_color) {
                return $template->background_color;
            }
        } catch (\Exception $e) {
            \Log::warning("FrameService: Could not load frame template", [
                'frame_design' => $frameDesign,
                'error' => $e->getMessage()
            ]);
        }

        return '#ffffff'; // Fallback to white
    }

    /**
     * Apply photo filter to image
     */
    private function applyPhotoFilter(\Intervention\Image\Image $image, string $filter): \Intervention\Image\Image
    {
        switch ($filter) {
            case 'vivid':
                $image->gamma(1.2)->brightness(10);
                break;

            case 'dramatic':
                $image->contrast(30)->gamma(0.8);
                break;

            case 'blackwhite':
                $image->greyscale();
                break;

            case 'sepia':
                $image->greyscale();
                $image->colorize(30, 20, -10); // Add sepia tone
                break;

            case 'cool':
                $image->colorize(-10, 0, 15); // Cool blue tone
                break;

            case 'warm':
                $image->colorize(15, 5, -10); // Warm red/yellow tone
                break;

            case 'negative':
                $image->invert();
                break;

            case 'none':
            default:
                // No filter applied
                break;
        }

        return $image;
    }

    /**
     * Load default frame template or fallback to white background
     * @param int|array $slots Slot count(s) to search for
     */
    private function loadDefaultTemplate($slots, int $frameWidth, int $frameHeight): \Intervention\Image\Image
    {
        $slotCounts = is_array($slots) ? $slots : [$slots];
        $slotCounts = array_unique($slotCounts);

        $defaultTemplate = null;

        // 1. Try to find exact match for slots
        foreach ($slotCounts as $s) {
            $defaultTemplate = \App\Models\FrameTemplate::where('slots', $s)
                ->where('is_default', true)
                ->where('status', 'active')
                ->first();

            if ($defaultTemplate) {
                \Log::info("FrameService: Found default template for {$s} slots", [
                    'template_id' => $defaultTemplate->id,
                    'template_name' => $defaultTemplate->name
                ]);
                break;
            }
        }

        // 2. If no match, try to find ANY default template (as a desperate fallback)
        if (!$defaultTemplate) {
            $defaultTemplate = \App\Models\FrameTemplate::where('is_default', true)
                ->where('status', 'active')
                ->first();

            if ($defaultTemplate) {
                \Log::warning("FrameService: No exact slot match found, using generic default template", [
                    'requested_slots' => implode(',', $slotCounts),
                    'found_template_id' => $defaultTemplate->id,
                    'found_template_slots' => $defaultTemplate->slots
                ]);
            }
        }

        if ($defaultTemplate && $defaultTemplate->template_path) {
            try {
                $templatePath = storage_path('app/public/' . $defaultTemplate->template_path);
                if (file_exists($templatePath)) {
                    $frame = $this->imageManager->read($templatePath);
                    return $frame->resize($frameWidth, $frameHeight);
                } else {
                    \Log::warning("FrameService: Default template file not found: {$templatePath}");
                }
            } catch (\Exception $e) {
                \Log::warning("FrameService: Could not load default template, using white background", [
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            \Log::warning("FrameService: No default template found for slots: " . implode(', ', $slotCounts));
        }

        // Use default white background with branding
        \Log::info("FrameService: Using white background fallback");
        $backgroundColor = $this->getFrameBackgroundColor('default');
        return $this->imageManager->create($frameWidth, $frameHeight)->fill($backgroundColor);
    }
}
