@extends('layouts.sidebar')

@section('page-title', 'New Violation Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Disciplinary Violation Report</h2>
            <p class="text-gray-600 dark:text-gray-400">Report a student disciplinary violation based on FEATI University Code of Conduct</p>
        </div>
        <a href="{{ route('violations.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <form action="{{ route('violations.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Selection with Search -->
                <div class="md:col-span-2">
                    <label for="student_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user-graduate mr-1"></i> Student <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="student_search" 
                               placeholder="Search student by name, ID, or program..." 
                               autocomplete="off"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <input type="hidden" id="student_id" name="student_id" value="{{ old('student_id') }}" required>
                        
                        <!-- Search Suggestions Dropdown -->
                        <div id="student_suggestions" class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                            <!-- Results will be inserted here -->
                        </div>
                    </div>
                    <div id="selected_student" class="mt-2 hidden">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-3 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-blue-900 dark:text-blue-100" id="selected_student_name"></span>
                                <span class="text-sm text-blue-700 dark:text-blue-300 ml-2" id="selected_student_details"></span>
                            </div>
                            <button type="button" onclick="clearStudentSelection()" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($students->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">⚠️ No students found. Please add students first.</p>
                    @endif
                </div>

                <!-- Violation Type/Code -->
                <div class="md:col-span-2">
                    <label for="violation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Offense Code <span class="text-red-600">*</span>
                    </label>
                    <select id="violation_type" name="violation_type" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Select offense code</option>
                        
                        @foreach($violationTypes as $category => $types)
                            <optgroup label="{{ strtoupper($category) }} OFFENSES">
                                @foreach($types as $type)
                                    <option value="{{ $type->code }}" {{ old('violation_type') == $type->code ? 'selected' : '' }}>
                                        Code {{ $type->code }} - {{ $type->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('violation_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle"></i> Select the appropriate offense code from the Student Handbook
                    </p>
                </div>

                <!-- Sanction -->
                <div>
                    <label for="sanction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Disciplinary Sanction <span class="text-red-600">*</span>
                    </label>
                    <select id="sanction" name="sanction" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select sanction</option>
                        <option value="Disciplinary Citation (E)" {{ old('sanction') == 'Disciplinary Citation (E)' ? 'selected' : '' }}>E - Disciplinary Citation</option>
                        <option value="Suspension (D)" {{ old('sanction') == 'Suspension (D)' ? 'selected' : '' }}>D - Suspension</option>
                        <option value="Preventive Suspension (C)" {{ old('sanction') == 'Preventive Suspension (C)' ? 'selected' : '' }}>C - Preventive Suspension</option>
                        <option value="Exclusion (B)" {{ old('sanction') == 'Exclusion (B)' ? 'selected' : '' }}>B - Exclusion</option>
                        <option value="Expulsion (A)" {{ old('sanction') == 'Expulsion (A)' ? 'selected' : '' }}>A - Expulsion</option>
                    </select>
                    @error('sanction')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Violation Date -->
                <div>
                    <label for="violation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Violation Date <span class="text-red-600">*</span>
                    </label>
                    <input type="date" id="violation_date" name="violation_date" required
                           value="{{ old('violation_date', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('violation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-600">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              placeholder="Provide detailed description of the violation..."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Taken -->
                <div class="md:col-span-2">
                    <label for="action_taken" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Action Taken (Optional)
                    </label>
                    <textarea id="action_taken" name="action_taken" rows="3"
                              placeholder="Describe any immediate actions taken..."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('action_taken') }}</textarea>
                    @error('action_taken')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('violations.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold shadow-md">
                    <i class="fas fa-file-alt mr-2"></i> Submit Violation Report
                </button>
            </div>
        </form>
    </div>

    <!-- Handbook Reference -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
        <div class="flex items-start">
            <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Disciplinary Code Reference</h3>
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    All offense codes are based on Section 14.3 of the FEATI University Student Handbook. 
                    Major offenses (Codes 1-35) and Minor offenses are categorized by severity class with corresponding penalties.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Student search functionality
const studentSearchInput = document.getElementById('student_search');
const studentSuggestions = document.getElementById('student_suggestions');
const studentIdInput = document.getElementById('student_id');
const selectedStudentDiv = document.getElementById('selected_student');
const selectedStudentName = document.getElementById('selected_student_name');
const selectedStudentDetails = document.getElementById('selected_student_details');

let searchTimeout;
let allStudents = @json($students);

// Show selected student if there's an old value
const oldStudentId = "{{ old('student_id') }}";
if (oldStudentId) {
    const student = allStudents.find(s => s.id == oldStudentId);
    if (student) {
        selectStudent(student);
    }
}

studentSearchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim().toLowerCase();
    
    if (query.length < 1) {
        studentSuggestions.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        const filtered = allStudents.filter(student => {
            return student.name.toLowerCase().includes(query) ||
                   (student.student_id && student.student_id.toLowerCase().includes(query)) ||
                   (student.program && student.program.toLowerCase().includes(query));
        });
        
        displayStudentSuggestions(filtered);
    }, 300);
});

function displayStudentSuggestions(students) {
    if (students.length === 0) {
        studentSuggestions.innerHTML = '<div class="p-3 text-sm text-gray-500 dark:text-gray-400">No students found</div>';
        studentSuggestions.classList.remove('hidden');
        return;
    }
    
    const html = students.slice(0, 10).map(student => `
        <div class="p-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0"
             onclick='selectStudent(${JSON.stringify(student)})'>
            <div class="font-medium text-gray-900 dark:text-gray-100">${student.name}</div>
            <div class="text-sm text-gray-600 dark:text-gray-300">
                ${student.student_id || 'No ID'} • ${student.program || 'No Program'} • Year ${student.year_level || 'N/A'}
            </div>
        </div>
    `).join('');
    
    studentSuggestions.innerHTML = html;
    studentSuggestions.classList.remove('hidden');
}

function selectStudent(student) {
    studentIdInput.value = student.id;
    studentSearchInput.value = '';
    studentSuggestions.classList.add('hidden');
    
    selectedStudentName.textContent = student.name;
    selectedStudentDetails.textContent = `(${student.student_id || 'No ID'}) - ${student.program || 'No Program'} - Year ${student.year_level || 'N/A'}`;
    selectedStudentDiv.classList.remove('hidden');
}

function clearStudentSelection() {
    studentIdInput.value = '';
    selectedStudentDiv.classList.add('hidden');
    studentSearchInput.value = '';
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!studentSearchInput.contains(e.target) && !studentSuggestions.contains(e.target)) {
        studentSuggestions.classList.add('hidden');
    }
});
</script>
@endsection
