@extends('layouts.sidebar')

@section('page-title', 'Edit Violation')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Violation</h2>
            <p class="text-gray-600 dark:text-gray-400">Update violation details</p>
        </div>
        <a href="{{ route('violations.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('violations.update', $violation) }}" method="POST">
            @csrf
            @method('PUT')

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
                            <option value="{{ $student->id }}" 
                                    {{ (old('student_id', $violation->student_id) == $student->id) ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_id ?? 'No ID' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Violation Type -->
                <div class="md:col-span-2">
                    <label for="violation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Violation Type <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="violation_type" name="violation_type" required
                           value="{{ old('violation_type', $violation->violation_type) }}"
                           placeholder="e.g., Fighting, Cheating, Disrespect, Absence"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('violation_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Violation Level -->
                <div class="md:col-span-2">
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Violation Level <span class="text-red-600">*</span>
                    </label>
                    <select id="level" name="level" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a level</option>
                        <option value="Level 1" {{ (old('level', $violation->level) == 'Level 1') ? 'selected' : '' }}>
                            Level 1 - Minor infraction requiring verbal warning and parent notification
                        </option>
                        <option value="Level 2" {{ (old('level', $violation->level) == 'Level 2') ? 'selected' : '' }}>
                            Level 2 - Moderate offense requiring written warning and disciplinary action
                        </option>
                        <option value="Level 3" {{ (old('level', $violation->level) == 'Level 3') ? 'selected' : '' }}>
                            Level 3 - Serious violation requiring suspension and comprehensive review
                        </option>
                        <option value="Expulsion" {{ (old('level', $violation->level) == 'Expulsion') ? 'selected' : '' }}>
                            Expulsion - Critical offense resulting in permanent dismissal from institution
                        </option>
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
                           value="{{ old('violation_date', $violation->violation_date) }}"
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
                        <option value="minor" {{ old('severity', $violation->severity) == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="moderate" {{ old('severity', $violation->severity) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="major" {{ old('severity', $violation->severity) == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="severe" {{ old('severity', $violation->severity) == 'severe' ? 'selected' : '' }}>Severe</option>
                    </select>
                    @error('severity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending" {{ old('status', $violation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ old('status', $violation->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="resolved" {{ old('status', $violation->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ old('status', $violation->status) == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Resolution Date -->
                <div>
                    <label for="resolution_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Resolution Date
                    </label>
                    <input type="date" id="resolution_date" name="resolution_date"
                           value="{{ old('resolution_date', $violation->resolution_date) }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('resolution_date')
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
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $violation->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Taken -->
                <div class="md:col-span-2">
                    <label for="action_taken" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Action Taken
                    </label>
                    <textarea id="action_taken" name="action_taken" rows="3"
                              placeholder="Describe any actions taken..."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('action_taken', $violation->action_taken) }}</textarea>
                    @error('action_taken')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Additional notes or comments..."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $violation->notes) }}</textarea>
                    @error('notes')
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
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-save mr-2"></i> Update Violation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
