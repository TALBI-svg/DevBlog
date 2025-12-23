@props(['comment', 'post', 'grouped_comments'])

<div class="flex gap-3 group" id="comment-{{ $comment->id }}">
    <div class="flex-shrink-0 flex flex-col items-center">
        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden text-xs">
            @if($comment->user && $comment->user->profile_photo_path)
                <img src="{{ asset('storage/' . $comment->user->profile_photo_path) }}" alt="{{ $comment->user->name }}" class="h-full w-full object-cover">
            @else
                {{ strtoupper(substr($comment->user ? $comment->user->name : 'User', 0, 1)) }}
            @endif
        </div>
        <div class="w-px h-full bg-gray-200 my-2 group-last:hidden"></div>
    </div>
    <div class="flex-grow pb-4">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-900">{{ $comment->user ? $comment->user->name : 'User' }}</span>
                <span class="text-xs text-gray-400">&bull;</span>
                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-100 py-1"
                     style="display: none;">
                    
                    <button @click="shareComment('{{ route('posts.show', $post) }}#comment-{{ $comment->id }}'); open = false" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share
                    </button>

                    @auth
                    @if(auth()->id() === $comment->user_id)
                        <button @click="toggleEditComment({{ $comment->id }}); open = false" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="w-full delete-comment-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 text-left">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
        
        <div id="comment-display-{{ $comment->id }}" class="mb-2">
            <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-line">{{ $comment->content }}</p>
        </div>

        @auth
        @if(auth()->id() === $comment->user_id)
            <form id="edit-comment-form-{{ $comment->id }}" action="{{ route('comments.update', $comment) }}" method="POST" class="hidden mt-2 mb-2 edit-comment-form">
                @csrf
                @method('PUT')
                <textarea name="content" rows="3" class="shadow-sm block w-full focus:ring-primary-500 focus:border-primary-500 text-sm border-gray-300 rounded-md mb-2" required>{{ $comment->content }}</textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="toggleEditComment({{ $comment->id }})" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Save</button>
                </div>
            </form>
        @endif
        @endauth

        @if(is_null($comment->parent_id))
        <div class="flex items-center gap-4">
            <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="flex items-center gap-1 text-gray-400 hover:bg-gray-100 px-2 py-1 rounded transition-colors text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Reply
            </button>
        </div>
        @endif

        <!-- Reply Form -->
        <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store', $post) }}" method="POST" class="hidden mt-4 ml-2 reply-comment-form">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <div class="mb-2">
                <textarea name="content" rows="2" class="shadow-sm block w-full focus:ring-primary-500 focus:border-primary-500 text-sm border-gray-300 rounded-md" placeholder="Write a reply..." required></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Cancel</button>
                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Reply</button>
            </div>
        </form>

        @php
            $replies = $grouped_comments->get($comment->id, collect())->sortByDesc('created_at');
        @endphp

        <div id="replies-container-{{ $comment->id }}" class="mt-4 pl-4 border-l-2 border-gray-100 {{ $replies->count() > 0 ? '' : 'hidden' }}">
            @foreach($replies as $reply)
                @include('posts.partials.comment', ['comment' => $reply, 'post' => $post, 'grouped_comments' => $grouped_comments])
            @endforeach
        </div>
    </div>
</div>
