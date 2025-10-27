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
        $superAdminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $securityRole = Role::where('name', 'security')->first();
        $osaRole = Role::where('name', 'osa')->first();
        $departmentHeadRole = Role::where('name', 'department_head')->first();
        $programHeadRole = Role::where('name', 'program_head')->first();

        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $securityRole->id ?? $teacherRole->id,
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
                'role_id' => $osaRole->id ?? $teacherRole->id,
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

        $deptHeadDepts = ['College of Engineering', 'College of Maritime Education', 'College of Business', 'College of Architecture', 'School of Fine Arts', 'College of Arts, Sciences, and Education'];
        foreach ($deptHeadDepts as $dept) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $departmentHeadRole->id ?? $teacherRole->id,
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
                    'role_id' => $programHeadRole->id ?? $teacherRole->id,
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

        $teachersNeeded = 100 - (15 + 3 + 6 + 17);
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
                'role_id' => $teacherRole->id,
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

        $this->command->info('Created 99 employees with proper hierarchy successfully!');
        $this->command->info('Note: Super Admin (admin@gmail.com) is created by AdminUserSeeder');
    }
}
