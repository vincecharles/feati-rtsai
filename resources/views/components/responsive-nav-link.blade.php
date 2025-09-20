@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 dark:border-indigo-300
       text-start text-base font-medium
       text-indigo-700 dark:text-indigo-300
       bg-indigo-50 dark:bg-gray-900/60
       focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200
       focus:bg-indigo-100 dark:focus:bg-gray-900/60
       focus:border-indigo-700 dark:focus:border-indigo-500 transition'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent
       text-start text-base font-medium
       text-gray-600 dark:text-gray-300
       hover:text-gray-800 dark:hover:text-gray-100
       hover:bg-gray-50 dark:hover:bg-gray-900/60
       hover:border-gray-300 dark:hover:border-gray-600
       focus:outline-none focus:text-gray-800 dark:focus:text-gray-100
       focus:bg-gray-50 dark:focus:bg-gray-900/60
       focus:border-gray-300 dark:focus:border-gray-600 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

