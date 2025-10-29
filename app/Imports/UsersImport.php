<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
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

        $userData = [
            'first_name'   => $row['first_name'],
            'last_name'    => $row['last_name'],
            'email'        => $row['email'],
            'role_id'      => $role->id,
            'password'     => Hash::make($row['password'] ?? 'password123'),
            'email_verified_at' => now(),
        ];

        if ($role->name === 'student') {
            $userData['student_id'] = $row['student_id'] ?? null;
            $userData['program'] = $row['program'] ?? null;
            $userData['year_level'] = $row['year_level'] ?? null;
        } else {
            $userData['employee_id'] = $row['employee_id'] ?? null;
        }

        $userData['department'] = $row['department'] ?? null;

        return new User($userData);
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
            'employee_id' => 'nullable|string',
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
