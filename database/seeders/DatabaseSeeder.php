<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([ 
            RoleSeeder::class, 
            AdminUserSeeder::class,
            EmployeeSeeder::class,
            StudentSeeder::class,
            ViolationSeeder::class,
        ]);
    }
}

