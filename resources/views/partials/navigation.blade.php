    <!-- Modern Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📝</span>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Blog
                    </span>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('posts.index') }}"
                        class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50">
                        📚 Wszystkie Posty
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50">
                            🏠 Dashboard
                        </a>
                        <a href="{{ route('posts.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nowy Post
                        </a>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:text-indigo-600 rounded-lg transition-colors duration-200 hover:bg-indigo-50">
                                <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Profil
                                    </a>
                                    <div class="border-t border-gray-100 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 w-full text-left">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Wyloguj
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50">
                            Logowanie
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Rejestracja
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('posts.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                    📚 Wszystkie Posty
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('posts.create') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                        ➕ Nowy Post
                    </a>
                    <div class="border-t border-gray-200 pt-2">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                            👤 Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600">
                                🚪 Wyloguj
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                        🔐 Logowanie
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-indigo-600 font-medium hover:bg-indigo-50">
                        📝 Rejestracja
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
