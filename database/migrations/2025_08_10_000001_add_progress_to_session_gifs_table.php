<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('session_gifs', function (Blueprint $table) {
            if (!Schema::hasColumn('session_gifs', 'progress')) {
                $table->unsignedTinyInteger('progress')->default(0)->after('status');
            }
            if (!Schema::hasColumn('session_gifs', 'step')) {
                $table->string('step')->nullable()->after('progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('session_gifs', function (Blueprint $table) {
            if (Schema::hasColumn('session_gifs', 'step')) {
                $table->dropColumn('step');
            }
            if (Schema::hasColumn('session_gifs', 'progress')) {
                $table->dropColumn('progress');
            }
        });
    }
};
