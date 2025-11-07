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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search students..." 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
            
            <!-- Department Filter (Only Super Admin) -->
            @if(Auth::user()->role->name === 'admin')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program</label>
                <select name="department" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Programs</option>
                    <!-- College of Engineering -->
                    <option value="BS Civil Engineering" {{ request('department') == 'BS Civil Engineering' ? 'selected' : '' }}>BS Civil Engineering</option>
                    <option value="BS Electrical Engineering" {{ request('department') == 'BS Electrical Engineering' ? 'selected' : '' }}>BS Electrical Engineering</option>
                    <option value="BS Geodetic Engineering" {{ request('department') == 'BS Geodetic Engineering' ? 'selected' : '' }}>BS Geodetic Engineering</option>
                    <option value="BS Electronics Engineering" {{ request('department') == 'BS Electronics Engineering' ? 'selected' : '' }}>BS Electronics Engineering</option>
                    <option value="BS Information Technology" {{ request('department') == 'BS Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                    <option value="BS Computer Science" {{ request('department') == 'BS Computer Science' ? 'selected' : '' }}>BS Computer Science</option>
                    <option value="Associate in Computer Science" {{ request('department') == 'Associate in Computer Science' ? 'selected' : '' }}>Associate in Computer Science</option>
                    <option value="BS Mechanical Engineering" {{ request('department') == 'BS Mechanical Engineering' ? 'selected' : '' }}>BS Mechanical Engineering</option>
                    <option value="BS Aeronautical Engineering" {{ request('department') == 'BS Aeronautical Engineering' ? 'selected' : '' }}>BS Aeronautical Engineering</option>
                    <option value="BS Aircraft Maintenance Technology" {{ request('department') == 'BS Aircraft Maintenance Technology' ? 'selected' : '' }}>BS Aircraft Maintenance Technology</option>
                    <option value="Certificate in Aircraft Maintenance Technology" {{ request('department') == 'Certificate in Aircraft Maintenance Technology' ? 'selected' : '' }}>Certificate in Aircraft Maintenance Technology</option>
                    <!-- College of Maritime Education -->
                    <option value="BS Marine Engineering" {{ request('department') == 'BS Marine Engineering' ? 'selected' : '' }}>BS Marine Engineering</option>
                    <option value="BS Marine Transportation" {{ request('department') == 'BS Marine Transportation' ? 'selected' : '' }}>BS Marine Transportation</option>
                    <!-- College of Business -->
                    <option value="BS Tourism Management" {{ request('department') == 'BS Tourism Management' ? 'selected' : '' }}>BS Tourism Management</option>
                    <option value="BS Customs Administration" {{ request('department') == 'BS Customs Administration' ? 'selected' : '' }}>BS Customs Administration</option>
                    <option value="BS Business Administration" {{ request('department') == 'BS Business Administration' ? 'selected' : '' }}>BS Business Administration</option>
                    <!-- College of Architecture -->
                    <option value="BS Architecture" {{ request('department') == 'BS Architecture' ? 'selected' : '' }}>BS Architecture</option>
                    <!-- School of Fine Arts -->
                    <option value="BFA major in Visual Communication" {{ request('department') == 'BFA major in Visual Communication' ? 'selected' : '' }}>BFA major in Visual Communication</option>
                    <!-- College of Arts, Sciences and Education -->
                    <option value="BA in Communication" {{ request('department') == 'BA in Communication' ? 'selected' : '' }}>BA in Communication</option>
                </select>
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
