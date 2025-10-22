<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRole = Role::where('name', 'employee')->first();

        // 1. Super Admin - Melanie Flores
        $superAdmin = User::create([
            'name' => 'Melanie Flores',
            'email' => 'melanie.flores@feati.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_id' => $employeeRole->id,
            'status' => 'active',
        ]);

        EmployeeProfile::create([
            'user_id' => $superAdmin->id,
            'employee_number' => 'EMP-00001',
            'first_name' => 'Melanie',
            'last_name' => 'Flores',
            'date_of_birth' => fake()->date('Y-m-d', '-40 years'),
            'sex' => 'Female',
            'gender' => 'Female',
            'mobile' => fake()->numerify('09#########'),
            'current_address' => fake()->address(),
            'date_hired' => now()->subYears(15)->format('Y-m-d'),
            'position' => 'Super Admin',
            'department' => 'Administration',
        ]);

        // 2. Security - 15 members
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'status' => 'active',
            ]);

            EmployeeProfile::create([
                'user_id' => $user->id,
                'employee_number' => 'SEC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
                'date_of_birth' => fake()->date('Y-m-d', '-30 years'),
                'sex' => fake()->randomElement(['Male', 'Female']),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'mobile' => fake()->numerify('09#########'),
                'current_address' => fake()->address(),
                'date_hired' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'position' => 'Security',
                'department' => 'Security',
            ]);
        }

        // 3. OSA - 3 persons
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'status' => 'active',
            ]);

            EmployeeProfile::create([
                'user_id' => $user->id,
                'employee_number' => 'OSA-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
                'date_of_birth' => fake()->date('Y-m-d', '-30 years'),
                'sex' => fake()->randomElement(['Male', 'Female']),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'mobile' => fake()->numerify('09#########'),
                'current_address' => fake()->address(),
                'date_hired' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'position' => 'OSA',
                'department' => 'Office of Student Affairs',
            ]);
        }

        // 4. Department Heads - 6 persons
        $deptHeadDepts = ['College of Engineering', 'College of Maritime Education', 'College of Business', 'College of Architecture', 'School of Fine Arts', 'College of Arts, Sciences, and Education'];
        foreach ($deptHeadDepts as $dept) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'status' => 'active',
            ]);

            EmployeeProfile::create([
                'user_id' => $user->id,
                'employee_number' => 'DH-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
                'date_of_birth' => fake()->date('Y-m-d', '-35 years'),
                'sex' => fake()->randomElement(['Male', 'Female']),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'mobile' => fake()->numerify('09#########'),
                'current_address' => fake()->address(),
                'date_hired' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
                'position' => 'Department Head',
                'department' => $dept,
            ]);
        }

        // 5. Program Chairs
        $programChairs = [
            'College of Engineering' => 9,
            'College of Maritime Education' => 2,
            'College of Business' => 3,
            'College of Architecture' => 1,
            'School of Fine Arts' => 1,
            'College of Arts, Sciences, and Education' => 1,
        ];

        $chairCount = 1;
        foreach ($programChairs as $dept => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = User::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role_id' => $employeeRole->id,
                    'status' => 'active',
                ]);

                EmployeeProfile::create([
                    'user_id' => $user->id,
                    'employee_number' => 'PC-' . str_pad($chairCount, 4, '0', STR_PAD_LEFT),
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
                    'date_of_birth' => fake()->date('Y-m-d', '-35 years'),
                    'sex' => fake()->randomElement(['Male', 'Female']),
                    'gender' => fake()->randomElement(['Male', 'Female']),
                    'mobile' => fake()->numerify('09#########'),
                    'current_address' => fake()->address(),
                    'date_hired' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
                    'position' => 'Program Chair',
                    'department' => $dept,
                ]);
                $chairCount++;
            }
        }

        // 6. Teachers - remaining employees
        $teachersNeeded = 100 - (1 + 15 + 3 + 6 + 17); // Total - (Super Admin + Security + OSA + Dept Heads + Program Chairs)
        $departments = [
            'College of Engineering',
            'College of Maritime Education',
            'College of Business',
            'College of Architecture',
            'School of Fine Arts',
            'College of Arts, Sciences, and Education',
        ];

        for ($i = 0; $i < $teachersNeeded; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'status' => 'active',
            ]);

            EmployeeProfile::create([
                'user_id' => $user->id,
                'employee_number' => 'TCH-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
                'date_of_birth' => fake()->date('Y-m-d', '-35 years'),
                'sex' => fake()->randomElement(['Male', 'Female']),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'mobile' => fake()->numerify('09#########'),
                'current_address' => fake()->address(),
                'date_hired' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
                'position' => 'Teacher',
                'department' => fake()->randomElement($departments),
            ]);
        }

        $this->command->info('Created 100 employees with proper hierarchy successfully!');
    }
}
