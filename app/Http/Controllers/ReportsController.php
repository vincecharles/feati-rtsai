<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\Violation;
// use App\Models\Application;
// use App\Models\Event;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Check if user has permission to access reports
     */
    private function checkReportAccess()
    {
        $userRole = Auth::user()->role->name ?? null;
        if (!in_array($userRole, ['admin', 'osa'])) {
            abort(403, 'You do not have permission to access reports.');
        }
    }

    /**
     * Display the reports dashboard
     */
    public function index()
    {
        $this->checkReportAccess();
        
        $overviewStats = $this->getOverviewStatistics();
        $chartData = $this->getChartData();
        
        return view('reports.index', compact('overviewStats', 'chartData'));
    }

    /**
     * Generate student enrollment report
     */
    public function studentEnrollment(Request $request)
    {
        $this->checkReportAccess();
        
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'department' => 'nullable|string',
            'format' => 'nullable|in:pdf,csv',
        ]);

        try {
            $query = User::with('role')
                ->whereHas('role', function($q) {
                    $q->where('name', 'student');
                })
                ->whereNotNull('student_id');

            // Apply filters
            if ($request->start_date) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('created_at', '<=', $request->end_date);
            }

            if ($request->department) {
                $query->where('program', $request->department);
            }

            $students = $query->orderBy('created_at', 'desc')->get();

            $data = [
                'students' => $students,
                'filters' => $request->only(['start_date', 'end_date', 'department']),
                'generated_at' => now(),
                'total_count' => $students->count(),
            ];

            if ($request->format === 'pdf') {
                return $this->generatePDF('reports.student-enrollment', $data, 'student-enrollment-report.pdf');
            } elseif ($request->format === 'csv') {
                return $this->generateStudentCSV($students, 'student-enrollment-report.csv');
            }

            return view('reports.student-enrollment', $data);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate student enrollment report: ' . $e->getMessage());
        }
    }

    /**
     * Generate violations report
     */
    public function violationsReport(Request $request)
    {
        $this->checkReportAccess();
        
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'severity' => 'nullable|in:minor,moderate,major,severe',
            'status' => 'nullable|in:pending,under_review,resolved,dismissed',
            'format' => 'nullable|in:pdf,csv',
        ]);

        try {
            $query = Violation::with(['student', 'reporter']);

            // Apply filters
            if ($request->start_date) {
                $query->whereDate('violation_date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('violation_date', '<=', $request->end_date);
            }

            if ($request->severity) {
                $query->where('severity', $request->severity);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $violations = $query->orderBy('violation_date', 'desc')->get();

            $data = [
                'violations' => $violations,
                'total' => $violations->count(),
                'by_severity' => $violations->groupBy('severity')->map->count(),
                'by_status' => $violations->groupBy('status')->map->count(),
                'filters' => $request->only(['start_date', 'end_date', 'severity', 'status']),
            ];

            if ($request->format === 'pdf') {
                return $this->generatePDF('reports.violations-report', $data, 'violations-report.pdf');
            } elseif ($request->format === 'csv') {
                return $this->generateViolationCSV($violations, 'violations-report.csv');
            }

            if ($request->expectsJson()) {
                return $this->successResponse('Violations report generated successfully', $data);
            }

            return view('reports.violations-report', $data);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to generate violations report: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to generate violations report: ' . $e->getMessage());
        }
    }

    /**
     * Generate employee report
     */
    /**
     * Generate employee report
     */
    public function employeeReport(Request $request)
    {
        $this->checkReportAccess();
        
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'department' => 'nullable|string',
            'status' => 'nullable|string',
            'format' => 'nullable|in:pdf,csv',
        ]);

        try {
            $query = User::with(['profile', 'role', 'dependents'])
                ->whereHas('role', function($q) {
                    $q->where('name', '!=', 'student');
                })
                ->whereHas('profile');

            // Apply filters
            if ($request->start_date) {
                $query->whereHas('profile', function($q) use ($request) {
                    $q->where('date_hired', '>=', $request->start_date);
                });
            }

            if ($request->end_date) {
                $query->whereHas('profile', function($q) use ($request) {
                    $q->where('date_hired', '<=', $request->end_date);
                });
            }

            if ($request->department) {
                $query->whereHas('profile', function($q) use ($request) {
                    $q->where('department', $request->department);
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $employees = $query->orderBy('created_at', 'desc')->get();

            $data = [
                'employees' => $employees,
                'filters' => $request->only(['start_date', 'end_date', 'department', 'status']),
                'generated_at' => now(),
                'total_count' => $employees->count(),
            ];

            if ($request->format === 'pdf') {
                return $this->generatePDF('reports.employee-report', $data, 'employee-report.pdf');
            } elseif ($request->format === 'csv') {
                return $this->generateEmployeeCSV($employees, 'employee-report.csv');
            }

            return view('reports.employee-report', $data);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate employee report: ' . $e->getMessage());
        }
    }

    /**
     * Generate application report
     */
    /*
     * Generate application report
     */
    /*
    public function applicationReport(Request $request)
    {
        $this->checkReportAccess();
        
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string',
            'department' => 'nullable|string',
            'format' => 'nullable|in:pdf,excel,csv',
        ]);

        try {
            $query = Application::with(['user', 'reviewedBy']);

            // Apply filters
            if ($request->start_date) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('created_at', '<=', $request->end_date);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->department) {
                $query->where('department', $request->department);
            }

            $applications = $query->orderBy('created_at', 'desc')->get();

            $data = [
                'applications' => $applications,
                'filters' => $request->only(['start_date', 'end_date', 'status', 'department']),
                'generated_at' => now(),
                'total_count' => $applications->count(),
                'status_counts' => $this->getApplicationStatusCounts($applications),
            ];

            if ($request->format === 'pdf') {
                return $this->generatePDF('reports.application-report', $data, 'application-report.pdf');
            } elseif ($request->format === 'excel') {
                return $this->generateExcel($data, 'application-report.xlsx');
            } elseif ($request->format === 'csv') {
                return $this->generateCSV($data, 'application-report.csv');
            }

            return view('reports.application-report', $data);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate application report: ' . $e->getMessage());
        }
    }
    */

    /*
     * Generate event report
     */
    /*
    public function eventReport(Request $request)
    {
        $this->checkReportAccess();
        
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'format' => 'nullable|in:pdf,excel,csv',
        ]);

        try {
            $query = Event::with(['createdBy', 'attendees']);

            // Apply filters
            if ($request->start_date) {
                $query->where('start_date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('end_date', '<=', $request->end_date);
            }

            if ($request->category) {
                $query->where('category', $request->category);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $events = $query->orderBy('start_date', 'desc')->get();

            $data = [
                'events' => $events,
                'filters' => $request->only(['start_date', 'end_date', 'category', 'status']),
                'generated_at' => now(),
                'total_count' => $events->count(),
                'category_counts' => $this->getEventCategoryCounts($events),
            ];

            if ($request->format === 'pdf') {
                return $this->generatePDF('reports.event-report', $data, 'event-report.pdf');
            } elseif ($request->format === 'excel') {
                return $this->generateExcel($data, 'event-report.xlsx');
            } elseif ($request->format === 'csv') {
                return $this->generateCSV($data, 'event-report.csv');
            }

            return view('reports.event-report', $data);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate event report: ' . $e->getMessage());
        }
    }
    */

    /**
     * Generate analytics dashboard data
     */
    public function analytics(Request $request)
    {
        $this->checkReportAccess();
        
        try {
            $period = $request->get('period', 'month');
            $startDate = $this->getStartDate($period);
            $endDate = now();

            $analytics = [
                'overview' => $this->getOverviewStatistics(),
                'enrollment_trend' => $this->getEnrollmentTrend($startDate, $endDate),
                // 'application_trend' => $this->getApplicationTrend($startDate, $endDate),
                'department_distribution' => $this->getDepartmentDistribution(),
                // 'event_attendance' => $this->getEventAttendanceStats($startDate, $endDate),
                'gender_distribution' => $this->getGenderDistribution(),
                'age_distribution' => $this->getAgeDistribution(),
                'monthly_stats' => $this->getMonthlyStats($startDate, $endDate),
            ];

            if ($request->expectsJson()) {
                return $this->successResponse('Analytics data retrieved successfully', $analytics);
            }

            return view('reports.analytics', compact('analytics'));

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve analytics data: ' . $e->getMessage());
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics()
    {
        $user = auth()->user();
        $roleName = $user->role?->name;
        $userDepartment = $user->profile?->department;

        // Super Admin: All statistics
        if ($roleName === 'admin') {
            return [
                'total_students' => User::whereNotNull('student_id')->count(),
                'total_employees' => User::whereHas('profile')->count(),
                'active_students' => User::whereNotNull('student_id')->where('status', 'active')->count(),
                'active_employees' => User::whereHas('profile')->where('status', 'active')->count(),
                'total_violations' => \App\Models\Violation::count(),
                'pending_violations' => \App\Models\Violation::where('status', 'pending')->count(),
            ];
        }

        // Department Head: Only their department
        if ($roleName === 'dept_head' && $userDepartment) {
            return [
                'total_students' => User::whereNotNull('student_id')->where('program', $userDepartment)->count(),
                'total_employees' => User::whereHas('profile', function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment);
                })->count(),
                'active_students' => User::whereNotNull('student_id')->where('program', $userDepartment)->where('status', 'active')->count(),
                'active_employees' => User::whereHas('profile', function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment)->where('status', 'active');
                })->count(),
                'total_violations' => \App\Models\Violation::whereHas('student', function($q) use ($userDepartment) {
                    $q->where('program', $userDepartment);
                })->count(),
                'pending_violations' => \App\Models\Violation::where('status', 'pending')->whereHas('student', function($q) use ($userDepartment) {
                    $q->where('program', $userDepartment);
                })->count(),
            ];
        }

        // OSA/Security: Limited view - violations only
        if (in_array($roleName, ['osa', 'security'])) {
            return [
                'total_violations' => \App\Models\Violation::count(),
                'pending_violations' => \App\Models\Violation::where('status', 'pending')->count(),
                'resolved_violations' => \App\Models\Violation::where('status', 'resolved')->count(),
                'dismissed_violations' => \App\Models\Violation::where('status', 'dismissed')->count(),
            ];
        }

        // Default: No data access
        return [
            'total_students' => 0,
            'total_employees' => 0,
            'active_students' => 0,
            'active_employees' => 0,
            'total_violations' => 0,
            'pending_violations' => 0,
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        return [
            'enrollment_trend' => $this->getEnrollmentTrend(now()->subMonths(12), now()),
            // 'application_status' => $this->getApplicationStatusDistribution(),
            'department_distribution' => $this->getDepartmentDistribution(),
            // 'monthly_events' => $this->getMonthlyEventsData(),
            'violations_by_severity' => $this->getViolationsBySeverity(),
        ];
    }

    /**
     * Get enrollment trend data
     */
    private function getEnrollmentTrend($startDate, $endDate)
    {
        $enrollments = EmployeeProfile::selectRaw('
            MONTH(date_hired) as month,
            YEAR(date_hired) as year,
            COUNT(*) as count
        ')
        ->whereBetween('date_hired', [$startDate, $endDate])
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $enrollment = $enrollments->where('month', $date->month)
                                   ->where('year', $date->year)
                                   ->first();
            
            $counts[] = $enrollment ? $enrollment->count : 0;
        }

        return [
            'labels' => $months,
            'data' => $counts
        ];
    }

    /*
     * Get application trend data
     */
    /*
    private function getApplicationTrend($startDate, $endDate)
    {
        $applications = Application::selectRaw('
            MONTH(created_at) as month,
            YEAR(created_at) as year,
            COUNT(*) as count
        ')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $application = $applications->where('month', $date->month)
                                     ->where('year', $date->year)
                                     ->first();
            
            $counts[] = $application ? $application->count : 0;
        }

        return [
            'labels' => $months,
            'data' => $counts
        ];
    }
    */

    /**
     * Get department distribution (using student programs instead)
     */
    private function getDepartmentDistribution()
    {
        // Get student program distribution instead of employee departments
        $programs = User::selectRaw('program, COUNT(*) as count')
            ->whereNotNull('program')
            ->whereNotNull('student_id')
            ->groupBy('program')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'labels' => $programs->pluck('program')->toArray(),
            'data' => $programs->pluck('count')->toArray()
        ];
    }

    /*
     * Get application status distribution
     */
    /*
    private function getApplicationStatusDistribution()
    {
        $statuses = Application::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $statuses->pluck('status')->toArray(),
            'data' => $statuses->pluck('count')->toArray()
        ];
    }
    */

    /*
     * Get monthly events data
     */
    /*
    private function getMonthlyEventsData()
    {
        $events = Event::selectRaw('
            MONTH(start_date) as month,
            YEAR(start_date) as year,
            COUNT(*) as count
        ')
        ->where('start_date', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $event = $events->where('month', $date->month)
                          ->where('year', $date->year)
                          ->first();
            
            $counts[] = $event ? $event->count : 0;
        }

        return [
            'labels' => $months,
            'data' => $counts
        ];
    }
    */

    /*
     * Get event attendance statistics
     */
    /*
    private function getEventAttendanceStats($startDate, $endDate)
    {
        $events = Event::withCount('attendees')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();

        return [
            'total_events' => $events->count(),
            'total_attendees' => $events->sum('attendees_count'),
            'average_attendance' => $events->avg('attendees_count'),
        ];
    }
    */

    /**
     * Get gender distribution
     */
    private function getGenderDistribution()
    {
        $genders = EmployeeProfile::selectRaw('sex, COUNT(*) as count')
            ->whereNotNull('sex')
            ->groupBy('sex')
            ->get();

        return [
            'labels' => $genders->pluck('sex')->toArray(),
            'data' => $genders->pluck('count')->toArray()
        ];
    }

    /**
     * Get age distribution
     */
    private function getAgeDistribution()
    {
        $ageGroups = EmployeeProfile::selectRaw('
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 20 THEN "Under 20"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 20 AND 29 THEN "20-29"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 30 AND 39 THEN "30-39"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 40 AND 49 THEN "40-49"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 50 AND 59 THEN "50-59"
                ELSE "60+"
            END as age_group,
            COUNT(*) as count
        ')
        ->whereNotNull('date_of_birth')
        ->groupBy('age_group')
        ->orderBy('count', 'desc')
        ->get();

        return [
            'labels' => $ageGroups->pluck('age_group')->toArray(),
            'data' => $ageGroups->pluck('count')->toArray()
        ];
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats($startDate, $endDate)
    {
        return [
            'new_students' => User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_employees' => User::whereHas('role', function($q) {
                $q->where('name', '!=', 'student');
            })->whereBetween('created_at', [$startDate, $endDate])->count(),
            // 'new_applications' => Application::whereBetween('created_at', [$startDate, $endDate])->count(),
            // 'new_events' => Event::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /*
     * Get application status counts
     */
    /*
    private function getApplicationStatusCounts($applications)
    {
        return $applications->groupBy('status')->map->count();
    }
    */

    /*
     * Get event category counts
     */
    /*
    private function getEventCategoryCounts($events)
    {
        return $events->groupBy('category')->map->count();
    }
    */
    /**
     * Get start date based on period
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return now()->subWeek();
            case 'month':
                return now()->subMonth();
            case 'quarter':
                return now()->subQuarter();
            case 'year':
                return now()->subYear();
            default:
                return now()->subMonth();
        }
    }

    /**
     * Generate PDF report
     */
    private function generatePDF($view, $data, $filename)
    {
        // Pass data to a simplified print layout with just the table
        $title = str_replace(['-report.pdf', '.pdf', '-'], [' Report', '', ' '], $filename);
        $title = ucwords($title);
        
        return response()->view('reports.print-layout', [
            'data' => $data,
            'title' => $title,
            'reportType' => $view
        ])->header('Content-Type', 'text/html');
    }

    /**
     * Generate CSV report for students
     */
    private function generateStudentCSV($students, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Student ID', 'Name', 'Email', 'Program', 'Year Level', 'Mobile', 'Status', 'Enrolled Date']);
            
            // Add data
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->student_id ?? 'N/A',
                    $student->name,
                    $student->email,
                    $student->program ?? 'N/A',
                    $student->year_level ?? 'N/A',
                    $student->mobile ?? 'N/A',
                    $student->status ?? 'N/A',
                    $student->created_at ? $student->created_at->format('Y-m-d') : 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate CSV report for employees
     */
    private function generateEmployeeCSV($employees, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Employee ID', 'Name', 'Email', 'Position', 'Department', 'Date Hired', 'Mobile', 'Status', 'Dependents']);
            
            // Add data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->profile->employee_id ?? 'N/A',
                    $employee->name,
                    $employee->email,
                    $employee->profile->position ?? 'N/A',
                    $employee->profile->department ?? 'N/A',
                    $employee->profile && $employee->profile->date_hired ? $employee->profile->date_hired : 'N/A',
                    $employee->mobile ?? 'N/A',
                    $employee->status ?? 'N/A',
                    $employee->dependents->count(),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate CSV report for violations
     */
    private function generateViolationCSV($violations, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($violations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Date', 'Student Name', 'Student ID', 'Violation Type', 'Severity', 'Status', 'Reported By', 'Description']);
            
            // Add data
            foreach ($violations as $violation) {
                fputcsv($file, [
                    $violation->violation_date ? Carbon::parse($violation->violation_date)->format('Y-m-d') : 'N/A',
                    $violation->student->name ?? 'N/A',
                    $violation->student->student_id ?? 'N/A',
                    $violation->violation_type ?? 'N/A',
                    $violation->severity ?? 'N/A',
                    $violation->status ?? 'N/A',
                    $violation->reporter->name ?? 'N/A',
                    $violation->description ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get violations by severity
     */
    private function getViolationsBySeverity()
    {
        $violations = \App\Models\Violation::selectRaw('sanction, COUNT(*) as count')
            ->groupBy('sanction')
            ->get();

        return [
            'labels' => $violations->pluck('sanction')->toArray(),
            'data' => $violations->pluck('count')->toArray()
        ];
    }
}
