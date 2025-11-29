<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - FEATI PRISM</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-yellow-50 overflow-hidden">
    <div class="text-center px-6 py-12 max-w-4xl fade-in-scale">
        <!-- FEATI PRISM Logo/Title -->
        <div class="mb-8">
            <h1 class="text-7xl md:text-9xl font-bold mb-4">
                <span class="text-blue-600">FEATI</span>
                <span class="text-yellow-500">PRISM</span>
            </h1>
        </div>

        <!-- Welcome Message -->
        <div class="space-y-6">
            <div class="text-5xl font-light text-gray-700">
                Welcome,
            </div>
            <div class="text-6xl md:text-7xl font-semibold text-blue-600">
                {{ auth()->user()->name }}
            </div>
            <div class="mt-8 text-gray-500 text-lg">
                Redirecting to your dashboard...
            </div>
            
            <!-- Loading dots -->
            <div class="flex justify-center gap-2 mt-6">
                <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        .fade-in-scale {
            animation: fadeInScale 0.8s ease-out;
        }

        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }
    </style>

    <script>
        // Redirect to dashboard after 5 seconds with fade out
        setTimeout(() => {
            document.body.classList.add('fade-out');
            setTimeout(() => {
                window.location.href = '{{ route('dashboard') }}';
            }, 500);
        }, 5000);
    </script>
</body>
</html>
