<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\SettingController;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $controller = new SettingController();
        $controller->createDefaultSettings();
    }
}
