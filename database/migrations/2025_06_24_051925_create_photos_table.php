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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_session_id')->constrained('photo_sessions')->onDelete('cascade');
            $table->integer('sequence_number'); // 1-10 untuk urutan foto
            $table->string('filename');
            $table->string('s3_path');
            $table->string('s3_url')->nullable();
            $table->boolean('is_selected')->default(false); // Apakah dipilih untuk frame
            $table->json('metadata')->nullable(); // Camera settings, dimensions, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
