@extends('layouts.student')

@section('page-title', 'File Appeal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">File Appeal</h2>
            <p class="text-gray-600 dark:text-gray-400">Submit an appeal for this violation</p>
        </div>
        <a href="{{ route('student.violations.show', $violation) }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Violation
        </a>
    </div>

    <!-- Violation Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Violation Summary</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Violation Type</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->violationType->name ?? 'Unknown Type' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Incident Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->incident_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Severity Level</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1
                        @if($violation->severity_level === 'minor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($violation->severity_level === 'moderate') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                        @elseif($violation->severity_level === 'major') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @elseif($violation->severity_level === 'severe') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        {{ ucfirst($violation->severity_level) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penalty Points</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->penalty ?? 0 }} points</p>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->description }}</p>
            </div>
        </div>
    </div>

    <!-- Appeal Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Appeal Information</h3>
        </div>
        <form action="{{ route('student.violations.appeal.store', $violation) }}" method="POST" enctype="multipart/form-data" class="px-6 py-4 space-y-6">
            @csrf

            <div>
                <label for="appeal_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Appeal Reason <span class="text-red-500">*</span>
                </label>
                <textarea id="appeal_reason" name="appeal_reason" rows="6" required
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('appeal_reason') border-red-500 @enderror"
                          placeholder="Please explain why you believe this violation should be appealed. Provide specific details and any relevant information...">{{ old('appeal_reason') }}</textarea>
                @error('appeal_reason')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="supporting_evidence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Supporting Evidence (Optional)
                </label>
                <textarea id="supporting_evidence" name="supporting_evidence" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('supporting_evidence') border-red-500 @enderror"
                          placeholder="Provide any additional supporting evidence or context...">{{ old('supporting_evidence') }}</textarea>
                @error('supporting_evidence')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="evidence_files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Supporting Documents (Optional)
                </label>
                <input type="file" id="evidence_files" name="evidence_files[]" multiple
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('evidence_files') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX. Maximum file size: 5MB per file.
                </p>
                @error('evidence_files')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Appeal Guidelines -->
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Appeal Guidelines</h4>
                <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                    <li>• Provide clear and specific reasons for your appeal</li>
                    <li>• Include any relevant evidence or witnesses</li>
                    <li>• Be respectful and professional in your language</li>
                    <li>• Appeals will be reviewed by the administration</li>
                    <li>• You will be notified of the decision via email</li>
                </ul>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('student.violations.show', $violation) }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md">
                    <i class="fas fa-gavel mr-2"></i> Submit Appeal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
