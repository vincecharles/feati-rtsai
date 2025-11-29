@extends('layouts.sidebar')

@section('page-title', 'Employees')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Employees</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage employee records and information</p>
        </div>
        @if(Auth::user()->role->name === 'admin')
        <a href="{{ route('employees.create') }}"
           class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium
                  bg-blue-600 hover:bg-blue-700 text-white shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                  dark:focus:ring-offset-gray-800">
            <i class="fa-solid fa-user-plus"></i>
            Add Employee
        </a>
        @endif
    </div>

    <!-- Search Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" id="employee-search" value="{{ request('search') }}" 
                           placeholder="Search by name, email, position..." 
                           class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                           autocomplete="off">
                    <div id="search-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <div id="employee-autocomplete" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>
            
            @if(Auth::user()->role->name === 'admin')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                <select name="department" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Departments</option>
                    <option value="College of Engineering" {{ request('department') == 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                    <option value="College of Architecture" {{ request('department') == 'College of Architecture' ? 'selected' : '' }}>College of Architecture</option>
                    <option value="College of Computer Studies" {{ request('department') == 'College of Computer Studies' ? 'selected' : '' }}>College of Computer Studies</option>
                    <option value="College of Business" {{ request('department') == 'College of Business' ? 'selected' : '' }}>College of Business</option>
                    <option value="Office of Student Affairs" {{ request('department') == 'Office of Student Affairs' ? 'selected' : '' }}>Office of Student Affairs</option>
                    <option value="Security" {{ request('department') == 'Security' ? 'selected' : '' }}>Security Office</option>
                </select>
            </div>
            @endif
            
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition flex items-center gap-2">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('employees.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow
                dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Name</th>
                                <th class="px-6 py-3 text-left font-semibold">Email</th>
                                <th class="px-6 py-3 text-left font-semibold">Position</th>
                                <th class="px-6 py-3 text-left font-semibold">Mobile</th>
                                <th class="px-6 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($employees as $emp)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $emp->name }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->email }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $position = $emp->profile?->position ?? 'N/A';
                                            $badgeClass = match($position) {
                                                'Teacher' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'Program Head' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                                'Department Head' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                                'Security' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'OSA' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                            {{ $position }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->mobile ?: '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if(Auth::user()->role->name === 'admin')
                                                <a href="{{ route('employees.edit', $emp) }}"
                                                   class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium
                                                          bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm
                                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                    Edit
                                                </a>

                                                <form action="{{ route('employees.destroy', $emp) }}" method="POST"
                                                      onsubmit="return confirm('Delete this employee?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium
                                                               bg-rose-600 hover:bg-rose-700 text-white shadow-sm
                                                               focus:outline-none focus:ring-2 focus:ring-rose-500 dark:focus:ring-offset-gray-800">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($employees->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-600 dark:text-gray-300">
                                        No employees yet.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($employees->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('employee-search');
    const autocompleteDiv = document.getElementById('employee-autocomplete');
    const loadingIndicator = document.getElementById('search-loading');
    let debounceTimer;
    let currentRequest = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            autocompleteDiv.classList.add('hidden');
            loadingIndicator.classList.add('hidden');
            if (currentRequest) {
                currentRequest.abort();
                currentRequest = null;
            }
            return;
        }

        // Show loading indicator
        loadingIndicator.classList.remove('hidden');
        autocompleteDiv.classList.add('hidden');

        debounceTimer = setTimeout(() => {
            // Cancel previous request if exists
            if (currentRequest) {
                currentRequest.abort();
            }

            // Create new AbortController for this request
            const controller = new AbortController();
            currentRequest = controller;

            fetch(`{{ route('employees.autocomplete') }}?q=${encodeURIComponent(query)}`, {
                signal: controller.signal
            })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden');
                    
                    if (data.length === 0) {
                        autocompleteDiv.innerHTML = `
                            <div class="px-4 py-3 text-center">
                                <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500 dark:text-gray-400">No employees found</p>
                            </div>
                        `;
                        autocompleteDiv.classList.remove('hidden');
                        return;
                    }

                    autocompleteDiv.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer autocomplete-item border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors" data-name="${item.name}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">${item.name}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-id-badge mr-1"></i>${item.employee_number} • 
                                        <i class="fas fa-building mr-1"></i>${item.department}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        <i class="fas fa-envelope mr-1"></i>${item.email}
                                    </div>
                                </div>
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
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error('Error:', error);
                        loadingIndicator.classList.add('hidden');
                        autocompleteDiv.innerHTML = `
                            <div class="px-4 py-3 text-center">
                                <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-2"></i>
                                <p class="text-red-500 dark:text-red-400">Error loading results</p>
                            </div>
                        `;
                        autocompleteDiv.classList.remove('hidden');
                    }
                })
                .finally(() => {
                    currentRequest = null;
                });
        }, 300);
    });

    // Close autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
            autocompleteDiv.classList.add('hidden');
        }
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = autocompleteDiv.querySelectorAll('.autocomplete-item');
        const activeItem = autocompleteDiv.querySelector('.autocomplete-item.bg-blue-100');
        
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
            autocompleteDiv.classList.add('hidden');
        }
    });
});
</script>
@endpush
