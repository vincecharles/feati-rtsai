<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    private $rowCount = 0;

    public function model(array $row)
    {
        $role = Role::where('name', $row['role'] ?? 'student')->first();

        if (!$role) {
            throw new \Exception("Role '{$row['role']}' not found in database.");
        }

        $this->rowCount++;

        // Create the user
        $user = User::create([
            'name'              => trim($row['first_name'] . ' ' . $row['last_name']),
            'email'             => $row['email'],
            'role_id'           => $role->id,
            'password'          => Hash::make($row['password'] ?? 'password123'),
            'email_verified_at' => now(),
            'status'            => 'active',
        ]);

        // Create appropriate profile based on role
        if ($role->name === 'student') {
            StudentProfile::create([
                'user_id'         => $user->id,
                'student_number'  => $row['student_id'] ?? $row['student_number'] ?? null,
                'first_name'      => $row['first_name'],
                'last_name'       => $row['last_name'],
                'middle_name'     => $row['middle_name'] ?? null,
                'program'         => $row['program'] ?? null,
                'course'          => $row['course'] ?? $row['program'] ?? null,
                'year_level'      => $row['year_level'] ?? null,
                'department'      => $row['department'] ?? null,
                'sex'             => $row['sex'] ?? null,
                'mobile'          => $row['mobile'] ?? $row['phone'] ?? null,
            ]);
        } else {
            // Create employee profile for non-student roles
            EmployeeProfile::create([
                'user_id'         => $user->id,
                'employee_number' => $row['employee_id'] ?? $row['employee_number'] ?? null,
                'first_name'      => $row['first_name'],
                'last_name'       => $row['last_name'],
                'middle_name'     => $row['middle_name'] ?? null,
                'department'      => $row['department'] ?? null,
                'position'        => $row['position'] ?? $role->label,
                'sex'             => $row['sex'] ?? null,
                'mobile'          => $row['mobile'] ?? $row['phone'] ?? null,
            ]);
        }

        return null; // Return null since we're handling creation manually
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'department_head', 'program_head', 'security', 'osa', 'teacher', 'student']),
            ],
            'student_id' => 'nullable|string',
            'student_number' => 'nullable|string',
            'employee_id' => 'nullable|string',
            'employee_number' => 'nullable|string',
            'program' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => 'The email :input is already registered.',
            'role.in' => 'The role :input is not valid.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
