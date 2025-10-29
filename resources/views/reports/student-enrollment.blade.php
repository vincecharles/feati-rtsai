<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Enrollment Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filter Report</h3>
                    <form method="GET" action="{{ route('reports.students') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                            <select name="department" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Programs</option>
                                <!-- College of Engineering -->
                                <option value="Civil Engineering" {{ ($filters['department'] ?? '') == 'Civil Engineering' ? 'selected' : '' }}>BS Civil Engineering</option>
                                <option value="Electrical Engineering" {{ ($filters['department'] ?? '') == 'Electrical Engineering' ? 'selected' : '' }}>BS Electrical Engineering</option>
                                <option value="Geodetic Engineering" {{ ($filters['department'] ?? '') == 'Geodetic Engineering' ? 'selected' : '' }}>BS Geodetic Engineering</option>
                                <option value="Electronics Engineering" {{ ($filters['department'] ?? '') == 'Electronics Engineering' ? 'selected' : '' }}>BS Electronics Engineering</option>
                                <option value="Information Technology" {{ ($filters['department'] ?? '') == 'Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                                <option value="Computer Science" {{ ($filters['department'] ?? '') == 'Computer Science' ? 'selected' : '' }}>BS Computer Science</option>
                                <option value="Associate in Computer Science" {{ ($filters['department'] ?? '') == 'Associate in Computer Science' ? 'selected' : '' }}>Associate in Computer Science</option>
                                <option value="Mechanical Engineering" {{ ($filters['department'] ?? '') == 'Mechanical Engineering' ? 'selected' : '' }}>BS Mechanical Engineering</option>
                                <option value="Aeronautical Engineering" {{ ($filters['department'] ?? '') == 'Aeronautical Engineering' ? 'selected' : '' }}>BS Aeronautical Engineering</option>
                                <option value="Aircraft Maintenance Technology" {{ ($filters['department'] ?? '') == 'Aircraft Maintenance Technology' ? 'selected' : '' }}>BS Aircraft Maintenance Technology</option>
                                <option value="Certificate in Aircraft Maintenance Technology" {{ ($filters['department'] ?? '') == 'Certificate in Aircraft Maintenance Technology' ? 'selected' : '' }}>Certificate in Aircraft Maintenance Technology</option>
                                <!-- College of Maritime Education -->
                                <option value="Marine Engineering" {{ ($filters['department'] ?? '') == 'Marine Engineering' ? 'selected' : '' }}>BS Marine Engineering</option>
                                <option value="Marine Transportation" {{ ($filters['department'] ?? '') == 'Marine Transportation' ? 'selected' : '' }}>BS Marine Transportation</option>
                                <!-- College of Business -->
                                <option value="Tourism Management" {{ ($filters['department'] ?? '') == 'Tourism Management' ? 'selected' : '' }}>BS Tourism Management</option>
                                <option value="Customs Administration" {{ ($filters['department'] ?? '') == 'Customs Administration' ? 'selected' : '' }}>BS Customs Administration</option>
                                <option value="Business Administration" {{ ($filters['department'] ?? '') == 'Business Administration' ? 'selected' : '' }}>BS Business Administration</option>
                                <!-- College of Architecture -->
                                <option value="Architecture" {{ ($filters['department'] ?? '') == 'Architecture' ? 'selected' : '' }}>BS Architecture</option>
                                <!-- School of Fine Arts -->
                                <option value="Fine Arts - Visual Communication" {{ ($filters['department'] ?? '') == 'Fine Arts - Visual Communication' ? 'selected' : '' }}>BFA major in Visual Communication</option>
                                <!-- College of Arts, Sciences and Education -->
                                <option value="Communication" {{ ($filters['department'] ?? '') == 'Communication' ? 'selected' : '' }}>BA in Communication</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                Apply Filters
                            </button>
                            <a href="{{ route('reports.students') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Total Students</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $total_count }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Active Students</div>
                    <div class="text-3xl font-bold text-green-600">{{ $students->where('status', 'active')->count() }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Suspended</div>
                    <div class="text-3xl font-bold text-red-600">{{ $students->where('status', 'suspended')->count() }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-sm text-gray-600 mb-2">Graduated</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $students->where('status', 'graduated')->count() }}</div>
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
                        <form method="GET" action="{{ route('reports.students') }}" class="inline">
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
                        <form method="GET" action="{{ route('reports.students') }}" class="inline">
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
                        <form method="GET" action="{{ route('reports.students') }}" class="inline">
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

            <!-- Students Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Student Enrollment Details</h3>
                        <div class="text-sm text-gray-600">
                            Generated: {{ $generated_at->format('F d, Y H:i:s') }}
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->student_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->program ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->year_level ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->mobile ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'inactive' => 'bg-yellow-100 text-yellow-800',
                                                    'suspended' => 'bg-red-100 text-red-800',
                                                    'graduated' => 'bg-blue-100 text-blue-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No students found matching the filters.
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
