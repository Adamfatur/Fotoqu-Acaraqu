<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FrameTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
    if (\DB::getDriverName() === 'mysql') { \DB::statement('SET FOREIGN_KEY_CHECKS=0;'); }
        
        // Clear existing templates
        FrameTemplate::truncate();
        
        // Re-enable foreign key checks
    if (\DB::getDriverName() === 'mysql') { \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); }
        
        // Create new 4x6 fotostrip template
        FrameTemplate::create([
            'name' => 'Template 4x6 Fotostrip Default',
            'description' => 'Template default untuk format 4x6 inch fotostrip dengan 6 slot foto (3 kiri + 3 kanan duplikat)',
            'slots' => '6',
            'template_path' => 'templates/4x6/default-fotostrip-template.png',
            'preview_path' => 'templates/4x6/default-fotostrip-preview.png',
            'layout_config' => [
                'type' => 'fotostrip',
                'format' => '4x6_inch',
                'strips' => 2,
                'photos_per_strip' => 3,
                'slots' => [
                    // Left strip (3 photos) - 3:2 landscape ratio (450x300)
                    ['x' => 60, 'y' => 100, 'width' => 450, 'height' => 300, 'strip' => 0, 'position_in_strip' => 0, 'is_duplicate' => false],
                    ['x' => 60, 'y' => 420, 'width' => 450, 'height' => 300, 'strip' => 0, 'position_in_strip' => 1, 'is_duplicate' => false],
                    ['x' => 60, 'y' => 740, 'width' => 450, 'height' => 300, 'strip' => 0, 'position_in_strip' => 2, 'is_duplicate' => false],
                    // Right strip (3 photos - duplicates of left strip) - 3:2 landscape ratio
                    ['x' => 570, 'y' => 100, 'width' => 450, 'height' => 300, 'strip' => 1, 'position_in_strip' => 0, 'is_duplicate' => true],
                    ['x' => 570, 'y' => 420, 'width' => 450, 'height' => 300, 'strip' => 1, 'position_in_strip' => 1, 'is_duplicate' => true],
                    ['x' => 570, 'y' => 740, 'width' => 450, 'height' => 300, 'strip' => 1, 'position_in_strip' => 2, 'is_duplicate' => true],
                    // Logo areas per strip
                    ['x' => 60, 'y' => 1060, 'width' => 450, 'height' => 150, 'strip' => 0, 'position_in_strip' => 'logo', 'is_logo' => true, 'type' => 'logo'],
                    ['x' => 570, 'y' => 1060, 'width' => 450, 'height' => 150, 'strip' => 1, 'position_in_strip' => 'logo', 'is_logo' => true, 'type' => 'logo'],
                ]
            ],
            'status' => 'active',
            'is_default' => true,
            'background_color' => '#ffffff',
            'width' => 1200,  // 4 inch × 300 DPI
            'height' => 1800, // 6 inch × 300 DPI
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        FrameTemplate::where('name', 'Template 4x6 Fotostrip Default')->delete();
    }
};
