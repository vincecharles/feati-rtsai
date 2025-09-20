<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and charts data
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        $chartData = $this->getChartData();
        $recentActivity = $this->getRecentActivity();
        $upcomingEvents = $this->getUpcomingEvents();

        return view('dashboard', compact('stats', 'chartData', 'recentActivity', 'upcomingEvents'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_students' => User::whereHas('profile')->count(),
            'active_applications' => $this->getActiveApplicationsCount(),
            'pending_approvals' => $this->getPendingApprovalsCount(),
            'events_this_month' => $this->getEventsThisMonthCount(),
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        return [
            'enrollment_trend' => $this->getEnrollmentTrendData(),
            'application_status' => $this->getApplicationStatusData(),
            'department_distribution' => $this->getDepartmentDistributionData(),
            'monthly_events' => $this->getMonthlyEventsData(),
            'student_performance' => $this->getStudentPerformanceData(),
        ];
    }

    /**
     * Get enrollment trend data for the last 12 months
     */
    private function getEnrollmentTrendData()
    {
        $enrollments = EmployeeProfile::selectRaw('
            MONTH(date_hired) as month,
            YEAR(date_hired) as year,
            COUNT(*) as count
        ')
        ->where('date_hired', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $months = [];
        $newEnrollments = [];
        $graduations = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $enrollment = $enrollments->where('month', $date->month)
                                   ->where('year', $date->year)
                                   ->first();
            
            $newEnrollments[] = $enrollment ? $enrollment->count : 0;
            $graduations[] = rand(80, 120); // Mock data for graduations
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'New Enrollments',
                    'data' => $newEnrollments,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Graduations',
                    'data' => $graduations,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ]
            ]
        ];
    }

    /**
     * Get application status distribution
     */
    private function getApplicationStatusData()
    {
        return [
            'labels' => ['Approved', 'Pending', 'Rejected', 'Under Review'],
            'datasets' => [
                [
                    'data' => [45, 25, 15, 15],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                        'rgb(59, 130, 246)'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get department distribution data
     */
    private function getDepartmentDistributionData()
    {
        return [
            'labels' => ['Engineering', 'Business', 'IT', 'Arts', 'Science'],
            'datasets' => [
                [
                    'data' => [35, 25, 20, 12, 8],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(251, 191, 36)',
                        'rgb(168, 85, 247)',
                        'rgb(239, 68, 68)'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get monthly events data
     */
    private function getMonthlyEventsData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $events = [8, 12, 15, 10, 18, 14];

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Events',
                    'data' => $events,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.8)',
                    'borderColor' => 'rgb(168, 85, 247)',
                ]
            ]
        ];
    }

    /**
     * Get student performance data
     */
    private function getStudentPerformanceData()
    {
        return [
            'labels' => ['Academic', 'Attendance', 'Participation', 'Leadership', 'Community Service'],
            'datasets' => [
                [
                    'label' => 'Average Score',
                    'data' => [85, 90, 78, 82, 88],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ]
            ]
        ];
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivity()
    {
        return [
            [
                'type' => 'application_approved',
                'message' => 'New student application approved',
                'details' => 'John Doe - BSIT',
                'time' => '2h ago',
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'type' => 'employee_added',
                'message' => 'New employee added',
                'details' => 'Jane Smith - Admin',
                'time' => '4h ago',
                'icon' => 'user-plus',
                'color' => 'blue'
            ],
            [
                'type' => 'event_scheduled',
                'message' => 'Event scheduled',
                'details' => 'Orientation Day 2024',
                'time' => '1d ago',
                'icon' => 'calendar',
                'color' => 'yellow'
            ],
            [
                'type' => 'disciplinary_case',
                'message' => 'Disciplinary case filed',
                'details' => 'Case #2024-001',
                'time' => '2d ago',
                'icon' => 'exclamation',
                'color' => 'red'
            ]
        ];
    }

    /**
     * Get upcoming events data
     */
    private function getUpcomingEvents()
    {
        return [
            [
                'title' => 'Freshman Orientation',
                'description' => 'Welcome new students to FEATI',
                'date' => 'March 15, 2024',
                'status' => 'upcoming',
                'status_color' => 'blue'
            ],
            [
                'title' => 'Career Fair 2024',
                'description' => 'Connect students with employers',
                'date' => 'March 22, 2024',
                'status' => 'confirmed',
                'status_color' => 'green'
            ],
            [
                'title' => 'Student Council Elections',
                'description' => 'Annual student government elections',
                'date' => 'April 5, 2024',
                'status' => 'planning',
                'status_color' => 'yellow'
            ]
        ];
    }

    /**
     * Get active applications count
     */
    private function getActiveApplicationsCount()
    {
        // Mock data - replace with actual application logic
        return 156;
    }

    /**
     * Get pending approvals count
     */
    private function getPendingApprovalsCount()
    {
        // Mock data - replace with actual approval logic
        return 23;
    }

    /**
     * Get events this month count
     */
    private function getEventsThisMonthCount()
    {
        // Mock data - replace with actual events logic
        return 12;
    }

    /**
     * Get dashboard data via API
     */
    public function getDashboardData(Request $request)
    {
        try {
            $stats = $this->getDashboardStats();
            $chartData = $this->getChartData();
            $recentActivity = $this->getRecentActivity();
            $upcomingEvents = $this->getUpcomingEvents();

            return $this->successResponse('Dashboard data retrieved successfully', [
                'stats' => $stats,
                'charts' => $chartData,
                'recent_activity' => $recentActivity,
                'upcoming_events' => $upcomingEvents
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for specific time period
     */
    public function getStatistics(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:week,month,quarter,year',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date'
        ]);

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        try {
            $stats = $this->getDashboardStats();
            
            return $this->successResponse('Statistics retrieved successfully', [
                'period' => $period,
                'stats' => $stats,
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }
}
