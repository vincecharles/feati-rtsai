<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([ 
            RoleSeeder::class, 
            AdminUserSeeder::class,
            ViolationTypesSeeder::class,
            // Removed faker seeders - users created through system
            // EmployeeSeeder::class,
            // StudentSeeder::class,
            // ViolationSeeder::class,
        ]);
    }
}

