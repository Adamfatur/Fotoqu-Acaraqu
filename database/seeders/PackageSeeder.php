<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket Fotostrip Standard',
                'description' => 'Paket standard dengan 6 slot fotostrip 4x6 inch. User memilih 3 foto terbaik dari 3 foto yang diambil, lalu diduplikasi menjadi 6 slot (3 kiri + 3 kanan).',
                'frame_slots' => '6',
                'price' => 35000,
                'discount_price' => null,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1
            ]
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
    }
}
