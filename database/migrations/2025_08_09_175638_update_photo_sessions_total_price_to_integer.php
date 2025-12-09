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
        Schema::table('photo_sessions', function (Blueprint $table) {
            // First multiply existing values by 100 to convert from decimal to proper rupiah
            // This handles case where existing data might be stored as 350.00 instead of 35000
            DB::statement('UPDATE photo_sessions SET total_price = total_price * 100 WHERE total_price < 1000');
            
            // Then change column type to unsigned big integer
            $table->unsignedBigInteger('total_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_sessions', function (Blueprint $table) {
            // Convert back to decimal
            $table->decimal('total_price', 10, 2)->change();
            
            // Divide by 100 to restore decimal format
            DB::statement('UPDATE photo_sessions SET total_price = total_price / 100');
        });
    }
};
