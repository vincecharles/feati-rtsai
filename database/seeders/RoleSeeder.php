<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'hr', 'label' => 'HR Staff'],
            ['name' => 'employee', 'label' => 'Employee'],
            ['name' => 'student', 'label' => 'Student'], // Add this line
        ] as $r) {
            Role::firstOrCreate(['name' => $r['name']], ['label' => $r['label']]);
        }
    }
}