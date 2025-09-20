<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder {
    public function run(): void {
        foreach ([
            ['name'=>'admin', 'label'=>'Administrator'],
            ['name'=>'hr', 'label'=>'HR Staff'],
            ['name'=>'employee', 'label'=>'Employee'],
        ] as $r) {
            Role::firstOrCreate(['name'=>$r['name']], ['label'=>$r['label']]);
        }
    }
}
