@extends('layouts.sidebar')

@section('page-title', 'Edit Violation')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-100" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-100" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Violation</h2>
            <p class="text-gray-600 dark:text-gray-400">Update violation status and add notes</p>
        </div>
        <a href="{{ route('violations.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <!-- Violation Details (Read-only) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
            Violation Details (Read-only)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student</label>
                <p class="text-gray-900 dark:text-gray-100">{{ $violation->student->name }} ({{ $violation->student->student_id ?? 'No ID' }})</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Offense Category</label>
                <p class="text-gray-900 dark:text-gray-100">{{ ucfirst($violation->offense_category ?? 'N/A') }} Offenses</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Offense Code</label>
                <p class="text-gray-900 dark:text-gray-100">{{ $violation->violation_type }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sanction</label>
                <p class="text-gray-900 dark:text-gray-100">{{ $violation->sanction }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Violation Date</label>
                <p class="text-gray-900 dark:text-gray-100">{{ $violation->violation_date?->format('F j, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reported By</label>
                <p class="text-gray-900 dark:text-gray-100">{{ $violation->reporter->name ?? 'Unknown' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $violation->description }}</p>
            </div>
        </div>
    </div>

    <!-- Update Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('violations.update', $violation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending" {{ old('status', $violation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ old('status', $violation->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        @if(Auth::user()->role?->name !== 'security')
                        <option value="resolved" {{ old('status', $violation->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ old('status', $violation->status) == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        @endif
                    </select>
                    @if(Auth::user()->role?->name === 'security')
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Note: Security personnel cannot resolve or dismiss violations. Only OSA and Admin can do this.
                    </p>
                    @endif
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
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('violations.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i> Update Violation
                </button>
            </div>
        </form>
    </div>

    <!-- Notes Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
            <i class="fas fa-sticky-note mr-2"></i> Notes
            @php
                $notesCollection = $violation->notes ?? collect();
            @endphp
            @if($notesCollection->count() > 0)
                ({{ $notesCollection->count() }} total)
            @endif
        </h3>

        <!-- Existing Notes -->
        <div class="space-y-4 mb-6">
            @forelse($notesCollection as $note)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-user-circle mr-2"></i>
                            <span class="font-medium">{{ $note->user->name ?? 'Unknown User' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $note->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
                    <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $note->note }}</p>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No notes added yet.</p>
            @endforelse
        </div>

        <!-- Add Note Form -->
        <form action="{{ route('violations.notes.store', $violation) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700 pt-6">
            @csrf
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Add New Note
                </label>
                <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-user-circle mr-1"></i>
                    <span>Adding as: <strong>{{ auth()->user()->name }}</strong></span>
                    <span class="mx-2">•</span>
                    <span>{{ now()->format('M j, Y g:i A') }}</span>
                </div>
                <textarea id="note" name="note" rows="3" required
                          placeholder="Enter your note here..."
                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-2"></i> Add Note
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
