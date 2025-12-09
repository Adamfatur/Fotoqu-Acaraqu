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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_session_id')->constrained('photo_sessions')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Admin who processed payment
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'qris', 'edc']); // Manual payment methods
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional payment details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
