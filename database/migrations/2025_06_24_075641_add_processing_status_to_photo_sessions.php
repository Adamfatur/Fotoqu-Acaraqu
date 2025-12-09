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
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->enum('session_status', [
                'created', 
                'approved', 
                'in_progress', 
                'photo_selection', 
                'processing',
                'completed', 
                'cancelled'
            ])->default('created')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_sessions', function (Blueprint $table) {
            $table->enum('session_status', [
                'created', 
                'approved', 
                'in_progress', 
                'photo_selection', 
                'completed', 
                'cancelled'
            ])->default('created')->change();
        });
    }
};
