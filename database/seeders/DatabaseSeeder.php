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

        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Call the other seeders
        $this->call([
            SupplierSeeder::class,
            ProductSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,

        ]);
    }
}
