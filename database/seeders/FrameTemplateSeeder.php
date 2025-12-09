<?php

namespace Database\Seeders;

use App\Models\FrameTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FrameTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing frame templates first (use delete instead of truncate for foreign key safety)
        FrameTemplate::query()->delete();
        
        // Create ONLY ONE default frame template for 6-slot fotostrip format
        $template = [
            'name' => 'Template Fotostrip Default',
            'description' => 'Template default dengan layout fotostrip 4x6 inch untuk 6 slot (3 foto + 3 duplikat)',
            'slots' => '6',
            'template_path' => 'frame-templates/default-fotostrip.png',
            'preview_path' => 'frame-templates/previews/default-fotostrip-preview.png',
            'layout_config' => [
                'format' => 'fotostrip',
                'background_color' => '#ffffff',
                'strips' => 2,
                'photos_per_strip' => 3,
                'logo_area' => [
                    'height' => 200,    
                    'margin_bottom' => 50 
                ],
                'dimensions' => [
                    'strip_width' => 540,
                    'strip_spacing' => 60,
                    'margin_x' => 30,
                    'margin_y' => 30,
                    'photo_width' => 480,
                    'photo_height' => 320,
                    'photo_spacing' => 50,
                    'bottom_margin' => 100  
                ],
                'slots' => [
                    // Strip kiri - 3 foto (start at 600px from top - MUCH LOWER)
                    ['x' => 60, 'y' => 600, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 1, 'position' => 1],
                    ['x' => 60, 'y' => 970, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 1, 'position' => 2],
                    ['x' => 60, 'y' => 1340, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 1, 'position' => 3],
                    
                    // Strip kanan - 3 foto duplikat (start at 600px from top - MUCH LOWER)
                    ['x' => 660, 'y' => 600, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 2, 'position' => 1],
                    ['x' => 660, 'y' => 970, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 2, 'position' => 2],
                    ['x' => 660, 'y' => 1340, 'width' => 480, 'height' => 320, 'type' => 'photo', 'strip' => 2, 'position' => 3],
                ],
                'logo_positions' => [
                    // Logo positions for each strip (centered in logo area)
                    ['x' => 300, 'y' => 130, 'text' => 'FOTOKU', 'strip' => 1], 
                    ['x' => 900, 'y' => 130, 'text' => 'FOTOKU', 'strip' => 2], 
                ],
                'bottom_text' => [
                    ['x' => 300, 'y' => 1720, 'text' => 'KREATIF DESAIN', 'strip' => 1], // Much closer to bottom
                    ['x' => 900, 'y' => 1720, 'text' => 'KREATIF DESAIN', 'strip' => 2], 
                ]
            ],
            'status' => 'active',
            'is_default' => true,
            'background_color' => '#ffffff',
            'width' => 1200,  // 4x6 inch at 300 DPI
            'height' => 1800, // 4x6 inch at 300 DPI
        ];

        FrameTemplate::create($template);

        // Create storage directories if they don't exist
        Storage::disk('public')->makeDirectory('frame-templates');
        Storage::disk('public')->makeDirectory('frame-templates/previews');
        
        $this->command->info('Frame template seeded successfully! (ONLY 1 template created)');
        $this->command->info('Note: Add actual template images to storage/app/public/frame-templates/');
    }
}
