<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        
        $this->call([
            // Priority 1: Core system users
            AdminSeeder::class,
            ProductionUserSeeder::class, // Backup user seeder for production
            
            // Priority 2: Core system data
            PhotoboxSeeder::class,
            SettingSeeder::class,
            PackageSeeder::class,
            FrameTemplateSeeder::class,
            EmailTemplateSeeder::class,
            
            // Priority 3: Test data (optional)
            UserRoleSeeder::class, // Additional test users
            // DemoDataSeeder::class, // Uncomment for demo data
        ]);
        
        $this->command->info('Database seeding completed!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('- Admin: admin@fotoku.com / admin123');
        $this->command->info('- Manager: manager@fotoku.com / manager123');
        $this->command->info('- Operator: operator@fotoku.com / operator123');
    }
}
