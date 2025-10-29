<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Violation;
use App\Imports\UsersImport;
use App\Imports\StudentsImport;
use App\Exports\UsersExport;
use App\Exports\StudentsExport;
use App\Exports\ViolationsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can access import/export features.');
        }

        $stats = [
            'total_users' => User::count(),
            'total_students' => User::whereHas('role', fn($q) => $q->where('name', 'student'))->count(),
            'total_violations' => Violation::count(),
        ];

        return view('import-export.index', compact('stats'));
    }

    // CSV Import for Students
    public function importStudents(Request $request)
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can import students.');
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            return back()->with('success', "Successfully imported {$import->getRowCount()} students.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // CSV Import for Users (All Roles)
    public function importUsers(Request $request)
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can import users.');
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));

            return back()->with('success', "Successfully imported {$import->getRowCount()} users.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Export Students to Excel
    public function exportStudents()
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can export students.');
        }

        return Excel::download(new StudentsExport(), 'students_' . date('Y-m-d_His') . '.xlsx');
    }

    // Export All Users to Excel
    public function exportUsers()
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can export users.');
        }

        return Excel::download(new UsersExport(), 'users_' . date('Y-m-d_His') . '.xlsx');
    }

    // Export Students to CSV
    public function exportStudentsCSV()
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can export students.');
        }

        return Excel::download(new StudentsExport(), 'students_' . date('Y-m-d_His') . '.csv');
    }

    // Export All Users to CSV
    public function exportUsersCSV()
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can export users.');
        }

        return Excel::download(new UsersExport(), 'users_' . date('Y-m-d_His') . '.csv');
    }

    // Export Violations to Excel
    public function exportViolations()
    {
        if (!in_array(Auth::user()->role->name, ['admin', 'osa'])) {
            abort(403, 'Only administrators and OSA can export violations.');
        }

        return Excel::download(new ViolationsExport(), 'violations_' . date('Y-m-d_His') . '.xlsx');
    }

    // Export Violations to CSV
    public function exportViolationsCSV()
    {
        if (!in_array(Auth::user()->role->name, ['admin', 'osa'])) {
            abort(403, 'Only administrators and OSA can export violations.');
        }

        return Excel::download(new ViolationsExport(), 'violations_' . date('Y-m-d_His') . '.csv');
    }

    // Download sample CSV templates
    public function downloadTemplate($type)
    {
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only administrators can download templates.');
        }

        $templates = [
            'students' => [
                'filename' => 'student_import_template.csv',
                'headers' => ['first_name', 'last_name', 'email', 'student_id', 'program', 'year_level', 'department', 'password'],
                'sample' => ['Juan', 'Dela Cruz', 'juan.delacruz@student.feati.edu', '2024-00001', 'BS Computer Science', '1', 'College of Engineering', 'password123'],
            ],
            'users' => [
                'filename' => 'user_import_template.csv',
                'headers' => ['first_name', 'last_name', 'email', 'role', 'employee_id', 'department', 'password'],
                'sample' => ['Maria', 'Santos', 'maria.santos@feati.edu', 'teacher', 'EMP-2024-001', 'College of Engineering', 'password123'],
            ],
        ];

        if (!isset($templates[$type])) {
            abort(404, 'Template not found.');
        }

        $template = $templates[$type];
        $csv = fopen('php://temp', 'r+');
        
        fputcsv($csv, $template['headers']);
        fputcsv($csv, $template['sample']);
        
        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return response($output)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $template['filename'] . '"');
    }
}
