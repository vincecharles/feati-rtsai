<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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
                
                User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role_id' => $studentRole->id,
                    'status' => 'active',
                    'student_id' => date('Y') . '-' . str_pad($studentNumber, 4, '0', STR_PAD_LEFT),
                    'program' => $program,
                    'year_level' => fake()->randomElement($yearLevels),
                ]);
            }
        }

        $this->command->info('Created 200 students (25 per program) successfully!');
    }
}
