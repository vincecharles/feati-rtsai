<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Data Import & Export') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-100" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-100" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_students'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Violations</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_violations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        📥 Import Data
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Import Students -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Import Students</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Upload a CSV file to bulk import student records.</p>
                            
                            <form action="{{ route('import.students') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <input type="file" name="file" accept=".csv,.txt" required
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600">
                                    @error('file')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Upload CSV
                                    </button>
                                    <a href="{{ route('import.template', 'students') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors dark:bg-gray-700 dark:text-gray-200">
                                        Download Template
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Import All Users -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Import Users (All Roles)</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Upload a CSV file to bulk import users with different roles.</p>
                            
                            <form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <input type="file" name="file" accept=".csv,.txt" required
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600">
                                    @error('file')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Upload CSV
                                    </button>
                                    <a href="{{ route('import.template', 'users') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors dark:bg-gray-700 dark:text-gray-200">
                                        Download Template
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>💡 Tip:</strong> Download the template to see the required CSV format. Make sure all required fields are filled correctly.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        📤 Export Data
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Export Students -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Students</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Export all student records to Excel or CSV format.</p>
                            <div class="space-y-2">
                                <a href="{{ route('export.students') }}" class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                                    📊 Export to Excel
                                </a>
                                <a href="{{ route('export.students.csv') }}" class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                                    📄 Export to CSV
                                </a>
                            </div>
                        </div>

                        <!-- Export All Users -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">All Users</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Export all users including employees and students.</p>
                            <div class="space-y-2">
                                <a href="{{ route('export.users') }}" class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                                    📊 Export to Excel
                                </a>
                                <a href="{{ route('export.users.csv') }}" class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                                    📄 Export to CSV
                                </a>
                            </div>
                        </div>

                        <!-- Export Violations -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Violations</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Export all violation records for analysis and reporting.</p>
                            <div class="space-y-2">
                                <a href="{{ route('export.violations') }}" class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                                    📊 Export to Excel
                                </a>
                                <a href="{{ route('export.violations.csv') }}" class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                                    📄 Export to CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>⚠️ Note:</strong> Excel files (.xlsx) preserve formatting and are recommended for viewing. CSV files are better for importing into other systems.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
