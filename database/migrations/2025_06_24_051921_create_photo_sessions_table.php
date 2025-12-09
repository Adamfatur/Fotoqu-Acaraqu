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
        Schema::create('photo_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_code')->unique(); // FOTOKU-A3B7C9D2E4, FOTOKU-X8Y2Z6H5M9, etc (10 alphanumeric chars)
            $table->foreignId('photobox_id')->constrained('photoboxes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Admin who created the session
            $table->string('customer_name');
            $table->string('customer_email');
            $table->enum('frame_slots', ['4', '6', '8']); // Number of photo slots in frame
            $table->decimal('total_price', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('session_status', ['created', 'approved', 'in_progress', 'completed', 'cancelled'])->default('created');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_sessions');
    }
};
