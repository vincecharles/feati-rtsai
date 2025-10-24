<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - FEATI Pulse</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-yellow-50 py-12">
    <div class="w-full max-w-md px-6">
        <!-- Logo/Title -->
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-5xl font-bold mb-2">
                <span class="text-blue-600">FEATI</span>
                <span class="text-yellow-500">Pulse</span>
            </h1>
            <p class="text-gray-600 text-sm">Student Information System</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 animate-slide-up">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Create Account</h2>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-center">
                    <input id="terms" 
                           type="checkbox" 
                           name="terms" 
                           required
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 @error('terms') border-red-500 @enderror">
                    <label for="terms" class="ml-2 text-sm text-gray-700">
                        I agree to the 
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Terms and Conditions</a>
                    </label>
                </div>
                @error('terms')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02]">
                    Register
                </button>

                <!-- Login Link -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    Already have an account? 
                    <a href="{{ route('login') }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        Login here
                    </a>
                </p>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-500 text-sm animate-fade-in-delay">
            <p>&copy; {{ date('Y') }} FEATI University</p>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-fade-in-delay {
            animation: fadeIn 0.8s ease-out 0.3s both;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out 0.2s both;
        }
    </style>
</body>
</html>
