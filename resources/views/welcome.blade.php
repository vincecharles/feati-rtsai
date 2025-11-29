<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FEATI PRISM - Platform for Records, Incidents & Student Management</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-yellow-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold">
                        <span class="text-blue-600">FEATI</span>
                        <span class="text-yellow-500">PRISM</span>
                    </h1>
                </div>
                
                <!-- Navigation -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-2 text-red-600 hover:text-red-800 border border-red-600 hover:border-red-800 rounded-lg transition-all duration-200 font-medium">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-6 py-2 text-blue-600 hover:text-blue-800 transition-colors duration-200 font-medium">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
                Welcome to <span class="text-blue-600">FEATI PRISM</span>
            </h2>
            <p class="text-xl md:text-2xl text-gray-600 mb-8 animate-slide-up">
                Platform for Records, Incidents & Student Management
            </p>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto animate-slide-up-delay">
                Streamlining student records, applications, and academic information management for FEATI University.
            </p>
        </div>
    </section>

    <!-- Vision, Mission, Core Values Section -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            
            <!-- Vision -->
            <div class="mb-16 animate-fade-in">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">Vision</h3>
                    <div class="w-20 h-1 bg-yellow-500 mx-auto rounded-full"></div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-yellow-50 rounded-2xl p-8 shadow-lg">
                    <p class="text-lg text-gray-700 text-center leading-relaxed">
                        FEATI will be a leading knowledge center in the country and advance new ideas. It will foster an 
                        environment that will enable faculty, students, and all personnel and alumni to become responsible, life-long 
                        learners and transformed individuals committed to service of God, the world, country, and family.
                    </p>
                </div>
            </div>

            <!-- Mission -->
            <div class="mb-16 animate-slide-up">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">Mission</h3>
                    <div class="w-20 h-1 bg-yellow-500 mx-auto rounded-full"></div>
                </div>
                <div class="bg-gradient-to-r from-yellow-50 to-blue-50 rounded-2xl p-8 shadow-lg">
                    <p class="text-lg text-gray-700 mb-4 text-center">
                        FEATI University will accomplish its vision by:
                    </p>
                    <ul class="space-y-3 max-w-3xl mx-auto">
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-3 text-xl">•</span>
                            <span class="text-gray-700">Promoting basic and applied research and development</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-3 text-xl">•</span>
                            <span class="text-gray-700">Generating innovative technologies</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-3 text-xl">•</span>
                            <span class="text-gray-700">Teaching creatively to develop critical-thinking abilities</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-3 text-xl">•</span>
                            <span class="text-gray-700">Inspiring students to achieve full academic, research, and spiritual potentials in arts, sciences, technology, and humanities</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Core Values -->
            <div class="animate-slide-up-delay">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">Core Values</h3>
                    <div class="w-20 h-1 bg-yellow-500 mx-auto rounded-full"></div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-yellow-50 rounded-2xl p-8 shadow-lg">
                    <p class="text-lg text-gray-700 mb-6 text-center">
                        FEATI is a values-centered university geared towards achieving its vision and mission through the following CORE VALUES:
                    </p>
                    <div class="grid md:grid-cols-5 gap-6 max-w-4xl mx-auto">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                                I
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-1">Integrity</h4>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                                S
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-1">Scholarship</h4>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                                A
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-1">Accountability</h4>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                                E
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-1">Equality</h4>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                                P
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-1">Patriotism</h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-white py-8 px-4 border-t">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-gray-600">&copy; {{ date('Y') }} FEATI University. All rights reserved.</p>
            <p class="text-sm text-gray-500 mt-2">Powered by FEATI PRISM</p>
        </div>
    </footer>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out 0.3s both;
        }

        .animate-slide-up-delay {
            animation: slideUp 0.8s ease-out 0.6s both;
        }
    </style>
</body>
</html>
