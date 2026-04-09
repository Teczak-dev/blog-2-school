<div class="flex gap-4 mb-6" id="comment-{{ $comment->id }}">
    <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-700 rounded-full flex items-center justify-center text-white font-semibold">
            {{ strtoupper(substr($comment->author_display_name, 0, 2)) }}
        </div>
    </div>
    <div class="flex-1">
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $comment->author_display_name }}</h4>
                    @if($comment->isFromLoggedUser())
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 text-xs rounded-full">Użytkownik</span>
                    @endif
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line mb-3">{{ $comment->content }}</div>
            
            <!-- Vote and Reply Actions -->
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <!-- Like Button -->
                    <button class="vote-btn flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                            data-comment-id="{{ $comment->id }}"
                            data-vote-type="like"
                            @if($comment->getUserVote(auth()->id())?->vote_type === 'like') data-active="true" @endif>
                        <svg class="w-5 h-5 {{ $comment->getUserVote(auth()->id())?->vote_type === 'like' ? 'fill-green-600 text-green-600 dark:fill-green-400 dark:text-green-400' : '' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        <span class="likes-count">{{ $comment->likes_count }}</span>
                    </button>
                    
                    <!-- Dislike Button -->
                    <button class="vote-btn flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                            data-comment-id="{{ $comment->id }}"
                            data-vote-type="dislike"
                            @if($comment->getUserVote(auth()->id())?->vote_type === 'dislike') data-active="true" @endif>
                        <svg class="w-5 h-5 {{ $comment->getUserVote(auth()->id())?->vote_type === 'dislike' ? 'fill-red-600 text-red-600 dark:fill-red-400 dark:text-red-400' : '' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                        </svg>
                        <span class="dislikes-count">{{ $comment->dislikes_count }}</span>
                    </button>
                @else
                    <!-- Show vote counts for guests -->
                    <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        <span>{{ $comment->likes_count }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                        </svg>
                        <span>{{ $comment->dislikes_count }}</span>
                    </div>
                @endauth
                
                <!-- Reply Button -->
                @if($comment->canReply())
                    <button class="reply-toggle-btn flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors font-medium"
                            data-comment-id="{{ $comment->id }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Odpowiedz
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Reply Form (hidden by default) -->
        @if($comment->canReply())
            <div class="reply-form-container mt-4 hidden" id="reply-form-{{ $comment->id }}">
                <form method="POST" action="{{ route('comments.reply', $comment->id) }}" class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4" data-comment-form="true">
                    @csrf
                    
                    @guest
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <input type="text" name="author_name" required
                                placeholder="Twoje imię"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            <input type="email" name="author_email" required
                                placeholder="Email"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>
                    @endguest
                    
                    <textarea name="content" required rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none text-sm mb-3"
                        placeholder="Napisz odpowiedź..."></textarea>
                    
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition-colors">
                            Wyślij odpowiedź
                        </button>
                        <button type="button" class="cancel-reply-btn px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
                            data-comment-id="{{ $comment->id }}">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        @endif
        
        <!-- Replies (nested) -->
        @if($comment->approvedReplies->count() > 0)
            <div class="mt-4 ml-8 space-y-4">
                @foreach($comment->approvedReplies as $reply)
                    <x-comment :comment="$reply" />
                @endforeach
            </div>
        @endif
    </div>
</div>
