@extends('layouts.sidebar')

@section('page-title', 'Create Employee')

@section('content')
<form method="POST" action="{{ route('employees.store') }}"
      x-data="{ }"
      class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-6">
                @csrf

                <div>
                    <h3 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Login & Role</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-300">Login Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                            @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-300">Temp Password</label>
                            <input type="password" name="password" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                            @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-300">Role</label>
                            <select name="role_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Select —</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" @selected(old('role_id')==$r->id)>{{ $r->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Identity</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div><label class="block text-sm dark:text-gray-300">Last</label><input name="last_name" value="{{ old('last_name') }}" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">First</label><input name="first_name" value="{{ old('first_name') }}" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Middle</label><input name="middle_name" value="{{ old('middle_name') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Suffix</label><input name="suffix" value="{{ old('suffix') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                    </div>

                    <div class="mt-3"><label class="block text-sm dark:text-gray-300">Preferred name</label><input name="preferred_name" value="{{ old('preferred_name') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                        <div>
                            <label class="block text-sm dark:text-gray-300">Sex</label>
                            <div class="mt-2 flex items-center gap-4">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="sex" value="Male" @checked(old('sex')==='Male') class="border-gray-300 dark:bg-gray-900">
                                    <span class="dark:text-gray-200">Male</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="sex" value="Female" @checked(old('sex')==='Female') class="border-gray-300 dark:bg-gray-900">
                                    <span class="dark:text-gray-200">Female</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm dark:text-gray-300">Age</label>
                            <input type="number" name="age" value="{{ old('age') }}" min="18" max="100"
                                   class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"
                                   placeholder="Enter age">
                            @error('age')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div><label class="block text-sm dark:text-gray-300">Birth Date</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Birth Place</label><input name="place_of_birth" value="{{ old('place_of_birth') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                        <div>
                            <label class="block text-sm dark:text-gray-300">Civil Status</label>
                            <select name="civil_status" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Select —</option>
                                @foreach(['Single','Married','Widowed','Legally Separated','Divorced','Annulled'] as $cs)
                                    <option value="{{ $cs }}" @selected(old('civil_status')===$cs)>{{ $cs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm dark:text-gray-300">Nationality</label><input name="nationality" value="{{ old('nationality') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Mobile</label><input name="mobile" value="{{ old('mobile') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Profile Email</label><input type="email" name="profile_email" value="{{ old('profile_email') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div>
                            <label class="block text-sm dark:text-gray-300">Current Address</label>
                            <textarea name="current_address" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">{{ old('current_address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm dark:text-gray-300">Permanent Address</label>
                            <textarea name="permanent_address" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">{{ old('permanent_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Emergency Contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div><label class="block text-sm dark:text-gray-300">Name</label><input name="emergency_name" value="{{ old('emergency_name') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Relationship</label><input name="emergency_relationship" value="{{ old('emergency_relationship') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Phone</label><input name="emergency_phone" value="{{ old('emergency_phone') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                        <div><label class="block text-sm dark:text-gray-300">Address</label><input name="emergency_address" value="{{ old('emergency_address') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100"></div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Employment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm dark:text-gray-300">Date Hired</label>
                            <input type="date" name="date_hired" value="{{ old('date_hired') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
                            <p class="text-xs text-gray-500 mt-1">Employee number (YY-XXXXXXXX) will be generated from the year hired.</p>
                        </div>
                        <div>
                            <label class="block text-sm dark:text-gray-300">Department/College <span class="text-red-500">*</span></label>
                            <select name="department" id="department-select" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
                                <option value="">— Select Department —</option>
                                <option value="College of Engineering" {{ old('department') == 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                                <option value="College of Maritime Education" {{ old('department') == 'College of Maritime Education' ? 'selected' : '' }}>College of Maritime Education</option>
                                <option value="College of Business" {{ old('department') == 'College of Business' ? 'selected' : '' }}>College of Business</option>
                                <option value="College of Architecture" {{ old('department') == 'College of Architecture' ? 'selected' : '' }}>College of Architecture</option>
                                <option value="School of Fine Arts" {{ old('department') == 'School of Fine Arts' ? 'selected' : '' }}>School of Fine Arts</option>
                                <option value="College of Arts, Sciences and Education" {{ old('department') == 'College of Arts, Sciences and Education' ? 'selected' : '' }}>College of Arts, Sciences and Education</option>
                                <option value="Administration" {{ old('department') == 'Administration' ? 'selected' : '' }}>Administration</option>
                                <option value="Human Resources" {{ old('department') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                <option value="Security" {{ old('department') == 'Security' ? 'selected' : '' }}>Security</option>
                                <option value="Office of Student Affairs" {{ old('department') == 'Office of Student Affairs' ? 'selected' : '' }}>Office of Student Affairs</option>
                            </select>
                            @error('department')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm dark:text-gray-300">Program/Course</label>  
                            <select name="program" id="program-select" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Select Program (if applicable) —</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select if teacher/faculty is assigned to a specific program.</p>
                            @error('program')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-primary-button>Save Employee</x-primary-button>
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded border dark:text-gray-100">Cancel</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department-select');
    const programSelect = document.getElementById('program-select');
    
    // Define programs by department
    const programsByDepartment = {
        'College of Engineering': [
            'BS Civil Engineering',
            'BS Electrical Engineering',
            'BS Geodetic Engineering',
            'BS Electronics Engineering',
            'BS Information Technology',
            'BS Computer Science',
            'Associate in Computer Science',
            'BS Mechanical Engineering',
            'BS Aeronautical Engineering',
            'BS Aircraft Maintenance Technology',
            'Certificate in Aircraft Maintenance Technology'
        ],
        'College of Maritime Education': [
            'BS Marine Engineering',
            'BS Marine Transportation'
        ],
        'College of Business': [
            'BS Tourism Management',
            'BS Customs Administration',
            'BS Business Administration'
        ],
        'College of Architecture': [
            'BS Architecture'
        ],
        'School of Fine Arts': [
            'BFA major in Visual Communication'
        ],
        'College of Arts, Sciences and Education': [
            'BA in Communication'
        ]
    };
    
    // Function to populate programs based on selected department
    function updatePrograms() {
        const selectedDepartment = departmentSelect.value;
        const oldProgram = '{{ old("program") }}';
        
        // Clear current options
        programSelect.innerHTML = '<option value="">— Select Program (if applicable) —</option>';
        
        // If department has programs, add them
        if (programsByDepartment[selectedDepartment]) {
            programsByDepartment[selectedDepartment].forEach(function(program) {
                const option = document.createElement('option');
                option.value = program;
                option.textContent = program;
                if (oldProgram === program) {
                    option.selected = true;
                }
                programSelect.appendChild(option);
            });
            programSelect.disabled = false;
        } else if (selectedDepartment) {
            // For non-academic departments (Admin, HR, Security, OSA)
            programSelect.disabled = true;
            programSelect.innerHTML = '<option value="">— Not applicable for this department —</option>';
        } else {
            programSelect.disabled = false;
        }
    }
    
    // Initialize on page load
    updatePrograms();
    
    // Update when department changes
    departmentSelect.addEventListener('change', updatePrograms);
});
</script>
@endsection
