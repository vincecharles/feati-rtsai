<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\StudentProfile;

return new class extends Migration
{
    public function up(): void
    {
        $students = User::with('profile')
            ->whereHas('role', function($q) {
                $q->where('name', 'student');
            })
            ->whereHas('profile')
            ->get();

        foreach ($students as $student) {
            if ($student->profile && $student->profile->employee_number) {
                StudentProfile::create([
                    'user_id' => $student->id,
                    'student_number' => $student->profile->employee_number,
                    'enrollment_date' => $student->profile->date_hired,
                    'last_name' => $student->profile->last_name,
                    'first_name' => $student->profile->first_name,
                    'middle_name' => $student->profile->middle_name,
                    'suffix' => $student->profile->suffix,
                    'sex' => $student->profile->sex,
                    'date_of_birth' => $student->profile->date_of_birth,
                    'place_of_birth' => $student->profile->place_of_birth,
                    'civil_status' => $student->profile->civil_status,
                    'nationality' => $student->profile->nationality,
                    'mobile' => $student->profile->mobile,
                    'current_address' => $student->profile->current_address,
                    'permanent_address' => $student->profile->permanent_address,
                    'emergency_name' => $student->profile->emergency_name,
                    'emergency_relationship' => $student->profile->emergency_relationship,
                    'emergency_phone' => $student->profile->emergency_phone,
                    'emergency_address' => $student->profile->emergency_address,
                    'department' => $student->profile->department,
                    'program' => DB::table('employee_profiles')->where('id', $student->profile->id)->value('program'),
                    'course' => DB::table('employee_profiles')->where('id', $student->profile->id)->value('course'),
                    'year_level' => DB::table('employee_profiles')->where('id', $student->profile->id)->value('year_level'),
                ]);

                $student->profile->delete();
            }
        }
    }

    public function down(): void
    {
    }
};
