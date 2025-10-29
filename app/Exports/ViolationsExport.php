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
        return Violation::with(['student', 'reportedBy'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Student ID',
            'Violation Type',
            'Level',
            'Severity',
            'Description',
            'Reported By',
            'Violation Date',
            'Status',
            'Resolution',
            'Created At',
        ];
    }

    public function map($violation): array
    {
        return [
            $violation->id,
            $violation->student ? $violation->student->first_name . ' ' . $violation->student->last_name : 'N/A',
            $violation->student->student_id ?? 'N/A',
            $violation->violation_type ?? 'N/A',
            $violation->level ?? 'N/A',
            ucfirst($violation->severity ?? 'N/A'),
            $violation->description ?? 'N/A',
            $violation->reportedBy ? $violation->reportedBy->first_name . ' ' . $violation->reportedBy->last_name : 'N/A',
            $violation->violation_date ? $violation->violation_date->format('Y-m-d') : 'N/A',
            ucfirst($violation->status ?? 'N/A'),
            $violation->resolution ?? 'N/A',
            $violation->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 15,
            'D' => 25,
            'E' => 12,
            'F' => 12,
            'G' => 40,
            'H' => 25,
            'I' => 15,
            'J' => 12,
            'K' => 30,
            'L' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
