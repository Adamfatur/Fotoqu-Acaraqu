<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jika kolom payment_method adalah enum, kita perlu menggunakan DB statement untuk mengubahnya
        if (DB::getDriverName() !== 'mysql') { return; }
        DB::statement("ALTER TABLE payment_logs MODIFY payment_method ENUM('cash', 'qris', 'edc', 'free') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Kembalikan enum ke status sebelumnya tanpa 'free'
        if (DB::getDriverName() !== 'mysql') { return; }
        DB::statement("ALTER TABLE payment_logs MODIFY payment_method ENUM('cash', 'qris', 'edc') NOT NULL");
    }
};
