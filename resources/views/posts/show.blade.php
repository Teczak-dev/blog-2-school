<x-layout>
    <!-- Main Content with modern background -->
    <main class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Back navigation -->
            <div class="mb-6">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-xl shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do postów
                </a>
            </div>

            <!-- Post Header -->
            <article class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden mb-8">
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
                            class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-lg font-semibold text-white">
                            {{ $post->author[0] }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->author }}</p>
                            <p class="text-sm text-gray-500">{{ $post->created_at->format('d.m.Y') }} • {{ $post->read_time_minutes ?? 5 }} min czytania</p>
                        </div>
                    </div>
                    <div class="ml-auto flex flex-wrap gap-2">
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

                <!-- Hashtags -->
                @if($post->tags && count($post->tags) > 0)
                    <div class="mt-8 mb-6 pt-6 border-t border-gray-200">
                        <div class="flex flex-wrap gap-3">
                            @foreach($post->tags as $tag)
                                <span class="text-indigo-600 hover:text-indigo-800 cursor-pointer font-medium transition-colors duration-200">
                                    #{{ strtolower(str_replace(' ', '', $tag)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif



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
        <section class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    Komentarze ({{ $post->approvedComments()->topLevel()->count() }})
                </h2>
                
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select id="comment-sort" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                            onchange="window.location.href = '{{ route('posts.show', $post->id) }}?sort=' + this.value">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Najnowsze</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Najstarsze</option>
                        <option value="most_liked" {{ $sort === 'most_liked' ? 'selected' : '' }}>Najbardziej lubiane</option>
                    </select>
                </div>
            </div>

            <!-- Comment Form -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dodaj komentarz</h3>
                
                @if (auth()->check())
                    <!-- Logged user form -->
                    <form method="POST" action="{{ route('comments.store', $post->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Komentarz *
                            </label>
                            <textarea id="content" name="content" required rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                                placeholder="Podziel się swoimi przemyśleniami...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Opublikuj komentarz
                            </button>
                            <p class="text-sm text-gray-500">Zalogowany jako <strong>{{ auth()->user()->name }}</strong></p>
                        </div>
                    </form>
                @else
                    <!-- Guest user form -->
                    <form method="POST" action="{{ route('comments.store', $post->id) }}" class="space-y-4">
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
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Komentarz *
                            </label>
                            <textarea id="content" name="content" required rows="4"
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
                                Wyślij komentarz
                            </button>
                            <p class="text-sm text-gray-500">* Pola wymagane</p>
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
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Pokaż więcej komentarzy ({{ $totalComments - 10 }})
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Brak komentarzy. Bądź pierwszy który skomentuje ten post!</p>
                    </div>
                @endif
            </div>
        </section>


        <!-- Related Posts -->
        @if($relatedPosts->count() > 0)
        <section class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Powiązane artykuły</h2>
            <div class="grid gap-6 md:grid-cols-{{ $relatedPosts->count() <= 3 ? $relatedPosts->count() : '3' }}">
                @foreach($relatedPosts as $relatedPost)
                <a href="{{ route('posts.show', $relatedPost->id) }}" class="group">
                    <article class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
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
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                {{ $relatedPost->title }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $relatedPost->read_time_minutes }} min czytania</p>
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
            
            const voteButtons = document.querySelectorAll('.vote-btn');
            console.log('Found vote buttons:', voteButtons.length);
            
            // Handle voting
            voteButtons.forEach(btn => {
                console.log('Attaching listener to button:', btn);
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const commentId = this.dataset.commentId;
                    const voteType = this.dataset.voteType;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    
                    console.log('Vote clicked:', { commentId, voteType, csrfToken });
                    
                    if (!csrfToken) {
                        console.error('CSRF token not found!');
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
                        
                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            // Update counters
                            const commentEl = document.getElementById(`comment-${commentId}`);
                            commentEl.querySelector('.likes-count').textContent = data.likes_count;
                            commentEl.querySelector('.dislikes-count').textContent = data.dislikes_count;
                            
                            // Update button states
                            const likeBtn = commentEl.querySelector('[data-vote-type="like"]');
                            const dislikeBtn = commentEl.querySelector('[data-vote-type="dislike"]');
                            const likeSvg = likeBtn.querySelector('svg');
                            const dislikeSvg = dislikeBtn.querySelector('svg');
                            
                            // Reset both buttons
                            likeSvg.classList.remove('fill-green-600', 'text-green-600');
                            dislikeSvg.classList.remove('fill-red-600', 'text-red-600');
                            likeBtn.removeAttribute('data-active');
                            dislikeBtn.removeAttribute('data-active');
                            
                            // Highlight active vote
                            if (data.user_vote === 'like') {
                                likeSvg.classList.add('fill-green-600', 'text-green-600');
                                likeBtn.setAttribute('data-active', 'true');
                            } else if (data.user_vote === 'dislike') {
                                dislikeSvg.classList.add('fill-red-600', 'text-red-600');
                                dislikeBtn.setAttribute('data-active', 'true');
                            }
                        }
                    } catch (error) {
                        console.error('Vote error:', error);
                        alert('Wystąpił błąd podczas głosowania. Spróbuj ponownie.');
                    }
                });
            });
            
            // Handle reply toggle
            document.querySelectorAll('.reply-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    replyForm.classList.toggle('hidden');
                    
                    // Focus on textarea if shown
                    if (!replyForm.classList.contains('hidden')) {
                        replyForm.querySelector('textarea').focus();
                    }
                });
            });
            
            // Handle cancel reply
            document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    replyForm.classList.add('hidden');
                    
                    // Clear form
                    replyForm.querySelector('form').reset();
                });
            });
        });
    </script>

</x-layout>
