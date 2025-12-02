<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }} - FEATI PRISM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            background: white;
            padding: 15px;
        }
        
        .print-header {
            text-align: center;
            padding: 10px 0 15px;
            border-bottom: 2px solid #2563eb;
            margin-bottom: 15px;
        }
        
        .print-header h1 {
            font-size: 18px;
            color: #2563eb;
            margin-bottom: 3px;
        }
        
        .print-header .subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        
        .print-header .date {
            color: #666;
            font-size: 10px;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .print-footer {
            text-align: center;
            padding: 10px 0;
            margin-top: 15px;
            color: #666;
            font-size: 9px;
            border-top: 1px solid #ddd;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        .no-print button {
            padding: 8px 16px;
            margin: 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-print {
            background: #2563eb;
            color: white;
        }
        
        .btn-close {
            background: #dc2626;
            color: white;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 0;
            }
            
            th {
                background-color: #2563eb !important;
                color: white !important;
            }
            
            @page {
                size: landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <button class="btn-close" onclick="window.history.back()">← Back</button>
    </div>

    <div class="print-header">
        <h1>FEATI PRISM</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="date">Generated: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    @if($reportType === 'reports.student-enrollment')
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Year Level</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Enrolled Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['students'] as $student)
                    <tr>
                        <td>{{ $student->student_id ?? 'N/A' }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->program ?? 'N/A' }}</td>
                        <td>{{ $student->year_level ?? 'N/A' }}</td>
                        <td>{{ $student->mobile ?? 'N/A' }}</td>
                        <td>{{ ucfirst($student->status ?? 'N/A') }}</td>
                        <td>{{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align: center;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="print-footer">Total Records: {{ $data['total_count'] ?? count($data['students']) }}</div>

    @elseif($reportType === 'reports.violations-report')
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Violation Type</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Reported By</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['violations'] as $violation)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y') }}</td>
                        <td>{{ $violation->student->name ?? 'N/A' }}</td>
                        <td>{{ $violation->student->student_id ?? 'N/A' }}</td>
                        <td>{{ $violation->violation_type ?? 'N/A' }}</td>
                        <td>{{ ucfirst($violation->severity ?? 'N/A') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $violation->status ?? 'N/A')) }}</td>
                        <td>{{ $violation->reporter->name ?? 'N/A' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($violation->description, 50) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align: center;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="print-footer">Total Records: {{ $data['total'] ?? count($data['violations']) }}</div>

    @elseif($reportType === 'reports.employee-report')
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Date Hired</th>
                    <th>Mobile</th>
                    <th>Dependents</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['employees'] as $employee)
                    <tr>
                        <td>{{ $employee->profile->employee_id ?? 'N/A' }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->profile->position ?? 'N/A' }}</td>
                        <td>{{ $employee->profile->department ?? 'N/A' }}</td>
                        <td>{{ $employee->profile && $employee->profile->date_hired ? \Carbon\Carbon::parse($employee->profile->date_hired)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $employee->mobile ?? 'N/A' }}</td>
                        <td>{{ $employee->dependents->count() }}</td>
                        <td>{{ ucfirst($employee->status ?? 'N/A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align: center;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="print-footer">Total Records: {{ $data['total_count'] ?? count($data['employees']) }}</div>
    @endif

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
