<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Report;
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
        // User::factory(10)->create();

        if (!User::where('email', 'balmesmarkclarence@gmail.com')->exists()) {
            User::create([
                'name' => 'Superadmin',
                'email' => 'balmesmarkclarence@gmail.com',
                'password' => bcrypt('SuperAdmin01'),
                'role' => 'superadmin',
            ]);
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Admin01'),
                'role' => 'superadmin',
            ]);
        }

        // Call the other seeders
        $this->call([
            SupplierSeeder::class,
            CategorySeeder::class,  // Categories must be seeded before products
            ProductSeeder::class,
            EmployeeSeeder::class,

        ]);
    }
}
