<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    public function collection()
    {
        return User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->with('role')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student ID',
            'First Name',
            'Last Name',
            'Email',
            'Program',
            'Year Level',
            'Department',
            'Status',
            'Created At',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->student_id ?? 'N/A',
            $student->first_name,
            $student->last_name,
            $student->email,
            $student->program ?? 'N/A',
            $student->year_level ?? 'N/A',
            $student->department ?? 'N/A',
            $student->email_verified_at ? 'Active' : 'Inactive',
            $student->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 30,
            'G' => 12,
            'H' => 30,
            'I' => 12,
            'J' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
