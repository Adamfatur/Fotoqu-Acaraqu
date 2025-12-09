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
        Schema::table('photobooth_events', function (Blueprint $table) {
            $table->foreignId('photobox_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photobooth_events', function (Blueprint $table) {
            $table->dropForeign(['photobox_id']);
            $table->dropColumn('photobox_id');
        });
    }
};
