<article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col h-full" id="post-{{ $post->id }}">
    @if($post->image_path)
        <div class="h-48 w-full overflow-hidden">
            <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
        </div>
    @endif
    <div class="p-6 flex-1 flex flex-col">
        <div class="flex items-center justify-between text-xs font-medium text-gray-500 mb-4">
            <div class="flex items-center space-x-2 group">
                <div class="h-6 w-6 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden text-[10px]">
                    @if($post->user && $post->user->profile_photo_path)
                        <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ substr($post->user ? $post->user->name : 'U', 0, 1) }}
                    @endif
                </div>
                <span class="text-gray-900 font-medium group-hover:text-primary-600 transition-colors">{{ $post->user ? $post->user->name : 'Unknown' }}</span>
            </div>
            <time datetime="{{ $post->created_at }}">{{ $post->created_at->format('M d, Y') }}</time>
        </div>
        
        <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-primary-600 transition-colors">
            <a href="{{ route('posts.show', $post) }}">
                {{ $post->title }}
            </a>
        </h2>
        
        <p class="text-gray-600 mb-6 line-clamp-3 text-sm leading-relaxed flex-1">
            {{ Str::limit($post->content, 120) }}
        </p>
        
        <div class="pt-6 border-t border-gray-100 flex items-center justify-between mt-auto">
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                {{ $post->comments->count() }} comments
            </div>
            
            <div class="flex items-center space-x-3">
                @auth
                    @if(auth()->id() === $post->user_id)
                        <button onclick="editPost({{ $post->id }})" class="text-gray-400 hover:text-primary-600 transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="deletePost({{ $post->id }})" class="text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                @endauth
                <a href="{{ route('posts.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm inline-flex items-center group">
                    Read more
                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</article>