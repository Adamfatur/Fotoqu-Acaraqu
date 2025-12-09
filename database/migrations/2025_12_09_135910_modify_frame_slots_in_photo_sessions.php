<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add temporary integer column
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->integer('frame_slots_int')->default(6)->after('customer_email');
        });

        // 2. Copy data
        // For MySQL/MariaDB, basic casting or implicit conversion works for '4' -> 4.
        DB::statement('UPDATE photo_sessions SET frame_slots_int = CAST(frame_slots AS UNSIGNED)');

        // 3. Drop old enum column
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->dropColumn('frame_slots');
        });

        // 4. Rename new column to original name
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->renameColumn('frame_slots_int', 'frame_slots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to ENUM
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->enum('frame_slots_enum', ['2', '4', '6', '8'])->default('6')->after('customer_email');
        });

        DB::statement("UPDATE photo_sessions SET frame_slots_enum = CAST(frame_slots AS CHAR)");

        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->dropColumn('frame_slots');
        });

        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->renameColumn('frame_slots_enum', 'frame_slots');
        });
    }
};
