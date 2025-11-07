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
            @if($canViewFullInfo ?? false)
            <a href="{{ route('students.edit', $student) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Student
            </a>
            @endif
            <a href="{{ route('students.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Students
            </a>
        </div>
    </div>

    @if(!($canViewFullInfo ?? false))
    <!-- Limited Access Notice -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Limited Information Access</h3>
                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                    You have restricted access to student information. Only basic details are visible based on your role.
                </p>
            </div>
        </div>
    </div>
    @endif

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
                                {{ $student->studentProfile->first_name ?? '' }} 
                                {{ $student->studentProfile->middle_name ?? '' }} 
                                {{ $student->studentProfile->last_name ?? '' }} 
                                {{ $student->studentProfile->suffix ?? '' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sex</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->sex ?? 'N/A' }}
                            </p>
                        </div>
                        @if($canViewFullInfo ?? false)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->date_of_birth ? $student->studentProfile->date_of_birth->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Civil Status</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->civil_status ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nationality</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->nationality ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Place of Birth</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->place_of_birth ?? 'N/A' }}
                            </p>
                        </div>
                        @endif
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
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">School Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->email }}</p>
                        </div>
                        @if($canViewFullInfo ?? false)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Mobile Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->mobile ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Current Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->current_address ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Permanent Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->permanent_address ?? 'N/A' }}</p>
                        </div>
                        @endif
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
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->student_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Department</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Course</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->course ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($student->studentProfile->year_level)
                                    {{ $student->studentProfile->year_level }}{{ $student->studentProfile->year_level == 1 ? 'st' : ($student->studentProfile->year_level == 2 ? 'nd' : ($student->studentProfile->year_level == 3 ? 'rd' : 'th')) }} Year
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Enrollment Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->studentProfile->enrollment_date ? $student->studentProfile->enrollment_date->format('M d, Y') : 'N/A' }}
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
            @if(($canViewFullInfo ?? false) && $student->studentProfile->emergency_name)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Emergency Contact</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contact Person</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->emergency_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->emergency_relationship ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->emergency_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->studentProfile->emergency_address ?? 'N/A' }}</p>
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
            @if($canViewFullInfo ?? false)
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
            @endif

            <!-- Account Information -->
            @if($canViewFullInfo ?? false)
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
            @endif
        </div>
    </div>
</div>
@endsection

