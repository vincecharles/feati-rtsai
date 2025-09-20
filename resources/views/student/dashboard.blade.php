@extends('layouts.student')

@section('page-title', 'Student Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-green-100 mt-1">Track your academic progress and stay updated with campus activities</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-graduation-cap text-6xl text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Violations Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Violations</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('student.violations.index') }}" class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                    View all violations →
                </a>
            </div>
        </div>

        <!-- Applications Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Applications</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('student.applications.index') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    View all applications →
                </a>
            </div>
        </div>

        <!-- Events Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-calendar-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Events</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('student.events.index') }}" class="text-sm text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                    View all events →
                </a>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Academic Reports</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Available</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('student.reports.index') }}" class="text-sm text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                    View reports →
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Violations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Violations</h3>
            </div>
            <div class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                    <p>No recent violations</p>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Applications</h3>
            </div>
            <div class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-file-alt text-4xl mb-2"></i>
                    <p>No recent applications</p>
                    <a href="{{ route('student.applications.create') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mt-2 inline-block">
                        Create your first application
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Upcoming Events</h3>
                <a href="{{ route('student.events.index') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    View all events
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <i class="fas fa-calendar-alt text-4xl mb-2"></i>
                <p>No upcoming events</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('student.applications.create') }}" 
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-3">
                        <i class="fas fa-plus text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">New Application</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Submit a new application</p>
                    </div>
                </a>

                <a href="{{ route('student.violations.index') }}" 
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg mr-3">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">View Violations</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Check your violations</p>
                    </div>
                </a>

                <a href="{{ route('student.events.index') }}" 
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                        <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">Browse Events</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Find campus events</p>
                    </div>
                </a>

                <a href="{{ route('student.reports.index') }}" 
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-3">
                        <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">View Reports</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Academic reports</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
