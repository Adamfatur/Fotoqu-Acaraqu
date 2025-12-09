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
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Populate UUIDs for existing records
        $events = \DB::table('photobooth_events')->get();
        foreach ($events as $event) {
            \DB::table('photobooth_events')
                ->where('id', $event->id)
                ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        }

        // Make it unique and not nullable
        Schema::table('photobooth_events', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photobooth_events', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
