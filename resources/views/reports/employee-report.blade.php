<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Filter Report</h3>
                    <form method="GET" action="{{ route('reports.employees') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date (Hired)</label>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date (Hired)</label>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                            <select name="department" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Departments</option>
                                <option value="College of Engineering" {{ ($filters['department'] ?? '') == 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                                <option value="College of Maritime Education" {{ ($filters['department'] ?? '') == 'College of Maritime Education' ? 'selected' : '' }}>College of Maritime Education</option>
                                <option value="College of Business" {{ ($filters['department'] ?? '') == 'College of Business' ? 'selected' : '' }}>College of Business</option>
                                <option value="College of Architecture" {{ ($filters['department'] ?? '') == 'College of Architecture' ? 'selected' : '' }}>College of Architecture</option>
                                <option value="School of Fine Arts" {{ ($filters['department'] ?? '') == 'School of Fine Arts' ? 'selected' : '' }}>School of Fine Arts</option>
                                <option value="College of Arts, Sciences and Education" {{ ($filters['department'] ?? '') == 'College of Arts, Sciences and Education' ? 'selected' : '' }}>College of Arts, Sciences and Education</option>
                                <option value="Administration" {{ ($filters['department'] ?? '') == 'Administration' ? 'selected' : '' }}>Administration</option>
                                <option value="Human Resources" {{ ($filters['department'] ?? '') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                <option value="Security" {{ ($filters['department'] ?? '') == 'Security' ? 'selected' : '' }}>Security</option>
                                <option value="Student Affairs" {{ ($filters['department'] ?? '') == 'Student Affairs' ? 'selected' : '' }}>Student Affairs</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                Apply Filters
                            </button>
                            <a href="{{ route('reports.employees') }}" class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Employees</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $total_count }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Active Employees</div>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $employees->where('status', 'active')->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">With Dependents</div>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $employees->filter(fn($e) => $e->dependents->count() > 0)->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Dependents</div>
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $employees->sum(fn($e) => $e->dependents->count()) }}</div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Export Options</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Download this report in different formats</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="GET" action="{{ route('reports.employees') }}" class="inline">
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
                        <form method="GET" action="{{ route('reports.employees') }}" class="inline">
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
                        <form method="GET" action="{{ route('reports.employees') }}" class="inline">
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

            <!-- Employees Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Employee Details</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Generated: {{ $generated_at->format('F d, Y H:i:s') }}
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Hired</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mobile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dependents</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($employees as $employee)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $employee->profile->employee_id ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @php
                                                $positionColors = [
                                                    'Teacher' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                                    'Program Head' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
                                                    'Department Head' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
                                                    'Security' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                    'OSA' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                                ];
                                                $position = $employee->profile->position ?? 'N/A';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $positionColors[$position] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ $position }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $employee->profile->department ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $employee->profile && $employee->profile->date_hired ? \Carbon\Carbon::parse($employee->profile->date_hired)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $employee->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $employee->mobile ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">
                                            {{ $employee->dependents->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                    'inactive' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$employee->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No employees found matching the filters.
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
                <a href="{{ route('reports.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
