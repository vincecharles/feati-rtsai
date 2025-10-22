@extends('layouts.sidebar')

@section('page-title', 'Disciplinary Violations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Disciplinary Violations</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage student disciplinary records and violations</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('violations.create') }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> New Violation
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Violations</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $violations->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $violations->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Resolved</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $violations->where('status', 'resolved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-gavel text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Under Review</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $violations->where('status', 'under_review')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- Search Bar (Separate) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Violations or Students</label>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search violations, students..." 
                       value="{{ request('search') }}" 
                       autocomplete="off"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <div id="searchSuggestions" class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50 hidden max-h-80 overflow-y-auto">
                    <div id="suggestionsContent"></div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search Hidden Input -->
            <input type="hidden" name="search" id="searchInputHidden" value="{{ request('search') }}">

            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                <select name="department" id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>

            <!-- Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Level</label>
                <select name="level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Levels</option>
                    <option value="Level 1" {{ request('level') == 'Level 1' ? 'selected' : '' }}>Level 1</option>
                    <option value="Level 2" {{ request('level') == 'Level 2' ? 'selected' : '' }}>Level 2</option>
                    <option value="Level 3" {{ request('level') == 'Level 3' ? 'selected' : '' }}>Level 3</option>
                    <option value="Expulsion" {{ request('level') == 'Expulsion' ? 'selected' : '' }}>Expulsion</option>
                </select>
            </div>

            <!-- Severity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Severity</label>
                <select name="severity" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Severity</option>
                    <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                    <option value="moderate" {{ request('severity') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                    <option value="major" {{ request('major') == 'major' ? 'selected' : '' }}>Major</option>
                    <option value="severe" {{ request('severity') == 'severe' ? 'selected' : '' }}>Severe</option>
                </select>
            </div>

            <!-- Student Autocomplete -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student</label>
                <input type="hidden" name="student_id" id="studentId" value="{{ request('student_id') }}">
                <input type="text" id="studentSearch" placeholder="Search students..."
                       value="{{ request('student_id') ? ($students->find(request('student_id'))->name ?? '') : '' }}"
                       autocomplete="off"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <div id="studentSuggestions" class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg z-50 hidden max-h-80 overflow-y-auto">
                    <div id="studentSuggestionsContent"></div>
                </div>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Violations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Violation Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Level
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Severity
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($violations as $violation)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $violation->student->name ?? 'Unknown Student' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $violation->student->student_id ?? 'No ID' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $violation->violation_type }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($violation->description, 50) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($violation->level === 'Level 1') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($violation->level === 'Level 2') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($violation->level === 'Level 3') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif($violation->level === 'Expulsion') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ $violation->level ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($violation->severity === 'minor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($violation->severity === 'moderate') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif($violation->severity === 'major') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($violation->severity === 'severe') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ ucfirst($violation->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($violation->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($violation->status === 'under_review') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($violation->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($violation->status === 'dismissed') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ ucfirst(str_replace('_', ' ', $violation->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('violations.edit', $violation) }}" 
                                   class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($violation->status !== 'resolved' && $violation->status !== 'dismissed')
                                <form action="{{ route('violations.resolve', $violation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to resolve this violation?')"
                                            class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300"
                                            title="Resolve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('violations.destroy', $violation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this violation?')"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                                <p>No violations found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($violations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $violations->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const suggestionsContent = document.getElementById('suggestionsContent');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const searchInputHidden = document.getElementById('searchInputHidden');
    const studentSearch = document.getElementById('studentSearch');
    const studentSuggestionsContent = document.getElementById('studentSuggestionsContent');
    const studentSuggestions = document.getElementById('studentSuggestions');
    const studentIdInput = document.getElementById('studentId');
    const departmentFilter = document.getElementById('departmentFilter');
    const filterForm = document.getElementById('filterForm');

    let searchTimeout;
    let studentTimeout;

    // Sync search input with hidden form field
    searchInput.addEventListener('input', function() {
        searchInputHidden.value = this.value;
    });

    // Algolia-style search autocomplete
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchSuggestions.classList.add('hidden');
            return;
        }

        // Show loading state
        suggestionsContent.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Searching...</div>';
        searchSuggestions.classList.remove('hidden');
        
        searchTimeout = setTimeout(() => {
            const dept = departmentFilter.value || '';
            const params = new URLSearchParams({
                search: query,
                department: dept,
                page: 1
            });
            
            fetch(`{{ route('violations.index') }}?${params}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(apiResponse => {
                    const data = apiResponse.data;
                    if (data && data.violations && data.violations.length > 0) {
                        suggestionsContent.innerHTML = data.violations
                            .slice(0, 8)
                            .map((v, idx) => `
                                <div class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-600 transition"
                                     onclick="selectViolation('${v.violation_type.replace(/'/g, '\\"')}');">
                                    <div class="flex items-start space-x-3">
                                        <div class="mt-0.5">
                                            <i class="fas fa-list text-blue-500"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                                ${v.violation_type}
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                <strong>Student:</strong> ${v.student?.name || 'Unknown'}<br>
                                                <strong>Type:</strong> ${v.description?.substring(0, 50)}...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        searchSuggestions.classList.remove('hidden');
                    } else {
                        suggestionsContent.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-search mr-2"></i>No violations found</div>';
                        searchSuggestions.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    suggestionsContent.innerHTML = '<div class="px-4 py-3 text-sm text-red-500">Error loading suggestions</div>';
                });
        }, 300);
    });

    // Select violation from suggestions
    function selectViolation(violationType) {
        searchInput.value = violationType;
        searchInputHidden.value = violationType;
        searchSuggestions.classList.add('hidden');
        filterForm.submit();
    }

    // Algolia-style student autocomplete
    studentSearch.addEventListener('input', function() {
        clearTimeout(studentTimeout);
        const query = this.value.trim();
        
        if (query.length < 1) {
            studentSuggestions.classList.add('hidden');
            studentIdInput.value = '';
            return;
        }

        // Show loading state
        studentSuggestionsContent.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Searching...</div>';
        studentSuggestions.classList.remove('hidden');

        studentTimeout = setTimeout(() => {
            const dept = departmentFilter.value || '';
            fetch(`{{ route('violations.students') }}?q=${encodeURIComponent(query)}&department=${encodeURIComponent(dept)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(students => {
                    if (students && students.length > 0) {
                        studentSuggestionsContent.innerHTML = students
                            .slice(0, 10)
                            .map(s => `
                                <div class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-600 transition"
                                     onclick="selectStudent('${s.id}', '${s.name.replace(/'/g, '\\"')} (${s.student_id})');">
                                    <div class="flex items-start space-x-3">
                                        <div class="mt-0.5">
                                            <i class="fas fa-user-circle text-purple-500"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                                ${s.name}
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                <strong>ID:</strong> ${s.student_id} • <strong>Program:</strong> ${s.program}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        studentSuggestions.classList.remove('hidden');
                    } else {
                        studentSuggestionsContent.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-user-slash mr-2"></i>No students found</div>';
                        studentSuggestions.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    studentSuggestionsContent.innerHTML = '<div class="px-4 py-3 text-sm text-red-500">Error loading suggestions</div>';
                });
        }, 300);
    });

    function selectStudent(id, name) {
        studentIdInput.value = id;
        studentSearch.value = name;
        studentSuggestions.classList.add('hidden');
    }

    // Close suggestions when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#searchInput') && !event.target.closest('#searchSuggestions')) {
            searchSuggestions.classList.add('hidden');
        }
        if (!event.target.closest('#studentSearch') && !event.target.closest('#studentSuggestions')) {
            studentSuggestions.classList.add('hidden');
        }
    });

    // Close suggestions on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            searchSuggestions.classList.add('hidden');
            studentSuggestions.classList.add('hidden');
        }
    });
</script>
@endpush

@endsection
