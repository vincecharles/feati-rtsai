<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
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

        return new User([
            'first_name'   => $row['first_name'],
            'last_name'    => $row['last_name'],
            'email'        => $row['email'],
            'student_id'   => $row['student_id'] ?? null,
            'program'      => $row['program'] ?? null,
            'year_level'   => $row['year_level'] ?? null,
            'department'   => $row['department'] ?? null,
            'role_id'      => $studentRole->id,
            'password'     => Hash::make($row['password'] ?? 'password123'),
            'email_verified_at' => now(),
        ]);
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
            'student_id' => [
                'nullable',
                'string',
                Rule::unique('users', 'student_id'),
            ],
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
            'student_id.unique' => 'The student ID :input already exists.',
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
