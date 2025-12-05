<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'student')->first();

        $programs = [
            'BS Computer Science',
            'BS Information Technology',
            'BS Mechanical Engineering',
            'BS Civil Engineering',
            'BS Electrical Engineering',
            'BS Architecture',
            'BS Business Administration',
            'BS Accountancy',
        ];

        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        // Distribute 200 students across 8 programs (25 students per program)
        $studentsPerProgram = 25;

        foreach ($programs as $programIndex => $program) {
            for ($i = 0; $i < $studentsPerProgram; $i++) {
                $firstName = fake()->firstName();
                $lastName = fake()->lastName();
                $studentNumber = ($programIndex * $studentsPerProgram) + $i + 1;
                
                $user = User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role_id' => $studentRole->id,
                    'status' => 'active',
                ]);

                StudentProfile::create([
                    'user_id' => $user->id,
                    'student_number' => date('Y') . '-' . str_pad($studentNumber, 4, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'program' => $program,
                    'year_level' => fake()->randomElement($yearLevels),
                    'sex' => fake()->randomElement(['Male', 'Female']),
                    'date_of_birth' => fake()->date('Y-m-d', '-20 years'),
                    'mobile' => fake()->numerify('09#########'),
                    'current_address' => fake()->address(),
                    'enrollment_date' => fake()->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
                ]);
            }
        }

        $this->command->info('Created 200 students (25 per program) successfully!');
    }
}
