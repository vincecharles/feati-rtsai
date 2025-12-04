@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex items-center px-4 py-3 text-white bg-green-700 dark:bg-green-900 rounded-lg transition-colors duration-200'
    : 'flex items-center px-4 py-3 text-gray-300 hover:bg-green-700 dark:hover:bg-green-900 hover:text-white rounded-lg transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
