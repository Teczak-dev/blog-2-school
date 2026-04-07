<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
            <!-- Modern Navigation Header -->
            <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">📝</span>
                            <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Blog
                            </span>
                        </div>
                        <a href="{{ route('posts.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Powrót do Blogów
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="flex flex-col justify-center items-center pt-12 sm:pt-20">
                <div class="w-full sm:max-w-md">
                    <!-- Logo Section -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg mb-4">
                            <span class="text-2xl">📝</span>
                        </div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Witaj w Blogu
                        </h1>
                        <p class="text-gray-600 mt-2">Zaloguj się aby kontynuować</p>
                    </div>

                    <!-- Form Container -->
                    <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl border border-gray-200/50 overflow-hidden">
                        <div class="px-8 py-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
