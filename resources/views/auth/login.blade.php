<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IBCC Portal') }} - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900">
    <div class="flex min-h-full">
        <!-- Left Side: Branding/Image -->
        <div class="relative hidden w-0 flex-1 lg:block bg-gray-900">
            <!-- Background Image/Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-blue-900 overflow-hidden">
                <!-- Decorative pattern -->
                <svg class="absolute left-0 top-0 h-full w-full opacity-20 transform scale-150 origin-top-left" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 0 L50 100 L100 0 Z" fill="white" />
                </svg>
                <div class="absolute inset-0 flex flex-col justify-center items-center p-12 text-white">
                   <div class="mb-8">
                        <img src="{{ asset('images/IBCC Logo.png') }}" alt="IBCC Logo" class="w-32 h-32 object-contain filter drop-shadow-lg" />
                   </div>
                   <h1 class="text-4xl font-bold mb-4 tracking-tight">Welcome Back</h1>
                   <p class="text-lg text-indigo-100 text-center max-w-md">Access your dashboard to manage centers, couriers, and dispatches efficiently.</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Mobile Logo (visible only on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('images/IBCC Logo.png') }}" alt="IBCC Logo" class="w-24 h-24 mx-auto object-contain mb-2" />
                    <h2 class="mt-4 text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
                </div>

                <div class="hidden lg:block">
                    <h2 class="text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Please enter your details to continue.
                    </p>
                </div>
                
                <div class="mt-10">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email address')" />
                            <div class="mt-2">
                                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <div class="mt-2">
                                <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="remember">
                                <label for="remember_me" class="ml-2 block text-sm leading-6 text-gray-900">{{ __('Remember me') }}</label>
                            </div>

                            <div class="text-sm leading-6">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <x-primary-button class="w-full flex justify-center py-3 text-sm font-semibold shadow-lg transform transition hover:scale-[1.02] duration-200">
                                {{ __('Sign in') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Register Link -->
                    @if (Route::has('register'))
                    <p class="mt-10 text-center text-sm text-gray-500">
                        Not a member?
                        <a href="{{ route('register') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500 transition duration-150 ease-in-out">Register</a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
