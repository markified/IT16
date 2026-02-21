<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Information Technology'],
            ['name' => 'Human Resources'],
            ['name' => 'Finance'],
            ['name' => 'Marketing'],
            ['name' => 'Sales'],
            ['name' => 'Research & Development'],
            ['name' => 'Operations'],
            ['name' => 'Customer Support'],
            ['name' => 'Legal'],
            ['name' => 'Administration'],
            ['name' => 'Product Management'],
            ['name' => 'Quality Assurance'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
