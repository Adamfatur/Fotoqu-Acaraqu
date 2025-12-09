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
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->foreignId('photobooth_event_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->dropForeign(['photobooth_event_id']);
            $table->dropColumn('photobooth_event_id');
        });
    }
};
