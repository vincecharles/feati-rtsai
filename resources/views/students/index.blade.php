@extends('layouts.sidebar')

@section('page-title', 'Students')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Students</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage student records and information</p>
        </div>
        <div class="flex space-x-3">
            @if(Auth::user()->role->name === 'admin')
            <form action="{{ route('students.sync-data') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('This will sync student data from employee profiles to the users table. Continue?')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-sync mr-2"></i> Sync Data
                </button>
            </form>
            <a href="{{ route('students.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Student
            </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-graduation-cap text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $students->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-user-check text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Students</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $students->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <i class="fas fa-user-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Inactive Students</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $students->where('status', 'inactive')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-user-graduate text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Graduated</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $students->where('status', 'graduated')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" id="student-search" value="{{ request('search') }}" 
                       placeholder="Search students..." 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                       autocomplete="off">
                <div id="student-autocomplete" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                </select>
            </div>
            
            <!-- Program Filter (Only Super Admin) -->
            @if(Auth::user()->role->name === 'admin')
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program</label>
                <div class="relative">
                    <input type="text" 
                           id="program-search" 
                           name="program_search" 
                           value="{{ request('program') }}" 
                           placeholder="Type to search programs..."
                           autocomplete="off"
                           class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <input type="hidden" id="program-hidden" name="program" value="{{ request('program') }}">
                    <div id="program-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <div id="program-autocomplete" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>
            @endif
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Course
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Year Level
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Enrolled
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 dark:text-gray-300"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $student->student_id ?? 'No ID' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $student->program ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $student->year_level ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($student->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($student->status === 'inactive') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($student->status === 'suspended') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($student->status === 'graduated') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $student->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('students.show', $student) }}" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->role->name === 'admin')
                                    <a href="{{ route('students.edit', $student) }}" 
                                       class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this student?')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-graduation-cap text-4xl mb-2"></i>
                                <p>No students found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('student-search');
    const autocompleteDiv = document.getElementById('student-autocomplete');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            autocompleteDiv.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('students.autocomplete') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        autocompleteDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">No results found</div>';
                        autocompleteDiv.classList.remove('hidden');
                        return;
                    }

                    autocompleteDiv.innerHTML = data.map(item => `
                        <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer autocomplete-item" data-name="${item.name}">
                            <div class="font-medium text-gray-900 dark:text-gray-100">${item.name}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                ${item.student_id} • ${item.program} • Year ${item.year_level}
                            </div>
                        </div>
                    `).join('');
                    autocompleteDiv.classList.remove('hidden');

                    // Add click handlers
                    document.querySelectorAll('.autocomplete-item').forEach(item => {
                        item.addEventListener('click', function() {
                            searchInput.value = this.dataset.name;
                            autocompleteDiv.classList.add('hidden');
                            searchInput.closest('form').submit();
                        });
                    });
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });

    // Close autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
            autocompleteDiv.classList.add('hidden');
        }
    });

    // Program autocomplete
    const programSearch = document.getElementById('program-search');
    const programAutocomplete = document.getElementById('program-autocomplete');
    const programHidden = document.getElementById('program-hidden');
    const programLoading = document.getElementById('program-loading');

    const allPrograms = [
        // College of Engineering
        { code: 'BSCE', name: 'Bachelor of Science in Civil Engineering' },
        { code: 'BSEE', name: 'Bachelor of Science in Electrical Engineering' },
        { code: 'BSGE', name: 'Bachelor of Science in Geodetic Engineering' },
        { code: 'BSEcE', name: 'Bachelor of Science in Electronics Engineering' },
        { code: 'BSIT', name: 'Bachelor of Science in Information Technology' },
        { code: 'BSCS', name: 'Bachelor of Science in Computer Science' },
        { code: 'ACS', name: 'Associate in Computer Science' },
        { code: 'BSME', name: 'Bachelor of Science in Mechanical Engineering' },
        { code: 'BSAeroE', name: 'Bachelor of Science in Aeronautical Engineering' },
        { code: 'BSAMT', name: 'Bachelor of Science in Aircraft Maintenance Technology' },
        { code: 'CAMT', name: 'Certificate in Aircraft Maintenance Technology' },
        // College of Maritime Education
        { code: 'BSMarE', name: 'Bachelor of Science in Marine Engineering' },
        { code: 'BSMarT', name: 'Bachelor of Science in Marine Transportation' },
        // College of Business
        { code: 'BSTM', name: 'Bachelor of Science in Tourism Management' },
        { code: 'BSCA', name: 'Bachelor of Science in Customs Administration' },
        { code: 'BSBA', name: 'Bachelor of Science in Business Administration' },
        // College of Architecture
        { code: 'BSArch', name: 'Bachelor of Science in Architecture' },
        // School of Fine Arts
        { code: 'BFA-VC', name: 'Bachelor of Fine Arts major in Visual Communication' },
        // College of Arts, Sciences and Education
        { code: 'BAC', name: 'Bachelor of Arts in Communication' }
    ];

    if (programSearch) {
        programSearch.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();

            if (query.length < 1) {
                programAutocomplete.classList.add('hidden');
                programHidden.value = '';
                return;
            }

            // Show loading
            programLoading.classList.remove('hidden');

            // Filter programs
            setTimeout(() => {
                const filtered = allPrograms.filter(program => 
                    program.code.toLowerCase().includes(query) ||
                    program.name.toLowerCase().includes(query)
                );

                programLoading.classList.add('hidden');

                if (filtered.length === 0) {
                    programAutocomplete.innerHTML = `
                        <div class="px-4 py-3 text-center">
                            <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400">No programs found</p>
                        </div>
                    `;
                    programAutocomplete.classList.remove('hidden');
                    return;
                }

                programAutocomplete.innerHTML = filtered.map(program => `
                    <div class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer program-item border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors" data-program="${program.code}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-gray-100">${program.code}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">${program.name}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
                programAutocomplete.classList.remove('hidden');

                // Add click handlers
                document.querySelectorAll('.program-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const programCode = this.dataset.program;
                        programSearch.value = programCode;
                        programHidden.value = programCode;
                        programAutocomplete.classList.add('hidden');
                    });
                });
            }, 150);
        });

        // Close autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (!programSearch.contains(e.target) && !programAutocomplete.contains(e.target)) {
                programAutocomplete.classList.add('hidden');
            }
        });

        // Keyboard navigation
        programSearch.addEventListener('keydown', function(e) {
            const items = programAutocomplete.querySelectorAll('.program-item');
            const activeItem = programAutocomplete.querySelector('.program-item.bg-blue-100');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!activeItem) {
                    items[0]?.classList.add('bg-blue-100', 'dark:bg-blue-900');
                } else {
                    activeItem.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                    const next = activeItem.nextElementSibling;
                    if (next) {
                        next.classList.add('bg-blue-100', 'dark:bg-blue-900');
                        next.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (activeItem) {
                    activeItem.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                    const prev = activeItem.previousElementSibling;
                    if (prev) {
                        prev.classList.add('bg-blue-100', 'dark:bg-blue-900');
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Enter' && activeItem) {
                e.preventDefault();
                activeItem.click();
            } else if (e.key === 'Escape') {
                programAutocomplete.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
