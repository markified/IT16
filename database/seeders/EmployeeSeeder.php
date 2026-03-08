<?php

namespace Database\Seeders;

use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create employees
        $employees = [
            ['name' => 'James Wilson', 'contact_number' => '555-123-4567'],
            ['name' => 'Sarah Chen', 'contact_number' => '555-234-5678'],
            ['name' => 'Michael Rodriguez', 'contact_number' => '555-345-6789'],
            ['name' => 'Emily Johnson', 'contact_number' => '555-456-7890'],
            ['name' => 'David Thompson', 'contact_number' => '555-567-8901'],
            ['name' => 'Jessica Williams', 'contact_number' => '555-678-9012'],
            ['name' => 'Robert Brown', 'contact_number' => '555-789-0123'],
            ['name' => 'Lisa Martinez', 'contact_number' => '555-890-1234'],
            ['name' => 'Andrew Davis', 'contact_number' => '555-901-2345'],
            ['name' => 'Samantha Wilson', 'contact_number' => '555-012-3456'],
            ['name' => 'John Smith', 'contact_number' => '555-123-7890'],
            ['name' => 'Jennifer Garcia', 'contact_number' => '555-234-8901'],
            ['name' => 'Thomas Lee', 'contact_number' => '555-345-9012'],
            ['name' => 'Michelle Chang', 'contact_number' => '555-456-0123'],
            ['name' => 'Christopher Kim', 'contact_number' => '555-567-1234'],
            ['name' => 'Amanda Taylor', 'contact_number' => '555-678-2345'],
            ['name' => 'Kevin Patel', 'contact_number' => '555-789-3456'],
            ['name' => 'Stephanie Nguyen', 'contact_number' => '555-890-4567'],
            ['name' => 'Daniel Jackson', 'contact_number' => '555-901-5678'],
            ['name' => 'Rachel Green', 'contact_number' => '555-012-6789'],
            ['name' => 'Mark Johnson', 'contact_number' => '555-123-7891'],
            ['name' => 'Olivia Adams', 'contact_number' => '555-234-8902'],
            ['name' => 'William Clark', 'contact_number' => '555-345-9013'],
            ['name' => 'Sophia Rodriguez', 'contact_number' => '555-456-0124'],
            ['name' => 'Alexander White', 'contact_number' => '555-567-1235'],
            ['name' => 'Natalie Singh', 'contact_number' => '555-678-2346'],
            ['name' => 'Tyler Robinson', 'contact_number' => '555-789-3457'],
        ];

        // Create additional employees with random data
        for ($i = 0; $i < 10; $i++) {
            $employees[] = [
                'name' => $faker->name,
                'contact_number' => $faker->numerify('555-###-####'),
            ];
        }

        // Insert all employees
        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
