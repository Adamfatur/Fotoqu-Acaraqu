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
        // Skip enum ALTERs on non-MySQL drivers (e.g., sqlite in tests)
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            // Best effort: map 'user' to 'customer' if column exists
            try {
                if (Schema::hasColumn('users', 'role')) {
                    DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
                }
            } catch (\Throwable $e) {
                // ignore in non-mysql test env
            }
            return;
        }

        // First, modify the enum to allow all values including old and new ones
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'operator', 'customer', 'user') NOT NULL DEFAULT 'customer'");
        
        // Then update existing 'user' role to 'customer'
        DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
        
        // Finally, remove 'user' from the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'operator', 'customer') NOT NULL DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            // Best effort revert mapping in non-mysql
            try {
                if (Schema::hasColumn('users', 'role')) {
                    DB::table('users')->where('role', 'customer')->update(['role' => 'user']);
                }
            } catch (\Throwable $e) {
                // ignore in non-mysql test env
            }
            return;
        }

        // Revert back to original enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator', 'user') NOT NULL DEFAULT 'user'");
        
        // Update 'customer' role back to 'user'
        DB::table('users')->where('role', 'customer')->update(['role' => 'user']);
    }
};
