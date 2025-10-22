<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Statistics -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Overview Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Total Students</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $analytics['overview']['total_students'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Active Students</div>
                        <div class="text-2xl font-bold text-green-600">{{ $analytics['overview']['active_students'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Total Employees</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ $analytics['overview']['total_employees'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Active Employees</div>
                        <div class="text-2xl font-bold text-purple-600">{{ $analytics['overview']['active_employees'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Total Violations</div>
                        <div class="text-2xl font-bold text-red-600">{{ $analytics['overview']['total_violations'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-sm text-gray-600 mb-2">Pending Violations</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $analytics['overview']['pending_violations'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Program Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Student Program Distribution</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($analytics['department_distribution'] as $program => $count)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">{{ $program }}</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $count }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ number_format(($count / max(array_sum($analytics['department_distribution']->toArray()), 1)) * 100, 1) }}%
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Gender Distribution -->
            @if(isset($analytics['gender_distribution']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Gender Distribution</h3>
                        <div class="space-y-4">
                            @foreach($analytics['gender_distribution'] as $gender => $count)
                                @php
                                    $total = array_sum($analytics['gender_distribution']->toArray());
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($gender) }}</span>
                                        <span class="text-sm text-gray-500">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Age Distribution -->
                @if(isset($analytics['age_distribution']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Age Distribution</h3>
                        <div class="space-y-4">
                            @foreach($analytics['age_distribution'] as $range => $count)
                                @php
                                    $total = array_sum($analytics['age_distribution']->toArray());
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $range }}</span>
                                        <span class="text-sm text-gray-500">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Enrollment Trend -->
            @if(isset($analytics['enrollment_trend']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Enrollment Trend</h3>
                    <div class="overflow-x-auto">
                        <canvas id="enrollmentChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            @endif

            <!-- Monthly Statistics -->
            @if(isset($analytics['monthly_stats']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Monthly Statistics</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Enrollments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Violations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($analytics['monthly_stats'] as $stat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $stat['month'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $stat['enrollments'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $stat['violations'] ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($stat['enrollments'] > ($analytics['monthly_stats']->avg('enrollments') ?? 0))
                                                <span class="text-green-600">↑ Above Average</span>
                                            @else
                                                <span class="text-gray-600">→ Below Average</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if(isset($analytics['enrollment_trend']))
        // Enrollment Trend Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (enrollmentCtx) {
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: @json($analytics['enrollment_trend']->pluck('month')),
                    datasets: [{
                        label: 'New Enrollments',
                        data: @json($analytics['enrollment_trend']->pluck('count')),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        @endif
    </script>
    @endpush
</x-app-layout>
