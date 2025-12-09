<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_gifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_session_id')->constrained('photo_sessions')->onDelete('cascade');
            $table->string('filename');
            $table->string('s3_path')->nullable();
            $table->string('local_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status')->default('processing'); // processing|completed|failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_gifs');
    }
};
