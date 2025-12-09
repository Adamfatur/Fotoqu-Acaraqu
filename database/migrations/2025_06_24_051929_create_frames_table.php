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
        Schema::create('frames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_session_id')->constrained('photo_sessions')->onDelete('cascade');
            $table->string('filename');
            $table->string('s3_path');
            $table->string('s3_url')->nullable();
            $table->string('presigned_url')->nullable();
            $table->timestamp('presigned_expires_at')->nullable();
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->json('layout_data')->nullable(); // Frame layout configuration
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frames');
    }
};
