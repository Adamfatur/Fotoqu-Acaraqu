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
        Schema::table('payment_logs', function (Blueprint $table) {
            // First multiply existing values by 100 to convert from decimal to proper rupiah
            // This handles case where existing data might be stored as 35.00 instead of 35000
            DB::statement('UPDATE payment_logs SET amount = amount * 100 WHERE amount < 1000');
            
            // Then change column type to unsigned big integer
            $table->unsignedBigInteger('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            // Convert back to decimal
            $table->decimal('amount', 10, 2)->change();
            
            // Divide by 100 to restore decimal format
            DB::statement('UPDATE payment_logs SET amount = amount / 100');
        });
    }
};
