<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@fotoku.com'],
            [
                'name' => 'Admin Fotoku',
                'email' => 'admin@fotoku.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        // Create operator user
        User::updateOrCreate(
            ['email' => 'operator@fotoku.com'],
            [
                'name' => 'Operator Fotoku',
                'email' => 'operator@fotoku.com',
                'password' => Hash::make('password123'),
                'role' => 'operator'
            ]
        );

        $this->command->info('Admin users created:');
        $this->command->info('Admin: admin@fotoku.com / password123');
        $this->command->info('Operator: operator@fotoku.com / password123');
    }
}
