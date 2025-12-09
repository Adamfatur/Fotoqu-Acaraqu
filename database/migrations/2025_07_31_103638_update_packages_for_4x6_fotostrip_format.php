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
        // First, update all existing packages to use 6 slots (fotostrip format with duplication)
        DB::table('packages')->update(['frame_slots' => '6']);
        
        Schema::table('packages', function (Blueprint $table) {
            // Update enum to only support 6 slots for 4x6 fotostrip format (3 photos + 3 duplicates)
            $table->enum('frame_slots', ['6'])->default('6')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Revert to original enum with multiple slot options
            $table->enum('frame_slots', ['4', '5', '6', '8'])->change();
        });
    }
};
