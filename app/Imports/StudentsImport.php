<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    private $rowCount = 0;

    public function model(array $row)
    {
        $studentRole = Role::where('name', 'student')->first();

        if (!$studentRole) {
            throw new \Exception('Student role not found in database. Please run seeders first.');
        }

        $this->rowCount++;

        // Create the user first
        $user = User::create([
            'name'              => trim($row['first_name'] . ' ' . $row['last_name']),
            'email'             => $row['email'],
            'role_id'           => $studentRole->id,
            'password'          => Hash::make($row['password'] ?? 'password123'),
            'email_verified_at' => now(),
            'status'            => 'active',
        ]);

        // Create the student profile
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
            'current_address' => $row['address'] ?? $row['current_address'] ?? null,
        ]);

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
            'student_id' => 'nullable|string',
            'student_number' => 'nullable|string',
            'program' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => 'The email :input is already registered.',
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
