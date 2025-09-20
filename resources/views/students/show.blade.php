@extends('layouts.sidebar')

@section('page-title', 'Student Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Student Details</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ $student->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('students.edit', $student) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Student
            </a>
            <a href="{{ route('students.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Student Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->first_name ?? '' }} 
                                {{ $student->profile->middle_name ?? '' }} 
                                {{ $student->profile->last_name ?? '' }} 
                                {{ $student->profile->suffix ?? '' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->date_of_birth ? $student->profile->date_of_birth->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sex</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->sex ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Civil Status</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->civil_status ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nationality</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->nationality ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Place of Birth</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->place_of_birth ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Contact Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Mobile Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->mobile ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Current Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->current_address ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Permanent Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->permanent_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Academic Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->employee_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Department</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Course</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->course ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($student->profile->year_level)
                                    {{ $student->profile->year_level }}{{ $student->profile->year_level == 1 ? 'st' : ($student->profile->year_level == 2 ? 'nd' : ($student->profile->year_level == 3 ? 'rd' : 'th')) }} Year
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Enrollment Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->profile->date_hired ? $student->profile->date_hired->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($student->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($student->status === 'inactive') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($student->status === 'suspended') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($student->status === 'graduated') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            @if($student->profile->emergency_name)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Emergency Contact</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contact Person</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->emergency_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->emergency_relationship ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->emergency_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->profile->emergency_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Photo/Avatar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Student Photo</h3>
                </div>
                <div class="p-6 text-center">
                    <div class="mx-auto h-32 w-32 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-600 dark:text-gray-300"></i>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No photo uploaded</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('students.edit', $student) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> Edit Student
                    </a>
                    <button onclick="if(confirm('Are you sure you want to delete this student?')) { document.getElementById('delete-form').submit(); }" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i> Delete Student
                    </button>
                    <form id="delete-form" action="{{ route('students.destroy', $student) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Account Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Account Created</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->created_at->format('M d, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->updated_at->format('M d, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Role</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->role->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
