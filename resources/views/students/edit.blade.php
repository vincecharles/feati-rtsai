@extends('layouts.sidebar')

@section('page-title', 'Edit Student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Student</h2>
            <p class="text-gray-600 dark:text-gray-400">Update student information</p>
        </div>
        <a href="{{ route('students.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Students
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <form action="{{ route('students.update', $student) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->profile->first_name ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('first_name') border-red-500 @enderror" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->profile->last_name ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('last_name') border-red-500 @enderror" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Middle Name
                        </label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $student->profile->middle_name ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('middle_name') border-red-500 @enderror">
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Suffix
                        </label>
                        <select id="suffix" name="suffix" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('suffix') border-red-500 @enderror">
                            <option value="">None</option>
                            <option value="Jr." {{ old('suffix', $student->profile->suffix ?? '') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('suffix', $student->profile->suffix ?? '') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ old('suffix', $student->profile->suffix ?? '') == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ old('suffix', $student->profile->suffix ?? '') == 'III' ? 'selected' : '' }}>III</option>
                        </select>
                        @error('suffix')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->profile->date_of_birth ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('date_of_birth') border-red-500 @enderror" required>
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sex <span class="text-red-500">*</span>
                        </label>
                        <select id="sex" name="sex" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('sex') border-red-500 @enderror" required>
                            <option value="">Select Sex</option>
                            <option value="Male" {{ old('sex', $student->profile->sex ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $student->profile->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="place_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Place of Birth
                        </label>
                        <input type="text" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $student->profile->place_of_birth ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('place_of_birth') border-red-500 @enderror">
                        @error('place_of_birth')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="civil_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Civil Status
                        </label>
                        <select id="civil_status" name="civil_status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('civil_status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            <option value="Single" {{ old('civil_status', $student->profile->civil_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status', $student->profile->civil_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('civil_status', $student->profile->civil_status ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status', $student->profile->civil_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @error('civil_status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nationality
                        </label>
                        <select id="nationality" name="nationality" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('nationality') border-red-500 @enderror">
                            <option value="">Select Nationality</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('nationality', $student->profile->nationality ?? 'Philippines') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('nationality')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mobile Number
                        </label>
                        <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $student->profile->mobile ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('mobile') border-red-500 @enderror">
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="current_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Address
                        </label>
                        <textarea id="current_address" name="current_address" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('current_address') border-red-500 @enderror">{{ old('current_address', $student->profile->current_address ?? '') }}</textarea>
                        @error('current_address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="permanent_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Permanent Address
                        </label>
                        <textarea id="permanent_address" name="permanent_address" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('permanent_address') border-red-500 @enderror">{{ old('permanent_address', $student->profile->permanent_address ?? '') }}</textarea>
                        @error('permanent_address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Emergency Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Emergency Contact Name
                        </label>
                        <input type="text" id="emergency_name" name="emergency_name" value="{{ old('emergency_name', $student->profile->emergency_name ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('emergency_name') border-red-500 @enderror">
                        @error('emergency_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Relationship
                        </label>
                        <input type="text" id="emergency_relationship" name="emergency_relationship" value="{{ old('emergency_relationship', $student->profile->emergency_relationship ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('emergency_relationship') border-red-500 @enderror">
                        @error('emergency_relationship')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Emergency Phone
                        </label>
                        <input type="text" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $student->profile->emergency_phone ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('emergency_phone') border-red-500 @enderror">
                        @error('emergency_phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Emergency Address
                        </label>
                        <input type="text" id="emergency_address" name="emergency_address" value="{{ old('emergency_address', $student->profile->emergency_address ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('emergency_address') border-red-500 @enderror">
                        @error('emergency_address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Academic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $student->profile->employee_number ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('student_id') border-red-500 @enderror" required>
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select id="department" name="department" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('department') border-red-500 @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $key => $value)
                                <option value="{{ $key }}" {{ old('department', $student->profile->department ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Course/Program <span class="text-red-500">*</span>
                        </label>
                        <select id="course" name="course" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('course') border-red-500 @enderror" required>
                            <option value="">Select a department first</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select a department first to view available programs</p>
                        @error('course')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="year_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Year Level <span class="text-red-500">*</span>
                        </label>
                        <select id="year_level" name="year_level" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('year_level') border-red-500 @enderror" required>
                            <option value="">Select Year Level</option>
                            <option value="1" {{ old('year_level', $student->profile->year_level ?? '') == '1' ? 'selected' : '' }}>1st Year</option>
                            <option value="2" {{ old('year_level', $student->profile->year_level ?? '') == '2' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3" {{ old('year_level', $student->profile->year_level ?? '') == '3' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4" {{ old('year_level', $student->profile->year_level ?? '') == '4' ? 'selected' : '' }}>4th Year</option>
                            <option value="5" {{ old('year_level', $student->profile->year_level ?? '') == '5' ? 'selected' : '' }}>5th Year</option>
                        </select>
                        @error('year_level')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="enrollment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Enrollment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="enrollment_date" name="enrollment_date" value="{{ old('enrollment_date', $student->profile->date_hired ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('enrollment_date') border-red-500 @enderror" required>
                        @error('enrollment_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password (leave blank to keep current)
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('students.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i> Update Student
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Department to Programs mapping
    const departmentPrograms = @json($departmentPrograms);
    const allPrograms = @json($programs);

    // Get the current course value (either from validation error or existing student data)
    const oldCourse = '{{ old("profile.course", $student->profile->course ?? "") }}';

    const departmentSelect = document.getElementById('department');
    const courseSelect = document.getElementById('course');

    function updateCourseOptions() {
        const selectedDepartment = departmentSelect.value;
        
        // Clear existing options
        courseSelect.innerHTML = '<option value="">Select a program...</option>';
        
        if (selectedDepartment && departmentPrograms[selectedDepartment]) {
            // Get programs for selected department
            const programCodes = departmentPrograms[selectedDepartment];
            
            // Add program options
            programCodes.forEach(code => {
                if (allPrograms[code]) {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = allPrograms[code];
                    
                    // Pre-select the student's current course
                    if (code === oldCourse) {
                        option.selected = true;
                    }
                    
                    courseSelect.appendChild(option);
                }
            });
            
            courseSelect.disabled = false;
        } else {
            courseSelect.disabled = true;
        }
    }

    // Update course options when department changes
    departmentSelect.addEventListener('change', updateCourseOptions);

    // Initialize course options on page load
    updateCourseOptions();
</script>
@endpush

@endsection
