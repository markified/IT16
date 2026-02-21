<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all department IDs
        $departmentIds = Department::pluck('id')->toArray();

        // Create 30 employees distributed across departments
        $employees = [
            // IT Department employees
            [
                'name' => 'James Wilson',
                'contact_number' => '555-123-4567',
                'department_id' => 1, // Information Technology
            ],
            [
                'name' => 'Sarah Chen',
                'contact_number' => '555-234-5678',
                'department_id' => 1, // Information Technology
            ],
            [
                'name' => 'Michael Rodriguez',
                'contact_number' => '555-345-6789',
                'department_id' => 1, // Information Technology
            ],

            // HR Department employees
            [
                'name' => 'Emily Johnson',
                'contact_number' => '555-456-7890',
                'department_id' => 2, // Human Resources
            ],
            [
                'name' => 'David Thompson',
                'contact_number' => '555-567-8901',
                'department_id' => 2, // Human Resources
            ],

            // Finance Department employees
            [
                'name' => 'Jessica Williams',
                'contact_number' => '555-678-9012',
                'department_id' => 3, // Finance
            ],
            [
                'name' => 'Robert Brown',
                'contact_number' => '555-789-0123',
                'department_id' => 3, // Finance
            ],
            [
                'name' => 'Lisa Martinez',
                'contact_number' => '555-890-1234',
                'department_id' => 3, // Finance
            ],

            // Marketing Department employees
            [
                'name' => 'Andrew Davis',
                'contact_number' => '555-901-2345',
                'department_id' => 4, // Marketing
            ],
            [
                'name' => 'Samantha Wilson',
                'contact_number' => '555-012-3456',
                'department_id' => 4, // Marketing
            ],

            // Sales Department employees
            [
                'name' => 'John Smith',
                'contact_number' => '555-123-7890',
                'department_id' => 5, // Sales
            ],
            [
                'name' => 'Jennifer Garcia',
                'contact_number' => '555-234-8901',
                'department_id' => 5, // Sales
            ],
            [
                'name' => 'Thomas Lee',
                'contact_number' => '555-345-9012',
                'department_id' => 5, // Sales
            ],

            // R&D Department employees
            [
                'name' => 'Michelle Chang',
                'contact_number' => '555-456-0123',
                'department_id' => 6, // Research & Development
            ],
            [
                'name' => 'Christopher Kim',
                'contact_number' => '555-567-1234',
                'department_id' => 6, // Research & Development
            ],

            // Operations Department employees
            [
                'name' => 'Amanda Taylor',
                'contact_number' => '555-678-2345',
                'department_id' => 7, // Operations
            ],
            [
                'name' => 'Kevin Patel',
                'contact_number' => '555-789-3456',
                'department_id' => 7, // Operations
            ],
            [
                'name' => 'Stephanie Nguyen',
                'contact_number' => '555-890-4567',
                'department_id' => 7, // Operations
            ],

            // Customer Support Department employees
            [
                'name' => 'Daniel Jackson',
                'contact_number' => '555-901-5678',
                'department_id' => 8, // Customer Support
            ],
            [
                'name' => 'Rachel Green',
                'contact_number' => '555-012-6789',
                'department_id' => 8, // Customer Support
            ],

            // Legal Department employees
            [
                'name' => 'Mark Johnson',
                'contact_number' => '555-123-7890',
                'department_id' => 9, // Legal
            ],

            // Administration Department employees
            [
                'name' => 'Olivia Adams',
                'contact_number' => '555-234-8901',
                'department_id' => 10, // Administration
            ],
            [
                'name' => 'William Clark',
                'contact_number' => '555-345-9012',
                'department_id' => 10, // Administration
            ],

            // Product Management Department employees
            [
                'name' => 'Sophia Rodriguez',
                'contact_number' => '555-456-0123',
                'department_id' => 11, // Product Management
            ],
            [
                'name' => 'Alexander White',
                'contact_number' => '555-567-1234',
                'department_id' => 11, // Product Management
            ],

            // Quality Assurance Department employees
            [
                'name' => 'Natalie Singh',
                'contact_number' => '555-678-2345',
                'department_id' => 12, // Quality Assurance
            ],
            [
                'name' => 'Tyler Robinson',
                'contact_number' => '555-789-3456',
                'department_id' => 12, // Quality Assurance
            ]
        ];

        // Create additional employees with random data for each department
        for ($i = 0; $i < 10; $i++) {
            $employees[] = [
                'name' => $faker->name,
                'contact_number' => $faker->numerify('555-###-####'),
                'department_id' => $faker->randomElement($departmentIds),
            ];
        }

        // Insert all employees
        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
