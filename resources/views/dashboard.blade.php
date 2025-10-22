@extends('layouts.sidebar')

@section('page-title', 'Dashboard')

@section('content')
@php
    use App\Models\User;
    use App\Models\Role;
    use App\Models\EmployeeProfile;
    
    $studentRole = Role::where('name', 'student')->first();
    $employeeRole = Role::where('name', 'employee')->first();
    
    $stats = [
        'total_students' => $studentRole ? User::where('role_id', $studentRole->id)->count() : 0,
        'total_employees' => $employeeRole ? User::where('role_id', $employeeRole->id)->count() : 0,
        'total_violations' => \App\Models\Violation::count(),
        'pending_violations' => \App\Models\Violation::where('status', 'pending')->count(),
    ];
    
    $programs = User::whereNotNull('program')->groupBy('program')->selectRaw('program, count(*) as count')->get();
    $yearLevels = User::whereNotNull('year_level')->groupBy('year_level')->selectRaw('year_level, count(*) as count')->orderBy('year_level')->get();
    
    $violationStats = [
        'pending' => \App\Models\Violation::where('status', 'pending')->count(),
        'resolved' => \App\Models\Violation::where('status', 'resolved')->count(),
        'under_review' => \App\Models\Violation::where('status', 'under_review')->count(),
    ];
@endphp

<!-- Hidden data container for JavaScript -->
<div id="dashboard-data" 
     data-stats='@json($stats)'
     data-programs='@json($programs)'
     data-year-levels='@json($yearLevels)'
     data-violations='@json($violationStats)'
     class="hidden"></div>
     
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Students -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-graduate text-3xl text-blue-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Students
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_students']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Employees -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-3xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Employees
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_employees']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Violations -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Violations
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_violations']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Violations -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-3xl text-yellow-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Pending Violations
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['pending_violations']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Student Enrollment Trend -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Student & Employee Overview</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>

    <!-- Violation Status Distribution -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Violation Status</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="violationChart"></canvas>
        </div>
    </div>
</div>

<!-- Additional Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Program Distribution -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Student Program Distribution</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="programChart"></canvas>
        </div>
    </div>

    <!-- Year Level Distribution -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Year Level Distribution</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="yearLevelChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Violations</h3>
            </div>
            <div class="p-6">
                @php
                    $recentViolations = \App\Models\Violation::with('student')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentViolations->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentViolations as $index => $violation)
                            <li>
                                <div class="relative {{ $index < $recentViolations->count() - 1 ? 'pb-8' : '' }}">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $violation->status == 'resolved' ? 'bg-green-500' : ($violation->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <i class="fas {{ $violation->status == 'resolved' ? 'fa-check' : 'fa-exclamation' }} text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $violation->violation_type }}
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    {{ $violation->student->name ?? 'Unknown Student' }} - {{ ucfirst($violation->status) }}
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <time>{{ $violation->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent violations</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/dashboard-charts.js')
@endpush
@endsection