<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        $adminRole = Role::firstOrCreate(['name'=>'admin'], ['label'=>'Administrator']);
        if (! User::where('email','admin@gmail.com')->exists()) {
            User::factory()->admin($adminRole)->create();
        }
    }
}

