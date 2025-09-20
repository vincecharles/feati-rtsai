@extends('layouts.sidebar')

@section('page-title', 'Employees')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Employees
    </h2>

    <a href="{{ route('employees.create') }}"
       class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium
              bg-blue-600 hover:bg-blue-700 text-white shadow-sm
              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
              dark:focus:ring-offset-gray-800">
        <i class="fa-solid fa-user-plus"></i>
        Add Employee
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow
            dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Name</th>
                                <th class="px-6 py-3 text-left font-semibold">Email</th>
                                <th class="px-6 py-3 text-left font-semibold">Role</th>
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
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->role?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                                        {{ $emp->mobile ?: '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
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
            </div>

        </div>
    </div>
@endsection

