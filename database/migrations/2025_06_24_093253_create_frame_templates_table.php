<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('frame_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('slots', ['6']); // Only 6 slots for 4x6 fotostrip format
            $table->string('template_path'); // Path to template image
            $table->string('preview_path')->nullable(); // Preview image path
            $table->json('layout_config'); // JSON layout configuration
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->string('background_color')->default('#ffffff');
            $table->integer('width')->default(1200); // 4 inch width in pixels (300 DPI)
            $table->integer('height')->default(1800); // 6 inch height in pixels (300 DPI)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_templates');
    }
};
