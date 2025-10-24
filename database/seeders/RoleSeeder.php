<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'super_admin', 'label' => 'Super Administrator'],
            ['name' => 'department_head', 'label' => 'Department Head'],
            ['name' => 'program_head', 'label' => 'Program Head'],
            ['name' => 'security', 'label' => 'Security Personnel'],
            ['name' => 'osa', 'label' => 'Office of Student Affairs'],
            ['name' => 'teacher', 'label' => 'Teacher/Faculty'],
            ['name' => 'student', 'label' => 'Student'],
        ] as $r) {
            Role::firstOrCreate(['name' => $r['name']], ['label' => $r['label']]);
        }
    }
}