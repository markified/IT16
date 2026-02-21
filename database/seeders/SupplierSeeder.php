<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'TechComponents Inc',
                'contact_number' => '800-555-1234',
                'email' => 'sales@techcomponents.com',
            ],
            [
                'name' => 'Global PC Parts',
                'contact_number' => '800-555-2345',
                'email' => 'orders@globalpcparts.com',
            ],
            [
                'name' => 'NextGen Computer Supplies',
                'contact_number' => '800-555-3456',
                'email' => 'info@nextgencomputer.com',
            ],
            [
                'name' => 'Digital Hardware Solutions',
                'contact_number' => '800-555-4567',
                'email' => 'support@digitalhardware.com',
            ],
            [
                'name' => 'ElectraTech Distributors',
                'contact_number' => '800-555-5678',
                'email' => 'sales@electratech.com',
            ],
            [
                'name' => 'Prime Computing',
                'contact_number' => '800-555-6789',
                'email' => 'orders@primecomputing.com',
            ],
            [
                'name' => 'Computer Parts Wholesale',
                'contact_number' => '800-555-7890',
                'email' => 'wholesale@computerparts.com',
            ],
            [
                'name' => 'Server Components Ltd',
                'contact_number' => '800-555-8901',
                'email' => 'enterprise@servercomponents.com',
            ],
            [
                'name' => 'PC Builder Supply Co',
                'contact_number' => '800-555-9012',
                'email' => 'sales@pcbuildersupply.com',
            ],
            [
                'name' => 'Tech Innovations Supply',
                'contact_number' => '800-555-0123',
                'email' => 'innovations@techsupply.com',
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
