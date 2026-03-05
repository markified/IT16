<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Processors',
                'code' => 'CPU',
                'description' => 'Central Processing Units - Intel, AMD processors',
                'icon' => 'fas fa-microchip',
                'is_active' => true,
            ],
            [
                'name' => 'Graphics Cards',
                'code' => 'GPU',
                'description' => 'Video cards and graphics processing units',
                'icon' => 'fas fa-tv',
                'is_active' => true,
            ],
            [
                'name' => 'Memory',
                'code' => 'RAM',
                'description' => 'DDR4, DDR5 RAM modules and memory kits',
                'icon' => 'fas fa-memory',
                'is_active' => true,
            ],
            [
                'name' => 'Storage',
                'code' => 'STO',
                'description' => 'SSDs, HDDs, NVMe drives and storage solutions',
                'icon' => 'fas fa-hdd',
                'is_active' => true,
            ],
            [
                'name' => 'Motherboards',
                'code' => 'MOB',
                'description' => 'ATX, Micro-ATX, Mini-ITX motherboards',
                'icon' => 'fas fa-server',
                'is_active' => true,
            ],
            [
                'name' => 'Power Supplies',
                'code' => 'PSU',
                'description' => 'ATX power supplies and modular PSUs',
                'icon' => 'fas fa-plug',
                'is_active' => true,
            ],
            [
                'name' => 'Cooling',
                'code' => 'COL',
                'description' => 'CPU coolers, AIO liquid cooling, case fans',
                'icon' => 'fas fa-fan',
                'is_active' => true,
            ],
            [
                'name' => 'Cases',
                'code' => 'CAS',
                'description' => 'PC cases, towers, and enclosures',
                'icon' => 'fas fa-cube',
                'is_active' => true,
            ],
            [
                'name' => 'Monitors',
                'code' => 'MON',
                'description' => 'Gaming monitors, professional displays',
                'icon' => 'fas fa-desktop',
                'is_active' => true,
            ],
            [
                'name' => 'Peripherals',
                'code' => 'PER',
                'description' => 'Keyboards, mice, headsets, and accessories',
                'icon' => 'fas fa-keyboard',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
