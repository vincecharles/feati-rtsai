@extends('layouts.sidebar')

@section('page-title', 'Employees')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Employees
    </h2>

    @if(Auth::user()->role->name === 'admin')
    <a href="{{ route('employees.create') }}"
       class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium
              bg-blue-600 hover:bg-blue-700 text-white shadow-sm
              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
              dark:focus:ring-offset-gray-800">
        <i class="fa-solid fa-user-plus"></i>
        Add Employee
    </a>
    @endif
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow
            dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Name</th>
                                <th class="px-6 py-3 text-left font-semibold">Email</th>
                                <th class="px-6 py-3 text-left font-semibold">Position</th>
                                <th class="px-6 py-3 text-left font-semibold">Mobile</th>
                                <th class="px-6 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($employees as $emp)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $emp->name }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->email }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $position = $emp->profile?->position ?? 'N/A';
                                            $badgeClass = match($position) {
                                                'Teacher' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'Program Head' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                                'Department Head' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                                'Security' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'OSA' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                            {{ $position }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->mobile ?: '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            @if(Auth::user()->role->name === 'admin')
                                                <a href="{{ route('employees.edit', $emp) }}"
                                                   class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium
                                                          bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm
                                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                    Edit
                                                </a>

                                                <form action="{{ route('employees.destroy', $emp) }}" method="POST"
                                                      onsubmit="return confirm('Delete this employee?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium
                                                               bg-rose-600 hover:bg-rose-700 text-white shadow-sm
                                                               focus:outline-none focus:ring-2 focus:ring-rose-500 dark:focus:ring-offset-gray-800">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($employees->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-600 dark:text-gray-300">
                                        No employees yet.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($employees->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

