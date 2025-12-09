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
        Schema::create('photobooth_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->integer('print_quota')->nullable()->comment('Null for unlimited (if package allows), 0 for no prints, >0 for limit');
            $table->integer('prints_used')->default(0);
            $table->timestamp('active_from')->useCurrent();
            $table->timestamp('active_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photobooth_events');
    }
};
