<x-layout>
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Post Header -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <!-- Featured Image -->
            <div class="h-96 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                @if ($post->photo)
                    <img src="{{ asset('storage/' . $post->photo) }}" alt="{{ $post->title }}" 
                         class="w-full h-full object-cover">
                @else
                    <span class="text-9xl">📝</span>
                @endif
            </div>

            <!-- Post Content -->
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Powrót do postów
                    </a>
                    
                    @if (auth()->check() && auth()->id() === $post->user_id)
                        <a href="{{ route('posts.edit', $post->slug) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edytuj
                        </a>
                    @endif
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Meta Info -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-lg font-semibold">
                            {{ $post->author[0] }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->author }}</p>
                            <p class="text-sm text-gray-500">Opublikowano: {{ $post->created_at->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <div class="ml-auto flex gap-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                            {{ $post->slug }}
                        </span>
                        <span class="text-gray-500 text-sm">5 min czytania</span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>

                @if ($post->lead)
                    <!-- Lead -->
                    <div class="text-xl text-gray-600 mb-8 leading-relaxed">
                        {!! $post->lead !!}
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none">
                    <div class="text-gray-700 mb-4 leading-relaxed whitespace-pre-line">
                        {!! $post->content !!}
                    </div>
                </div>

                <!-- Tags -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Tagi:</p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #laravel
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #php
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #docker
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #tutorial
                        </span>
                    </div>
                </div>

                <!-- Social Share -->
                <div class="mt-6 flex items-center gap-4">
                    <span class="text-sm text-gray-600">Udostępnij:</span>
                    <button class="text-blue-600 hover:text-blue-700">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </button>
                    <button class="text-sky-500 hover:text-sky-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                Komentarze ({{ $post->comments->count() }})
            </h2>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Comment Form -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dodaj komentarz</h3>
                <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="space-y-4">
                    @csrf
                    <!-- Name -->
                    <div>
                        <label for="author_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Twoje imię *
                        </label>
                        <input type="text" id="author_name" name="author_name" required
                            value="{{ old('author_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Jan Kowalski">
                        @error('author_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="author_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" id="author_email" name="author_email" required
                            value="{{ old('author_email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="jan@example.com">
                        <p class="mt-1 text-sm text-gray-500">Email nie będzie publikowany</p>
                        @error('author_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comment Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Komentarz *
                        </label>
                        <textarea id="content" name="content" required rows="5"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                            placeholder="Podziel się swoimi przemyśleniami...">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Opublikuj komentarz
                        </button>
                        <p class="text-sm text-gray-500">* Pola wymagane</p>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div class="space-y-6">
                @forelse($post->comments->sortByDesc('created_at') as $comment)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($comment->author_name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $comment->author_name }}</h4>
                                    <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $comment->content }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>Brak komentarzy. Bądź pierwszy który skomentuje ten post!</p>
                    </div>
                @endforelse
            </div>
        </section>


        <!-- Related Posts -->
        <section class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Powiązane artykuły</h2>
            <div class="grid gap-6 md:grid-cols-3">

                <!-- Related Post 1 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div
                            class="h-32 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                            <span class="text-5xl">🚀</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                Docker dla początkujących - pełny przewodnik
                            </h3>
                            <p class="text-sm text-gray-500">8 min czytania</p>
                        </div>
                    </article>
                </a>

                <!-- Related Post 2 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div
                            class="h-32 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                            <span class="text-5xl">⚡</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                Laravel Breeze - autentykacja w 5 minut
                            </h3>
                            <p class="text-sm text-gray-500">12 min czytania</p>
                        </div>
                    </article>
                </a>

                <!-- Related Post 3 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div
                            class="h-32 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                            <span class="text-5xl">🎨</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                Laravel Filament - admin panel w 15 minut
                            </h3>
                            <p class="text-sm text-gray-500">6 min czytania</p>
                        </div>
                    </article>
                </a>

            </div>
        </section>

    </main>

</x-layout>
