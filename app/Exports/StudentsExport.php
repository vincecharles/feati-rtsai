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
        })->with(['role', 'studentProfile'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Number',
            'First Name',
            'Last Name',
            'Middle Name',
            'Email',
            'Program',
            'Course',
            'Year Level',
            'Department',
            'Mobile',
            'Sex',
            'Status',
            'Created At',
        ];
    }

    public function map($student): array
    {
        $profile = $student->studentProfile;
        
        return [
            $student->id,
            $profile->student_number ?? 'N/A',
            $profile->first_name ?? 'N/A',
            $profile->last_name ?? 'N/A',
            $profile->middle_name ?? '',
            $student->email,
            $profile->program ?? 'N/A',
            $profile->course ?? 'N/A',
            $profile->year_level ?? 'N/A',
            $profile->department ?? 'N/A',
            $profile->mobile ?? 'N/A',
            $profile->sex ?? 'N/A',
            $student->status ?? ($student->email_verified_at ? 'Active' : 'Inactive'),
            $student->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 18,
            'D' => 18,
            'E' => 15,
            'F' => 30,
            'G' => 30,
            'H' => 30,
            'I' => 12,
            'J' => 30,
            'K' => 15,
            'L' => 10,
            'M' => 12,
            'N' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
