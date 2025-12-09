<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing records to compatible values
        DB::table('frame_templates')->update([
            'slots' => '6',
            'width' => 1200,
            'height' => 1800,
        ]);
        
        Schema::table('frame_templates', function (Blueprint $table) {
            // Update slots enum to only support 6 slots (3x2 format)
            $table->enum('slots', ['6'])->default('6')->change();
            
            // Update dimensions for 4x6 inch format at 300 DPI
            // 4 inch × 300 DPI = 1200 pixels width
            // 6 inch × 300 DPI = 1800 pixels height
            $table->integer('width')->default(1200)->change();
            $table->integer('height')->default(1800)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frame_templates', function (Blueprint $table) {
            // Revert to original slots enum
            $table->enum('slots', ['4', '6', '8'])->change();
            
            // Revert to A5 dimensions
            $table->integer('width')->default(1748)->change();
            $table->integer('height')->default(2480)->change();
        });
    }
};
