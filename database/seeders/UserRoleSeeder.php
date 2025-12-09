<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting User Role Seeder...');
        
        // Create Admin user if not exists
        if (!User::where('email', 'admin@fotoku.com')->exists()) {
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'customer', // Create with default role first
                'status' => 'active',
                'phone' => '081234567890',
                'notes' => 'Super admin with full access'
            ]);
            // Then update the role directly
            $admin->update(['role' => 'admin']);
            $this->command->info('✓ Created admin user: admin@fotoku.com');
        } else {
            $this->command->info('- Admin user already exists');
        }

        // Create Manager user if not exists
        if (!User::where('email', 'manager@fotoku.com')->exists()) {
            $manager = User::create([
                'name' => 'Manager Fotoku',
                'email' => 'manager@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'customer', // Create with default role first
                'status' => 'active',
                'phone' => '081234567891',
                'notes' => 'Manager with session and customer management access'
            ]);
            // Then update the role directly
            $manager->update(['role' => 'manager']);
            $this->command->info('✓ Created manager user: manager@fotoku.com');
        } else {
            $this->command->info('- Manager user already exists');
        }

        // Create Operator user if not exists
        if (!User::where('email', 'operator@fotoku.com')->exists()) {
            $operator = User::create([
                'name' => 'Operator Photobox',
                'email' => 'operator@fotoku.com',
                'password' => Hash::make('password'),
                'role' => 'customer', // Create with default role first
                'status' => 'active',
                'phone' => '081234567892',
                'notes' => 'Operator with photobox operation access'
            ]);
            // Then update the role directly
            $operator->update(['role' => 'operator']);
            $this->command->info('✓ Created operator user: operator@fotoku.com');
        } else {
            $this->command->info('- Operator user already exists');
        }

        // Create some Customer users
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'customer',
                'phone' => '081234567893',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'customer',
                'phone' => '081234567894',
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'role' => 'customer',
                'phone' => '081234567895',
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'role' => 'customer',
                'phone' => '081234567896',
            ],
        ];

        foreach ($customers as $customer) {
            if (!User::where('email', $customer['email'])->exists()) {
                User::create([
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'password' => Hash::make('password'),
                    'role' => $customer['role'],
                    'status' => 'active',
                    'phone' => $customer['phone'],
                    'notes' => 'Test customer user'
                ]);
                $this->command->info('✓ Created customer: ' . $customer['email']);
            }
        }

        // Create one banned customer for testing
        if (!User::where('email', 'banned@example.com')->exists()) {
            $bannedUser = User::create([
                'name' => 'Banned User',
                'email' => 'banned@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active', // Create as active first
                'phone' => '081234567897',
                'notes' => 'Test banned customer'
            ]);
            // Then update status to banned
            $bannedUser->update(['status' => 'banned']);
            $this->command->info('✓ Created banned customer: banned@example.com');
        } else {
            $this->command->info('- Banned customer already exists');
        }

        // Verify the roles were set correctly
        $this->command->info('');
        $this->command->info('Final user counts:');
        $this->command->info('- Admins: ' . User::where('role', 'admin')->count());
        $this->command->info('- Managers: ' . User::where('role', 'manager')->count());
        $this->command->info('- Operators: ' . User::where('role', 'operator')->count());
        $this->command->info('- Customers: ' . User::where('role', 'customer')->count());
        $this->command->info('- Total users: ' . User::count());

        $this->command->info('User roles seeder completed!');
    }
}
