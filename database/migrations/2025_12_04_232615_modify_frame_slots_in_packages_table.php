<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('frame_slots_new')->default(6)->after('description');
        });

        // Copy data (if any)
        DB::table('packages')->update(['frame_slots_new' => DB::raw('CAST(frame_slots AS UNSIGNED)')]);
        // Note: SQLite might need just 'frame_slots'. MySQL needs CAST or implicit.
        // Let's just set default 6 for all existing rows if casting is complex across drivers.
        // DB::table('packages')->update(['frame_slots_new' => 6]); // Safer fallback

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('frame_slots');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn('frame_slots_new', 'frame_slots');
        });
    }

    public function down(): void
    {
        // Revert is hard because we lost the enum constraint info, but we can recreate it.
        Schema::table('packages', function (Blueprint $table) {
            $table->enum('frame_slots_enum', ['6'])->default('6')->after('description');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('frame_slots');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn('frame_slots_enum', 'frame_slots');
        });
    }
};
