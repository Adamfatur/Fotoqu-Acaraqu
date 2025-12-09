<?php

namespace Database\Seeders;

use App\Models\Photobox;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotoboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photoboxes = [
            [
                'code' => 'BOX-01',
                'name' => 'Photobox Studio Alpha',
                'description' => 'Main photobox di studio utama',
                'location' => 'Studio Utama - Lantai 1',
                'status' => 'active',
                'settings' => [
                    'camera' => [
                        'resolution' => '1920x1080',
                        'quality' => 95,
                        'flash' => true,
                    ],
                    'lighting' => [
                        'brightness' => 80,
                        'color_temperature' => 5500,
                    ],
                    'interface' => [
                        'language' => 'id',
                        'theme' => 'pastel',
                    ],
                ],
            ],
            [
                'code' => 'BOX-02',
                'name' => 'Photobox Studio Beta',
                'description' => 'Photobox cadangan di studio kedua',
                'location' => 'Studio Kedua - Lantai 1',
                'status' => 'active',
                'settings' => [
                    'camera' => [
                        'resolution' => '1920x1080',
                        'quality' => 95,
                        'flash' => true,
                    ],
                    'lighting' => [
                        'brightness' => 75,
                        'color_temperature' => 5500,
                    ],
                    'interface' => [
                        'language' => 'id',
                        'theme' => 'pastel',
                    ],
                ],
            ],
            [
                'code' => 'BOX-03',
                'name' => 'Photobox Mobile Gamma',
                'description' => 'Photobox portabel untuk event outdoor',
                'location' => 'Mobile Unit',
                'status' => 'inactive',
                'settings' => [
                    'camera' => [
                        'resolution' => '1920x1080',
                        'quality' => 90,
                        'flash' => true,
                    ],
                    'lighting' => [
                        'brightness' => 85,
                        'color_temperature' => 5000,
                    ],
                    'interface' => [
                        'language' => 'id',
                        'theme' => 'pastel',
                    ],
                ],
            ],
        ];

        foreach ($photoboxes as $photobox) {
            Photobox::create($photobox);
        }
    }
}
