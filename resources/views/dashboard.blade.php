@extends('layouts.sidebar')

@section('page-title', 'Dashboard')

@section('content')
@php
    use App\Models\Violation;
    use App\Models\ViolationType;
    use Illuminate\Support\Facades\Auth;
    

    

    $programs = [];
    $yearLevels = [];
    $userRole = Auth::user()->role->name ?? null;
    $userDept = Auth::user()->profile->department ?? null;
    
    if ($userRole === 'department_head' || $userRole === 'program_head') {

        $programs = \App\Models\User::where('program', $userDept)->whereNotNull('program')->groupBy('program')->selectRaw('program, count(*) as count')->get();
        $yearLevels = \App\Models\User::where('program', $userDept)->whereNotNull('year_level')->groupBy('year_level')->selectRaw('year_level, count(*) as count')->orderBy('year_level')->get();
    } elseif ($userRole === 'security') {
  
        $programs = \App\Models\User::whereHas('violations')->whereNotNull('program')->groupBy('program')->selectRaw('program, count(*) as count')->get();
        $yearLevels = \App\Models\User::whereHas('violations')->whereNotNull('year_level')->groupBy('year_level')->selectRaw('year_level, count(*) as count')->orderBy('year_level')->get();
    } else {
 
        $programs = \App\Models\User::whereNotNull('program')->groupBy('program')->selectRaw('program, count(*) as count')->get();
        $yearLevels = \App\Models\User::whereNotNull('year_level')->groupBy('year_level')->selectRaw('year_level, count(*) as count')->orderBy('year_level')->get();
    }
    
    $violationStats = [
        'pending' => Violation::where('status', 'pending')->count(),
        'resolved' => Violation::where('status', 'resolved')->count(),
        'under_review' => Violation::where('status', 'under_review')->count(),
    ];

    $severityData = [
        'minor' => \App\Models\Violation::where('severity', 'minor')->count(),
        'moderate' => \App\Models\Violation::where('severity', 'moderate')->count(),
        'major' => \App\Models\Violation::where('severity', 'major')->count(),
        'critical' => \App\Models\Violation::where('severity', 'critical')->count(),
    ];

    // Get top violation types
    $topViolationTypes = \App\Models\Violation::select('violation_type', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
        ->groupBy('violation_type')
        ->orderBy('count', 'desc')
        ->limit(5)
        ->get()
        ->map(function($v) { 
            return [
                'name' => $v->violation_type ?? 'Unknown',
                'count' => $v->count
            ];
        });

    // Get 7-day violation trends
    $violationTrends = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $count = \App\Models\Violation::whereDate('created_at', $date)->count();
        $violationTrends[] = [
            'date' => $date,
            'count' => $count
        ];
    }
@endphp

