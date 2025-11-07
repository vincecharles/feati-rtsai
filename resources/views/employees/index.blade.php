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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, email, position..." 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                    <option value="Security Office" {{ request('department') == 'Security Office' ? 'selected' : '' }}>Security Office</option>
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
