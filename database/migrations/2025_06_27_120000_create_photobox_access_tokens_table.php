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
        Schema::create('photobox_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photobox_id')->constrained()->cascadeOnDelete();
            $table->string('token', 128)->unique();
            $table->timestamp('expires_at');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['photobox_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photobox_access_tokens');
    }
};
