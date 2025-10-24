<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;

class ViolationSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $students = User::where('role_id', $studentRole->id)->limit(30)->get();
        
        // Get any employees (teachers, security, etc.) as reporters
        $reporters = User::whereHas('role', function($q) {
            $q->whereIn('name', ['super_admin', 'teacher', 'security', 'osa']);
        })->limit(10)->get();

        $violationTypes = [
            'Uniform Violation',
            'Late Attendance',
            'Disruptive Behavior',
            'Academic Dishonesty',
            'Unauthorized Absence',
            'Damage to Property',
            'Bullying',
            'Use of Mobile Phone in Class',
            'Littering',
            'Disrespect to Faculty',
        ];

        $levels = ['Level 1', 'Level 2', 'Level 3', 'Expulsion'];
        $levelDescriptions = [
            'Level 1' => 'Minor infraction requiring verbal warning and parent notification.',
            'Level 2' => 'Moderate offense requiring written warning and disciplinary action.',
            'Level 3' => 'Serious violation requiring suspension and comprehensive review.',
            'Expulsion' => 'Critical offense resulting in permanent dismissal from institution.',
        ];

        $statuses = ['pending', 'under_review', 'resolved', 'dismissed'];

        foreach ($students as $index => $student) {
            if ($index < 20) { // Create 20 violations
                $level = fake()->randomElement($levels);
                $hasResolution = fake()->boolean(40);
                
                Violation::create([
                    'student_id' => $student->id,
                    'reported_by' => $reporters->random()->id,
                    'violation_type' => fake()->randomElement($violationTypes),
                    'level' => $level,
                    'description' => $levelDescriptions[$level],
                    'status' => fake()->randomElement($statuses),
                    'severity' => $this->getLevelSeverity($level),
                    'violation_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                    'action_taken' => fake()->boolean(60) ? fake()->sentence(10) : null,
                    'resolution_date' => $hasResolution ? fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d') : null,
                    'notes' => fake()->boolean(50) ? fake()->sentence(8) : null,
                ]);
            }
        }

        $this->command->info('Created 20 violation records with levels successfully!');
    }

    private function getLevelSeverity($level)
    {
        return match($level) {
            'Level 1' => 'minor',
            'Level 2' => 'moderate',
            'Level 3' => 'major',
            'Expulsion' => 'severe',
        };
    }
}
