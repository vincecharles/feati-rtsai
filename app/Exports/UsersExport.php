<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    public function collection()
    {
        return User::with(['role', 'studentProfile', 'profile'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee/Student Number',
            'First Name',
            'Last Name',
            'Middle Name',
            'Email',
            'Role',
            'Department',
            'Program/Position',
            'Year Level',
            'Mobile',
            'Status',
            'Created At',
        ];
    }

    public function map($user): array
    {
        $isStudent = $user->role?->name === 'student';
        $profile = $isStudent ? $user->studentProfile : $user->profile;
        
        return [
            $user->id,
            $isStudent ? ($profile->student_number ?? 'N/A') : ($profile->employee_number ?? 'N/A'),
            $profile->first_name ?? 'N/A',
            $profile->last_name ?? 'N/A',
            $profile->middle_name ?? '',
            $user->email,
            $user->role->label ?? 'N/A',
            $profile->department ?? 'N/A',
            $isStudent ? ($profile->program ?? 'N/A') : ($profile->position ?? 'N/A'),
            $isStudent ? ($profile->year_level ?? 'N/A') : 'N/A',
            $profile->mobile ?? 'N/A',
            $user->status ?? ($user->email_verified_at ? 'Active' : 'Inactive'),
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 20,
            'C' => 18,
            'D' => 18,
            'E' => 15,
            'F' => 30,
            'G' => 25,
            'H' => 30,
            'I' => 30,
            'J' => 12,
            'K' => 15,
            'L' => 12,
            'M' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
