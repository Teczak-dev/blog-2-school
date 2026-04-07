<x-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    📝 Najnowsze Posty
                </h1>
                <p class="text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                    Odkryj najnowsze artykuły z świata programowania, technologii i rozwoju osobistego
                </p>
                @auth
                    <a href="{{ route('posts.create') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-indigo-600 font-bold rounded-2xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Napisz nowy post
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-indigo-600 font-bold rounded-2xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105">
                        🚀 Zacznij pisać
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Messages -->
        @if (session('success'))
            <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Filters/Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Szukaj postów..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option>Wszystkie kategorie</option>
                <option>Laravel</option>
                <option>React</option>
                <option>AI & Copilot</option>
            </select>
        </div>

        <!-- Posts Grid -->
        @if($posts->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $item)
                <!-- Post Card X -->
                <article
                    class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        @if ($item->photo)
                            <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-6xl">📝</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                                {{ $item->slug }}
                            </span>
                            <span class="text-gray-500 text-sm">5 min czytania</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                            <a href="{{ route('posts.show', $item->slug) }}">{{ $item->title }}</a>
                        </h3>
                        <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {!! $item->lead ?? Str::limit(strip_tags($item->content), 150) !!}
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                    {{ $item->author[0] }}
                                </div>
                                <span class="text-sm text-gray-700 font-medium">{{ $item->author }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <span class="text-4xl">📝</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak postów</h3>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">
                    Nie ma jeszcze żadnych postów do wyświetlenia. 
                    @if (auth()->check())
                        Napisz pierwszy post!
                    @else
                        Zaloguj się, aby napisać pierwszy post!
                    @endif
                </p>
                @if (auth()->check())
                    <a href="{{ route('posts.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        ✍️ Napisz pierwszy post
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        🔑 Zaloguj się
                    </a>
                    </a>
                @endif
            </div>
        @endif

            {{-- <!-- Post Card 2 -->
            <article
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                    <span class="text-6xl">🤖</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            AI & Copilot
                        </span>
                        <span class="text-gray-500 text-sm">8 min czytania</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                        <a href="post-detail.html">GitHub Copilot Agent Mode w praktyce</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        Sprawdź, jak wykorzystać agenta AI do generowania całych komponentów aplikacji. Przykłady z
                        życia wzięte!
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                AN
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Anna Nowak</span>
                        </div>
                        <span class="text-sm text-gray-500">1 dzień temu</span>
                    </div>
                </div>
            </article>

            <!-- Post Card 3 -->
            <article
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                    <span class="text-6xl">⚛️</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-pink-100 text-pink-800 text-xs font-semibold rounded-full">
                            React
                        </span>
                        <span class="text-gray-500 text-sm">12 min czytania</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                        <a href="post-detail.html">Inertia.js - most między Laravel a React</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        Zbuduj SPA bez API! Poznaj Inertia.js i dowiedz się, dlaczego to game-changer w ekosystemie
                        Laravel.
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                MZ
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Michał Zając</span>
                        </div>
                        <span class="text-sm text-gray-500">3 dni temu</span>
                    </div>
                </div>
            </article>

            <!-- Post Card 4 -->
            <article
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                    <span class="text-6xl">🎨</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                            Laravel
                        </span>
                        <span class="text-gray-500 text-sm">6 min czytania</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                        <a href="post-detail.html">Laravel Filament - admin panel w 15 minut</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        RAD (Rapid Application Development) w praktyce. Stwórz profesjonalny panel administracyjny bez
                        pisania CSS.
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                KP
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Kasia Pawlak</span>
                        </div>
                        <span class="text-sm text-gray-500">5 dni temu</span>
                    </div>
                </div>
            </article>

            <!-- Post Card 5 -->
            <article
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                    <span class="text-6xl">🔒</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            Security
                        </span>
                        <span class="text-gray-500 text-sm">10 min czytania</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                        <a href="post-detail.html">Bezpieczeństwo w Laravel - Top 10 zasad</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        CSRF, XSS, SQL Injection? Dowiedz się, jak Laravel chroni Twoją aplikację i co musisz wiedzieć
                        jako developer.
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                PK
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Piotr Kowal</span>
                        </div>
                        <span class="text-sm text-gray-500">1 tydzień temu</span>
                    </div>
                </div>
            </article>

            <!-- Post Card 6 -->
            <article
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                    <span class="text-6xl">📊</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-violet-100 text-violet-800 text-xs font-semibold rounded-full">
                            Database
                        </span>
                        <span class="text-gray-500 text-sm">15 min czytania</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                        <a href="post-detail.html">Eloquent ORM - relacje w praktyce</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        hasMany, belongsTo, morphMany - przewodnik po relacjach w Eloquent. Od podstaw do zaawansowanych
                        przypadków.
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                EW
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Ewa Wiśniewska</span>
                        </div>
                        <span class="text-sm text-gray-500">2 tygodnie temu</span>
                    </div>
                </div>
            </article> --}}

        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <nav class="flex gap-2">
                <button
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                    disabled>
                    Poprzednia
                </button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium">
                    1
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    2
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    3
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Następna
                </button>
            </nav>
        </div>
    </main>



</x-layout>
