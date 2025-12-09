<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Running AdminSeeder...');

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@fotoku.com'],
            [
                'name' => 'Admin Fotoku',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081234567890',
                'notes' => 'Super admin with full access'
            ]
        );
        $this->command->info('✓ Admin user processed: admin@fotoku.com');

        // Create operator user
        $operator = User::updateOrCreate(
            ['email' => 'operator@fotoku.com'],
            [
                'name' => 'Operator Fotoku',
                'email_verified_at' => now(),
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'status' => 'active',
                'phone' => '081234567891',
                'notes' => 'Operator with photobox access'
            ]
        );
        $this->command->info('✓ Operator user processed: operator@fotoku.com');

        // Create manager user
        $manager = User::updateOrCreate(
            ['email' => 'manager@fotoku.com'],
            [
                'name' => 'Manager Fotoku',
                'email_verified_at' => now(),
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'status' => 'active',
                'phone' => '081234567892',
                'notes' => 'Manager with session and customer management access'
            ]
        );
        $this->command->info('✓ Manager user processed: manager@fotoku.com');

        // Display final counts
        $this->command->info('');
        $this->command->info('User counts after AdminSeeder:');
        $this->command->info('- Admins: ' . DB::table('users')->where('role', 'admin')->count());
        $this->command->info('- Managers: ' . DB::table('users')->where('role', 'manager')->count());
        $this->command->info('- Operators: ' . DB::table('users')->where('role', 'operator')->count());
        $this->command->info('- Total users: ' . DB::table('users')->count());
    }
}
