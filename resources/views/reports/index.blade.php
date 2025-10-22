@extends('layouts.sidebar')

@section('page-title', 'Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Reports & Analytics</h2>
            <p class="text-gray-600 dark:text-gray-400">Generate reports and view system analytics</p>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Student Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-graduation-cap text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Student Reports</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Enrollment, demographics, and academic progress</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.students') }}" 
                       class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
                        View Student Reports →
                    </a>
                </div>
            </div>
        </div>

        <!-- Employee Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Employee Reports</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Staff information, attendance, and performance</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.employees') }}" 
                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                        View Employee Reports →
                    </a>
                </div>
            </div>
        </div>

        {{-- Application Reports - Feature Removed
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <i class="fas fa-file-alt text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Application Reports</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Admissions, applications, and processing status</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.applications') }}" 
                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm font-medium">
                        View Application Reports →
                    </a>
                </div>
            </div>
        </div>
        --}}

        {{-- Event Reports - Feature Removed
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Event Reports</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Event attendance, participation, and analytics</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.events') }}" 
                       class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium">
                        View Event Reports →
                    </a>
                </div>
            </div>
        </div>
        --}}

        <!-- Disciplinary Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Disciplinary Reports</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Violations, penalties, and disciplinary actions</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.violations') }}" 
                       class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                        View Disciplinary Reports →
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                        <i class="fas fa-chart-bar text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Analytics Dashboard</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Comprehensive analytics and insights</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('reports.analytics') }}" 
                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                        View Analytics →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overviewStats['total_students'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Students</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overviewStats['total_employees'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Employees</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overviewStats['active_students'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Active Students</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overviewStats['total_violations'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Violations</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
