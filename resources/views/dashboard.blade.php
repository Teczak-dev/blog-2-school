<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏠 Panel Użytkownika
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Dashboard Card -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🏠</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">🏠 Panel</h1>
                            <p class="text-indigo-100">Witaj ponownie, {{ auth()->user()->name }}!</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Twój Blog</h2>
                        <p class="text-gray-600">Zarządzaj swoimi postami i twórz nowe treści.</p>
                    </div>
                    
                    <!-- Action Cards -->
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <!-- View Posts Card -->
                        <a href="{{ route('posts.index') }}" 
                           class="group p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">📚</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-900">Zobacz Posty</h3>
                                    <p class="text-sm text-blue-600">Przeglądaj wszystkie</p>
                                </div>
                            </div>
                            <p class="text-blue-700">Sprawdź wszystkie opublikowane posty w blogu.</p>
                        </a>

                        <!-- Create Post Card -->
                        <a href="{{ route('posts.create') }}" 
                           class="group p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">✍️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-green-900">Nowy Post</h3>
                                    <p class="text-sm text-green-600">Stwórz artykuł</p>
                                </div>
                            </div>
                            <p class="text-green-700">Napisz i opublikuj nowy post w swoim blogu.</p>
                        </a>

                        <!-- Profile Card -->
                        <a href="{{ route('profile.edit') }}" 
                           class="group p-6 bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl border border-purple-200 hover:border-purple-300 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">👤</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-purple-900">Profil</h3>
                                    <p class="text-sm text-purple-600">Twoje konto</p>
                                </div>
                            </div>
                            <p class="text-purple-700">Edytuj swoje dane osobowe i ustawienia.</p>
                        </a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-8 p-6 bg-gray-50 rounded-xl">
                        <h3 class="font-semibold text-gray-900 mb-4">Szybkie statystyki</h3>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ \App\Models\Post::where('user_id', auth()->id())->count() }}</div>
                                <div class="text-sm text-gray-600">Twoje posty</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ \App\Models\Post::count() }}</div>
                                <div class="text-sm text-gray-600">Wszystkie posty</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ \App\Models\Comment::count() }}</div>
                                <div class="text-sm text-gray-600">Komentarze</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
