@extends('layouts.student')

@section('page-title', 'Violation Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Violation Details</h2>
            <p class="text-gray-600 dark:text-gray-400">View detailed information about this violation</p>
        </div>
        <div class="flex space-x-3">
            @if($violation->status !== 'resolved' && $violation->status !== 'dismissed')
            <a href="{{ route('student.violations.appeal', $violation) }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-gavel mr-2"></i> File Appeal
            </a>
            @endif
            <a href="{{ route('student.violations.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Violation Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Violation Information</h3>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Violation Type</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->violationType->name ?? 'Unknown Type' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Incident Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->incident_date->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->incident_location ?? 'Not specified' }}</p>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1
                        @if($violation->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($violation->status === 'active') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($violation->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($violation->status === 'dismissed') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        {{ ucfirst($violation->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penalty Points</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->penalty ?? 0 }} points</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->description }}</p>
            </div>

            @if($violation->penalty_description)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penalty Description</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->penalty_description }}</p>
            </div>
            @endif

            @if($violation->resolution_notes)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resolution Notes</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->resolution_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Reporter Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reporter Information</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reported By</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->reporter->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reported On</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolution Information -->
    @if($violation->status === 'resolved' && $violation->resolver)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Resolution Information</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resolved By</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->resolver->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resolved On</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $violation->resolved_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Evidence Files -->
    @if($violation->evidence->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Evidence Files</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($violation->evidence as $evidence)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file text-gray-400 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $evidence->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $evidence->file_size_human }}</p>
                            </div>
                        </div>
                        <a href="{{ route('student.violations.evidence.download', $evidence) }}" 
                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Appeals -->
    @if($violation->appeals->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Appeals</h3>
        </div>
        <div class="px-6 py-4 space-y-4">
            @foreach($violation->appeals as $appeal)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $appeal->appeal_reason }}</p>
                        @if($appeal->supporting_evidence)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $appeal->supporting_evidence }}</p>
                        @endif
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Submitted on {{ $appeal->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        @if($appeal->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($appeal->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($appeal->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        {{ ucfirst($appeal->status) }}
                    </span>
                </div>
                @if($appeal->review_notes)
                <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <strong>Review Notes:</strong> {{ $appeal->review_notes }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
