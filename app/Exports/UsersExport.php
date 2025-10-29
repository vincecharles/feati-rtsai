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
        return User::with('role')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee/Student ID',
            'First Name',
            'Last Name',
            'Email',
            'Role',
            'Department',
            'Program',
            'Year Level',
            'Status',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->employee_id ?? $user->student_id ?? 'N/A',
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->role->label ?? 'N/A',
            $user->department ?? 'N/A',
            $user->program ?? 'N/A',
            $user->year_level ?? 'N/A',
            $user->email_verified_at ? 'Active' : 'Inactive',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 25,
            'G' => 30,
            'H' => 30,
            'I' => 12,
            'J' => 12,
            'K' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
