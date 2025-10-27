<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title') - FEATI Pulse Student Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script>
    (() => {
    const ls = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const wantDark = ls ? ls === 'dark' : prefersDark;
    document.documentElement.classList.toggle('dark', wantDark);
    window.toggleTheme = () => {
        const root = document.documentElement.classList;
        root.toggle('dark');
        localStorage.setItem('theme', root.contains('dark') ? 'dark' : 'light');
    };
    })();
    </script>


    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        referrerpolicy="no-referrer" />


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
            @click.away="sidebarOpen = false">
            <div class="flex items-center justify-center h-16 bg-green-600 dark:bg-green-800 text-white text-xl font-semibold">
                <i class="fas fa-graduation-cap mr-2"></i> Student Portal
            </div>
            <nav class="flex-1 px-2 py-4 space-y-1">
                <x-sidebar-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('student.violations.index')" :active="request()->routeIs('student.violations.*')">
                    <i class="fas fa-exclamation-triangle mr-3"></i> Violations
                </x-sidebar-link>
                <x-sidebar-link :href="route('student.applications.index')" :active="request()->routeIs('student.applications.*')">
                    <i class="fas fa-file-alt mr-3"></i> Applications
                </x-sidebar-link>
                <x-sidebar-link :href="route('student.events.index')" :active="request()->routeIs('student.events.*')">
                    <i class="fas fa-calendar-alt mr-3"></i> Events
                </x-sidebar-link>
                <x-sidebar-link :href="route('student.reports.index')" :active="request()->routeIs('student.reports.*')">
                    <i class="fas fa-chart-bar mr-3"></i> Reports
                </x-sidebar-link>
                <x-sidebar-link :href="route('student.profile')" :active="request()->routeIs('student.profile')">
                    <i class="fas fa-user mr-3"></i> Profile
                </x-sidebar-link>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">@yield('page-title')</h1>
                <div class="flex items-center">
                    <button type="button" onclick="toggleTheme()"
                            class="me-3 inline-flex items-center justify-center rounded-md px-3 py-2
                                   text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900">
                        <i class="fa-solid fa-moon dark:hidden"></i>
                        <i class="fa-solid fa-sun hidden dark:inline"></i>
                        <span class="sr-only">Toggle theme</span>
                    </button>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium
                                           rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800
                                           hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('student.profile')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
