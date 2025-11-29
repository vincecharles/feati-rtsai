<?php

namespace App\Exports;

use App\Models\Violation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ViolationsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    public function collection()
    {
        return Violation::with(['student.studentProfile', 'reporter.profile', 'reporter.studentProfile'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Student Number',
            'Department',
            'Program',
            'Offense Category',
            'Violation Type',
            'Sanction',
            'Description',
            'Reported By',
            'Violation Date',
            'Status',
            'Action Taken',
            'Resolution Date',
            'Created At',
        ];
    }

    public function map($violation): array
    {
        $studentProfile = $violation->student?->studentProfile;
        $reporterProfile = $violation->reporter?->profile ?? $violation->reporter?->studentProfile;
        
        $studentName = 'N/A';
        if ($studentProfile) {
            $studentName = trim($studentProfile->first_name . ' ' . $studentProfile->last_name);
        } elseif ($violation->student) {
            $studentName = $violation->student->name;
        }
        
        $reporterName = 'N/A';
        if ($reporterProfile) {
            $reporterName = trim($reporterProfile->first_name . ' ' . $reporterProfile->last_name);
        } elseif ($violation->reporter) {
            $reporterName = $violation->reporter->name;
        }
        
        return [
            $violation->id,
            $studentName,
            $studentProfile->student_number ?? 'N/A',
            $studentProfile->department ?? 'N/A',
            $studentProfile->program ?? 'N/A',
            ucfirst($violation->offense_category ?? 'N/A'),
            $violation->violation_type ?? 'N/A',
            $violation->sanction ?? 'Pending',
            $violation->description ?? 'N/A',
            $reporterName,
            $violation->violation_date ? $violation->violation_date->format('Y-m-d') : 'N/A',
            ucfirst(str_replace('_', ' ', $violation->status ?? 'N/A')),
            $violation->action_taken ?? 'N/A',
            $violation->resolution_date ? $violation->resolution_date->format('Y-m-d') : 'N/A',
            $violation->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 18,
            'D' => 30,
            'E' => 30,
            'F' => 15,
            'G' => 25,
            'H' => 25,
            'I' => 40,
            'J' => 25,
            'K' => 15,
            'L' => 15,
            'M' => 30,
            'N' => 15,
            'O' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
