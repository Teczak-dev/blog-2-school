<x-layout>
    <!-- Main Content with modern background -->
    <main class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Back navigation -->
            <div class="mb-6">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm px-4 py-2 rounded-xl shadow-md hover:shadow-lg border dark:border-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do postów
                </a>
            </div>

            <!-- Post Header -->
            <article class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden mb-8">
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
                <div class="flex items-center justify-between mb-6">
                    <div></div> <!-- Spacer -->
                    
                    @if (auth()->check() && auth()->id() === $post->user_id)
                        <a href="{{ route('posts.edit', $post->id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edytuj
                        </a>
                    @endif
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 dark:border-green-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20" fill="currentColor">
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
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-full flex items-center justify-center text-lg font-semibold text-white">
                            @if($post->user)
                                {{ $post->user->name[0] }}
                            @else
                                {{ $post->author[0] }}
                            @endif
                        </div>
                        <div>
                            @if($post->user)
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('users.profile', $post->user) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $post->user->name }}
                                    </a>
                                </p>
                            @else
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $post->author }}</p>
                            @endif
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $post->created_at->format('d.m.Y') }} • {{ $post->read_time_minutes ?? 5 }} min czytania</p>
                        </div>
                    </div>
                    
                    <!-- Social Actions - tylko jeśli to nie jest nasz post i jesteśmy zalogowani -->
                    @auth
                        @if($post->user && $post->user->id !== auth()->id())
                            <div class="flex items-center gap-2 ml-auto mr-4">
                                @php
                                    $currentUser = Auth::user();
                                    $isFollowing = $currentUser->isFollowing($post->user);
                                    $friendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $post) {
                                        $query->where('requester_id', $currentUser->id)->where('addressee_id', $post->user->id);
                                    })->orWhere(function ($query) use ($currentUser, $post) {
                                        $query->where('requester_id', $post->user->id)->where('addressee_id', $currentUser->id);
                                    })->first();
                                    
                                    $canSendFriendRequest = !$friendship;
                                    $canAcceptFriendRequest = $friendship && $friendship->status === 'pending' && $friendship->addressee_id === $currentUser->id;
                                    $isFriend = $friendship && $friendship->status === 'accepted';
                                    $requestSent = $friendship && $friendship->status === 'pending' && $friendship->requester_id === $currentUser->id;
                                @endphp

                                <!-- Follow Button -->
                                <button 
                                    onclick="toggleFollow({{ $post->user->id }}, this)"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200 {{ $isFollowing ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                    {{ $isFollowing ? 'Przestań obserwować' : 'Obserwuj' }}
                                </button>

                                <!-- Friend Button -->
                                @if($isFriend)
                                    <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-md">
                                        ✓ Znajomy
                                    </span>
                                @elseif($canAcceptFriendRequest)
                                    <button 
                                        onclick="acceptFriendRequest({{ $post->user->id }}, this)"
                                        class="px-3 py-1 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700">
                                        Zaakceptuj
                                    </button>
                                @elseif($requestSent)
                                    <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-700 rounded-md">
                                        Oczekuje
                                    </span>
                                @elseif($canSendFriendRequest)
                                    <button 
                                        onclick="sendFriendRequest({{ $post->user->id }}, this)"
                                        class="px-3 py-1 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700">
                                        Dodaj do znajomych
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endauth
                    
                    <div class="flex flex-wrap gap-2">
                        <!-- Category -->
                        @if($post->category)
                            <span class="px-4 py-2 text-sm font-semibold rounded-full" 
                                  style="{{ $post->getCategoryInlineStyles() }}">
                                {{ $post->category }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    {{ $post->title }}
                </h1>

                @if ($post->lead)
                    <!-- Lead -->
                    <div class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        {!! $post->lead !!}
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none">
                    <div class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed whitespace-pre-line">
                        {!! $post->content !!}
                    </div>
                </div>

                <!-- Hashtags -->
                @if($post->tags && count($post->tags) > 0)
                    <div class="mt-8 mb-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-3">
                            @foreach($post->tags as $tag)
                                <span class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 cursor-pointer font-medium transition-colors duration-200">
                                    #{{ strtolower(str_replace(' ', '', $tag)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif



                <!-- Social Share -->
                @php
                    $shareAbsoluteUrl = route('posts.show', $post->id);
                @endphp
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Udostępnij:</span>
                    <button type="button"
                       class="social-share-btn text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                       data-share-platform="facebook"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij na Facebooku">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </button>
                    <button type="button"
                       class="social-share-btn text-sky-500 dark:text-sky-400 hover:text-sky-600 dark:hover:text-sky-300"
                       data-share-platform="x"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij na X">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </button>
                    <button type="button"
                       class="social-share-btn text-blue-700 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-200"
                       data-share-platform="linkedin"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij na LinkedIn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.762 2.239 5 5 5h14c2.762 0 5-2.238 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764 0-.975.784-1.764 1.75-1.764s1.75.789 1.75 1.764c0 .974-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-1.337-.027-3.058-1.864-3.058-1.867 0-2.154 1.46-2.154 2.965v5.697h-3v-11h2.881v1.502h.041c.401-.761 1.381-1.563 2.844-1.563 3.042 0 3.604 2.003 3.604 4.609v6.452z"/>
                        </svg>
                    </button>
                    <button type="button"
                       class="social-share-btn text-blue-500 dark:text-blue-300 hover:text-blue-700 dark:hover:text-blue-100"
                       data-share-platform="messenger"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij w Messengerze">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 4.97 0 11.108c0 3.495 1.745 6.613 4.472 8.65V24l4.012-2.207c1.07.296 2.2.454 3.516.454 6.627 0 12-4.97 12-11.108C24 4.97 18.627 0 12 0zm1.19 14.964l-3.055-3.258-5.96 3.258 6.556-6.959 3.133 3.258 5.881-3.258-6.556 6.959z"/>
                        </svg>
                    </button>
                    <button type="button"
                       class="social-share-btn text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300"
                       data-share-platform="instagram"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij na Instagramie">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2C4.578 2 2 4.578 2 7.75v8.5C2 19.422 4.578 22 7.75 22h8.5c3.172 0 5.75-2.578 5.75-5.75v-8.5C22 4.578 19.422 2 16.25 2h-8.5zm0 1.5h8.5a4.255 4.255 0 0 1 4.25 4.25v8.5a4.255 4.255 0 0 1-4.25 4.25h-8.5a4.255 4.255 0 0 1-4.25-4.25v-8.5A4.255 4.255 0 0 1 7.75 3.5zm9.5 1.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5zM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 1.5A3.5 3.5 0 1 1 12 15.5 3.5 3.5 0 0 1 12 8.5z"/>
                        </svg>
                    </button>
                    <button type="button"
                       class="social-share-btn text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300"
                       data-share-platform="whatsapp"
                       data-share-url="{{ $shareAbsoluteUrl }}"
                       data-share-title="{{ $post->title }}"
                       aria-label="Udostępnij na WhatsApp">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.51 0 .17 5.33.17 11.88c0 2.09.55 4.13 1.59 5.93L0 24l6.37-1.67a11.86 11.86 0 0 0 5.69 1.45h.01c6.55 0 11.88-5.33 11.88-11.88 0-3.17-1.24-6.16-3.43-8.42zm-8.46 18.3h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.78.99 1.01-3.69-.24-.38a9.87 9.87 0 0 1-1.51-5.23c0-5.45 4.44-9.88 9.9-9.88 2.64 0 5.12 1.03 6.98 2.89a9.82 9.82 0 0 1 2.9 6.99c0 5.46-4.44 9.9-9.86 9.9zm5.43-7.39c-.3-.15-1.77-.87-2.04-.96-.27-.1-.47-.15-.66.15-.2.3-.76.96-.94 1.16-.17.2-.34.22-.64.08-.3-.15-1.25-.46-2.38-1.46a8.94 8.94 0 0 1-1.65-2.06c-.17-.3-.02-.46.12-.6.13-.13.3-.34.45-.51.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.66-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.03 1-1.03 2.44 0 1.43 1.06 2.82 1.2 3.02.15.2 2.08 3.18 5.04 4.45.7.3 1.26.48 1.69.62.71.22 1.36.19 1.88.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                        </svg>
                    </button>
                    <button type="button"
                            class="copy-share-link text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
                            data-share-url="{{ $shareAbsoluteUrl }}"
                            aria-label="Kopiuj link do posta">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8m-8-4h8m-8-4h8m2 12H6a2 2 0 01-2-2V6a2 2 0 012-2h9l5 5v9a2 2 0 01-2 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Komentarze (<span id="comments-count-value">{{ $post->approvedComments()->topLevel()->count() }}</span>)
                </h2>
                
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select id="comment-sort" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                            onchange="window.location.href = '{{ route('posts.show', $post->id) }}?sort=' + this.value">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Najnowsze</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Najstarsze</option>
                        <option value="most_liked" {{ $sort === 'most_liked' ? 'selected' : '' }}>Najbardziej lubiane</option>
                    </select>
                </div>
            </div>

            <!-- Comment Form -->
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dodaj komentarz</h3>
                
                @if (auth()->check())
                    <!-- Logged user form -->
                    <form method="POST" action="{{ route('comments.store', $post->id) }}" class="space-y-4" data-comment-form="true">
                        @csrf
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Komentarz *
                            </label>
                            <textarea id="content" name="content" required rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                                placeholder="Podziel się swoimi przemyśleniami...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Opublikuj komentarz
                            </button>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Zalogowany jako <strong>{{ auth()->user()->name }}</strong></p>
                        </div>
                    </form>
                @else
                    <!-- Guest user form -->
                    <form method="POST" action="{{ route('comments.store', $post->id) }}" class="space-y-4" data-comment-form="true">
                        @csrf
                        
                        <!-- Info about moderation -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Twój komentarz zostanie opublikowany po zatwierdzeniu przez administratora. 
                                        <a href="{{ route('login') }}" class="underline hover:no-underline">Zaloguj się</a> 
                                        aby komentować bez moderacji.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        </div>

                        <!-- Comment Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Komentarz *
                            </label>
                            <textarea id="content" name="content" required rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                                placeholder="Podziel się swoimi przemyśleniami...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Wyślij komentarz
                            </button>
                            <p class="text-sm text-gray-500 dark:text-gray-400">* Pola wymagane</p>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Comments List -->
            <div id="comments-container">
                @php
                    $commentsQuery = $post->approvedComments()->topLevel();
                    
                    switch ($sort) {
                        case 'oldest':
                            $commentsQuery->orderBy('created_at', 'asc');
                            break;
                        case 'most_liked':
                            $commentsQuery->orderBy('likes_count', 'desc')->orderBy('created_at', 'desc');
                            break;
                        case 'newest':
                        default:
                            $commentsQuery->orderBy('created_at', 'desc');
                            break;
                    }
                    
                    $approvedComments = $commentsQuery->take(10)->get();
                    $totalComments = $post->approvedComments()->topLevel()->count();
                @endphp
                
                @if($totalComments > 0)
                    <div id="comments-list" class="space-y-6">
                        @foreach($approvedComments as $comment)
                            <x-comment :comment="$comment" />
                        @endforeach
                    </div>
                    
                    @if($totalComments > 10)
                        <div class="text-center mt-6">
                            <button id="load-more-comments" 
                                data-post-id="{{ $post->id }}"
                                data-offset="10"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Pokaż więcej komentarzy ({{ $totalComments - 10 }})
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>Brak komentarzy. Bądź pierwszy który skomentuje ten post!</p>
                    </div>
                @endif
            </div>
        </section>


        <!-- Related Posts -->
        @if($relatedPosts->count() > 0)
        <section class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Powiązane artykuły</h2>
            <div class="grid gap-6 md:grid-cols-{{ $relatedPosts->count() <= 3 ? $relatedPosts->count() : '3' }}">
                @foreach($relatedPosts as $relatedPost)
                <a href="{{ route('posts.show', $relatedPost->id) }}" class="group">
                    <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            @if($relatedPost->photo)
                                <img src="{{ asset('storage/' . $relatedPost->photo) }}" alt="{{ $relatedPost->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-5xl">📝</span>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block px-2 py-1 text-xs rounded-full" 
                                      style="{{ $relatedPost->getCategoryInlineStyles() }}">
                                    {{ $relatedPost->category }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-2 mb-2">
                                {{ $relatedPost->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $relatedPost->read_time_minutes }} min czytania</p>
                        </div>
                    </article>
                </a>
                @endforeach
            </div>
        </section>
        @endif

    </main>

    <!-- JavaScript for voting and replies -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Vote script loaded');
            
            // Handle voting (event delegation for current and future comment nodes)
            document.addEventListener('click', async function (e) {
                const button = e.target.closest('.vote-btn');
                if (!button) {
                    return;
                }

                e.preventDefault();

                const commentId = button.dataset.commentId;
                const voteType = button.dataset.voteType;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                if (!csrfToken) {
                    alert('Błąd: Brak tokenu CSRF. Odśwież stronę.');
                    return;
                }

                try {
                    const response = await fetch(`/comments/${commentId}/vote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ vote_type: voteType })
                    });

                    if (!response.ok) {
                        throw new Error('Vote request failed');
                    }

                    const data = await response.json();

                    if (data.success) {
                        const commentEl = document.getElementById(`comment-${commentId}`);
                        if (!commentEl) {
                            return;
                        }

                        commentEl.querySelector('.likes-count').textContent = data.likes_count;
                        commentEl.querySelector('.dislikes-count').textContent = data.dislikes_count;

                        const likeBtn = commentEl.querySelector('[data-vote-type="like"]');
                        const dislikeBtn = commentEl.querySelector('[data-vote-type="dislike"]');
                        const likeSvg = likeBtn.querySelector('svg');
                        const dislikeSvg = dislikeBtn.querySelector('svg');

                        likeSvg.classList.remove('fill-green-600', 'text-green-600', 'dark:fill-green-400', 'dark:text-green-400');
                        dislikeSvg.classList.remove('fill-red-600', 'text-red-600', 'dark:fill-red-400', 'dark:text-red-400');
                        likeBtn.removeAttribute('data-active');
                        dislikeBtn.removeAttribute('data-active');

                        if (data.user_vote === 'like') {
                            likeSvg.classList.add('fill-green-600', 'text-green-600', 'dark:fill-green-400', 'dark:text-green-400');
                            likeBtn.setAttribute('data-active', 'true');
                        } else if (data.user_vote === 'dislike') {
                            dislikeSvg.classList.add('fill-red-600', 'text-red-600', 'dark:fill-red-400', 'dark:text-red-400');
                            dislikeBtn.setAttribute('data-active', 'true');
                        }
                    }
                } catch (error) {
                    console.error('Vote error:', error);
                    alert('Wystąpił błąd podczas głosowania. Spróbuj ponownie.');
                }
            });
            
            // Handle reply toggles, cancel and share copy via event delegation
            document.addEventListener('click', function (e) {
                const replyToggleButton = e.target.closest('.reply-toggle-btn');
                if (replyToggleButton) {
                    e.preventDefault();
                    const commentId = replyToggleButton.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    if (!replyForm) {
                        return;
                    }
                    replyForm.classList.toggle('hidden');
                    if (!replyForm.classList.contains('hidden')) {
                        replyForm.querySelector('textarea')?.focus();
                    }
                    return;
                }

                const cancelReplyButton = e.target.closest('.cancel-reply-btn');
                if (cancelReplyButton) {
                    e.preventDefault();
                    const commentId = cancelReplyButton.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    if (!replyForm) {
                        return;
                    }
                    replyForm.classList.add('hidden');
                    replyForm.querySelector('form')?.reset();
                    return;
                }

                const copyButton = e.target.closest('.copy-share-link');
                if (copyButton) {
                    e.preventDefault();
                    const shareUrl = copyButton.dataset.shareUrl;
                    if (!shareUrl || !navigator.clipboard) {
                        return;
                    }
                    navigator.clipboard.writeText(shareUrl).then(() => {
                        copyButton.classList.add('text-green-600', 'dark:text-green-400');
                        setTimeout(() => {
                            copyButton.classList.remove('text-green-600', 'dark:text-green-400');
                        }, 1200);
                    });
                    return;
                }

                const socialShareButton = e.target.closest('.social-share-btn');
                if (socialShareButton) {
                    e.preventDefault();
                    const platform = socialShareButton.dataset.sharePlatform;
                    const shareUrl = socialShareButton.dataset.shareUrl || window.location.href;
                    const shareTitle = socialShareButton.dataset.shareTitle || document.title;
                    const encodedUrl = encodeURIComponent(shareUrl);
                    const encodedTitle = encodeURIComponent(shareTitle);
                    const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);

                    if (platform === 'instagram') {
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(shareUrl);
                        }
                        window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
                        return;
                    }

                    let targetUrl = '';
                    switch (platform) {
                        case 'facebook':
                            targetUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
                            break;
                        case 'x':
                            targetUrl = `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`;
                            break;
                        case 'linkedin':
                            targetUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
                            break;
                        case 'messenger':
                            targetUrl = `https://www.facebook.com/dialog/send?link=${encodedUrl}&app_id=291494419107518&redirect_uri=${encodedUrl}`;
                            break;
                        case 'whatsapp':
                            targetUrl = `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`;
                            break;
                        default:
                            return;
                    }

                    if (isLocalhost) {
                        alert('Link prowadzi do localhost, więc część serwisów społecznościowych może odrzucić udostępnienie. Najpierw wystaw aplikację pod publicznym adresem.');
                    }

                    window.open(targetUrl, '_blank', 'noopener,noreferrer');
                }
            });
        });

        // Social Functions
        async function toggleFollow(userId, button) {
            const isFollowing = button.textContent.trim().includes('Przestań');
            
            try {
                const response = await fetch(`/users/${userId}/${isFollowing ? 'unfollow' : 'follow'}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    if (isFollowing) {
                        button.className = 'px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200 bg-blue-600 text-white hover:bg-blue-700';
                        button.textContent = 'Obserwuj';
                    } else {
                        button.className = 'px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                        button.textContent = 'Przestań obserwować';
                    }
                } else {
                    alert('Błąd podczas zmiany obserwowania');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas zmiany obserwowania');
            }
        }

        async function sendFriendRequest(userId, button) {
            try {
                const response = await fetch(`/users/${userId}/friend-request`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    button.className = 'px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-700 rounded-md';
                    button.textContent = 'Oczekuje';
                    button.onclick = null;
                } else {
                    alert('Błąd podczas wysyłania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas wysyłania zaproszenia');
            }
        }

        async function acceptFriendRequest(userId, button) {
            try {
                const response = await fetch(`/users/${userId}/friend-request`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    button.className = 'px-3 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-md';
                    button.textContent = '✓ Znajomy';
                    button.onclick = null;
                } else {
                    alert('Błąd podczas akceptowania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas akceptowania zaproszenia');
            }
        }
    </script>

</x-layout>
