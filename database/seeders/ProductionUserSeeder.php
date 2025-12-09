<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionUserSeeder extends Seeder
{
    /**
     * Run the database seeder for production.
     */
    public function run(): void
    {
        $this->command->info('Starting Production User Seeder...');
        
        // Check if admin user exists
        $adminExists = DB::table('users')->where('email', 'admin@fotoku.com')->exists();
        
        if (!$adminExists) {
            // Insert admin user using raw SQL to bypass model defaults
            DB::table('users')->insert([
                'name' => 'Administrator',
                'email' => 'admin@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081234567890',
                'notes' => 'Super admin with full access',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Created admin user: admin@fotoku.com');
        } else {
            // Update existing admin to ensure correct role
            DB::table('users')
                ->where('email', 'admin@fotoku.com')
                ->update(['role' => 'admin', 'status' => 'active']);
            $this->command->info('✓ Updated existing admin user');
        }

        // Check if manager user exists
        $managerExists = DB::table('users')->where('email', 'manager@fotoku.com')->exists();
        
        if (!$managerExists) {
            DB::table('users')->insert([
                'name' => 'Manager Fotoku',
                'email' => 'manager@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status' => 'active',
                'phone' => '081234567891',
                'notes' => 'Manager with session and customer management access',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Created manager user: manager@fotoku.com');
        } else {
            // Update existing manager to ensure correct role
            DB::table('users')
                ->where('email', 'manager@fotoku.com')
                ->update(['role' => 'manager', 'status' => 'active']);
            $this->command->info('✓ Updated existing manager user');
        }

        // Check if operator user exists
        $operatorExists = DB::table('users')->where('email', 'operator@fotoku.com')->exists();
        
        if (!$operatorExists) {
            DB::table('users')->insert([
                'name' => 'Operator Photobox',
                'email' => 'operator@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'status' => 'active',
                'phone' => '081234567892',
                'notes' => 'Operator with photobox operation access',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Created operator user: operator@fotoku.com');
        } else {
            // Update existing operator to ensure correct role
            DB::table('users')
                ->where('email', 'operator@fotoku.com')
                ->update(['role' => 'operator', 'status' => 'active']);
            $this->command->info('✓ Updated existing operator user');
        }

        // Verify the final counts
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $managerCount = DB::table('users')->where('role', 'manager')->count();
        $operatorCount = DB::table('users')->where('role', 'operator')->count();
        $customerCount = DB::table('users')->where('role', 'customer')->count();
        $totalCount = DB::table('users')->count();

        $this->command->info('');
        $this->command->info('Final user counts:');
        $this->command->info("- Admins: {$adminCount}");
        $this->command->info("- Managers: {$managerCount}");
        $this->command->info("- Operators: {$operatorCount}");
        $this->command->info("- Customers: {$customerCount}");
        $this->command->info("- Total users: {$totalCount}");
        
        if ($adminCount >= 1 && $managerCount >= 1 && $operatorCount >= 1) {
            $this->command->info('✅ Production user seeder completed successfully!');
        } else {
            $this->command->error('❌ Some staff users were not created properly. Check the database manually.');
        }
    }
}
