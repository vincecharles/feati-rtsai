@extends('layouts.sidebar')

@section('page-title', 'New Violation')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Report New Violation</h2>
            <p class="text-gray-600 dark:text-gray-400">Record a student disciplinary violation</p>
        </div>
        <a href="{{ route('violations.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('violations.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Selection -->
                <div class="md:col-span-2">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Student <span class="text-red-600">*</span>
                    </label>
                    <select id="student_id" name="student_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_id ?? 'No ID' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($students->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">⚠️ No students found in the system</p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">{{ $students->count() }} students available</p>
                    @endif
                </div>

                <!-- Violation Type -->
                <div class="md:col-span-2">
                    <label for="violation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Violation Type <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="violation_type" name="violation_type" required
                           value="{{ old('violation_type') }}"
                           placeholder="e.g., Fighting, Cheating, Disrespect, Absence"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('violation_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Violation Level -->
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Violation Level <span class="text-red-600">*</span>
                    </label>
                    <select id="level" name="level" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select level</option>
                        <option value="Level 1" {{ old('level') == 'Level 1' ? 'selected' : '' }}>Level 1 - Minor infraction</option>
                        <option value="Level 2" {{ old('level') == 'Level 2' ? 'selected' : '' }}>Level 2 - Moderate offense</option>
                        <option value="Level 3" {{ old('level') == 'Level 3' ? 'selected' : '' }}>Level 3 - Serious violation</option>
                        <option value="Expulsion" {{ old('level') == 'Expulsion' ? 'selected' : '' }}>Expulsion - Critical offense</option>
                    </select>
                    @error('level')
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

                <!-- Severity -->
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Severity Level <span class="text-red-600">*</span>
                    </label>
                    <select id="severity" name="severity" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select severity</option>
                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="moderate" {{ old('severity') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="severe" {{ old('severity') == 'severe' ? 'selected' : '' }}>Severe</option>
                    </select>
                    @error('severity')
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
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-save mr-2"></i> Report Violation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