<!-- Hidden data container for JavaScript -->
<div id="dashboard-data" 
     data-stats='@json($stats)'
     data-programs='@json($programs)'
     data-year-levels='@json($yearLevels)'
     data-violations='@json($violationStats)'
     data-severity='@json($severityData)'
     data-top-types='@json($topViolationTypes)'
     data-trends='@json($violationTrends)'
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

    @if(Auth::user()->role->name === 'admin')
    <!-- Total Employees (Super Admin Only) -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-tie text-3xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Employees
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_employees'] ?? 0) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                            {{ number_format(Violation::count()) }}
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
                            {{ number_format($stats['pending_approvals'] ?? 0) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Disciplinary Overview (Violation Status Distribution) -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Disciplinary Overview</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="violationChart"></canvas>
        </div>
    </div>

    <!-- Violation Trends Analytics -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Violations Analytics</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="violationTrendsChart"></canvas>
        </div>
    </div>
</div>

<!-- Additional Analytics Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Violation by Level/Severity -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Violation record</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="violationSeverityChart"></canvas>
        </div>
    </div>

    <!-- Top Violation Types -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Top Violation Types</h3>
        </div>
        <div class="p-6" style="height: 300px;">
            <canvas id="topViolationTypesChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Violations</h3>
                <div class="flex gap-2">
                    <button onclick="filterViolations('today')" class="px-3 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100 hover:bg-blue-200 violation-filter" data-filter="today">Today</button>
                    <button onclick="filterViolations('7days')" class="px-3 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 hover:bg-gray-200 violation-filter" data-filter="7days">Last 7 Days</button>
                    <button onclick="filterViolations('month')" class="px-3 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 hover:bg-gray-200 violation-filter" data-filter="month">This Month</button>
                    <button onclick="filterViolations('3months')" class="px-3 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 hover:bg-gray-200 violation-filter" data-filter="3months">Last 3 Months</button>
                </div>
            </div>
            <div class="p-6" style="max-height: 500px; overflow-y: auto;">
                <div id="violations-container">
                    @php
                        $recentViolationsQuery = \App\Models\Violation::with('student')->orderBy('created_at', 'desc');
                        
                        // Filter violations by role
                        if ($userRole === 'department_head' || $userRole === 'program_head') {
                            $recentViolationsQuery = $recentViolationsQuery->whereHas('student', function($q) use ($userDept) {
                                $q->where('program', $userDept);
                            });
                        } elseif ($userRole === 'security') {
  
                        }
                        
                        // Load up to 100 violations to have data across different date ranges
                        $recentViolations = $recentViolationsQuery->take(100)->get();
                    @endphp
                    
                    @if($recentViolations->count() > 0)
                        <div class="flow-root">
                            <ul class="-mb-8" id="violations-list">
                                @foreach($recentViolations as $index => $violation)
                                <li class="violation-item" data-created="{{ $violation->created_at->format('Y-m-d') }}">
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
</div>

@push('scripts')
<script>
function filterViolations(filter) {
    const items = document.querySelectorAll('.violation-item');
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    const todayStr = now.toISOString().split('T')[0];
    let count = 0;

    document.querySelectorAll('.violation-filter').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-100');
        btn.classList.add('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-100');
    });
    event.target.classList.remove('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-100');
    event.target.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-100');

    items.forEach(item => {
        const createdDateStr = item.dataset.created;
        const createdDate = new Date(createdDateStr + 'T00:00:00');
        createdDate.setHours(0, 0, 0, 0);
        
        let shouldShow = false;
        const todayDate = new Date();
        todayDate.setHours(0, 0, 0, 0);

        switch(filter) {
            case 'today':
                shouldShow = createdDateStr === todayStr || 
                            (createdDate.getDate() === todayDate.getDate() &&
                             createdDate.getMonth() === todayDate.getMonth() &&
                             createdDate.getFullYear() === todayDate.getFullYear());
                break;
            case '7days':
                const sevenDaysAgo = new Date(todayDate);
                sevenDaysAgo.setDate(todayDate.getDate() - 7);
                sevenDaysAgo.setHours(0, 0, 0, 0);
                shouldShow = createdDate >= sevenDaysAgo && createdDate <= todayDate;
                break;
            case 'month':
                shouldShow = createdDate.getMonth() === todayDate.getMonth() && 
                            createdDate.getFullYear() === todayDate.getFullYear();
                break;
            case '3months':
                const threeMonthsAgo = new Date(todayDate);
                threeMonthsAgo.setMonth(todayDate.getMonth() - 3);
                threeMonthsAgo.setHours(0, 0, 0, 0);
                shouldShow = createdDate >= threeMonthsAgo && createdDate <= todayDate;
                break;
            default:
                shouldShow = true;
        }

        item.style.display = shouldShow ? '' : 'none';
        if (shouldShow) count++;
    });

    const container = document.getElementById('violations-container');
    const existingMsg = container.querySelector('.no-results-message');
    
    if (count === 0) {
        if (!existingMsg) {
            const msg = document.createElement('p');
            msg.className = 'no-results-message text-sm text-gray-500 dark:text-gray-400 text-center py-4';
            msg.textContent = 'No violations found for this period';
            container.appendChild(msg);
        }
    } else {
        if (existingMsg) existingMsg.remove();
    }
}

window.addEventListener('load', () => {
    const todayBtn = document.querySelector('[data-filter="today"]');
    if (todayBtn) todayBtn.click();
});
</script>
@vite('resources/js/dashboard-charts.js')
@endpush
@endsection
