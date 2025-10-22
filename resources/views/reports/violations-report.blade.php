<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Disciplinary Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filter Report</h3>
                    <form method="GET" action="{{ route('reports.violations') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                            <select name="severity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Severities</option>
                                <option value="minor" {{ ($filters['severity'] ?? '') == 'minor' ? 'selected' : '' }}>Minor</option>
                                <option value="moderate" {{ ($filters['severity'] ?? '') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="major" {{ ($filters['severity'] ?? '') == 'major' ? 'selected' : '' }}>Major</option>
                                <option value="severe" {{ ($filters['severity'] ?? '') == 'severe' ? 'selected' : '' }}>Severe</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="under_review" {{ ($filters['status'] ?? '') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="resolved" {{ ($filters['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="dismissed" {{ ($filters['status'] ?? '') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                Apply Filters
                            </button>
                            <a href="{{ route('reports.violations') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Total Violations</div>
                    <div class="text-3xl font-bold text-red-600">{{ $total }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Pending</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $by_status['pending'] ?? 0 }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Under Review</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $by_status['under_review'] ?? 0 }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Resolved</div>
                    <div class="text-3xl font-bold text-green-600">{{ $by_status['resolved'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Severity Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Violations by Severity</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="text-sm text-yellow-800 font-medium">Minor</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $by_severity['minor'] ?? 0 }}</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <div class="text-sm text-orange-800 font-medium">Moderate</div>
                            <div class="text-2xl font-bold text-orange-600">{{ $by_severity['moderate'] ?? 0 }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="text-sm text-red-800 font-medium">Major</div>
                            <div class="text-2xl font-bold text-red-600">{{ $by_severity['major'] ?? 0 }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="text-sm text-purple-800 font-medium">Severe</div>
                            <div class="text-2xl font-bold text-purple-600">{{ $by_severity['severe'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">Export Options</h3>
                        <p class="text-sm text-gray-600 mt-1">Download this report in different formats</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="GET" action="{{ route('reports.violations') }}" class="inline">
                            @foreach($filters as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                                <i class="fas fa-file-pdf mr-2"></i>Export PDF
                            </button>
                        </form>
                        <form method="GET" action="{{ route('reports.violations') }}" class="inline">
                            @foreach($filters as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </form>
                        <form method="GET" action="{{ route('reports.violations') }}" class="inline">
                            @foreach($filters as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="format" value="csv">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-file-csv mr-2"></i>Export CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Violations Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Violation Details</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Violation Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($violations as $violation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $violation->student->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $violation->student->student_id ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $violation->violation_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $severityColors = [
                                                    'minor' => 'bg-yellow-100 text-yellow-800',
                                                    'moderate' => 'bg-orange-100 text-orange-800',
                                                    'major' => 'bg-red-100 text-red-800',
                                                    'severe' => 'bg-purple-100 text-purple-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $severityColors[$violation->severity] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($violation->severity) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'under_review' => 'bg-blue-100 text-blue-800',
                                                    'resolved' => 'bg-green-100 text-green-800',
                                                    'dismissed' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$violation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $violation->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $violation->reporter->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            {{ $violation->description }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No violations found matching the filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
</x-app-layout>
