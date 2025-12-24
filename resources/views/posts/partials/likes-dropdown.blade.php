<div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
<div class="relative z-10">
    <div class="font-semibold mb-1 border-b border-gray-700 pb-1 text-[10px] uppercase tracking-wider text-gray-400">Liked by</div>
    @foreach($likes->take(7) as $user)
        <div class="flex items-center gap-2 py-1">
            <div class="h-5 w-5 rounded-full bg-gray-700 flex items-center justify-center text-white text-[9px] font-bold border border-gray-600 overflow-hidden shrink-0">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                @else
                    {{ substr($user->name, 0, 1) }}
                @endif
            </div>
            <div class="truncate text-[11px]">{{ $user->name }}</div>
        </div>
    @endforeach
    @if($likes->count() > 7)
        <a href="{{ route('posts.likes', $post) }}" class="block text-center text-gray-400 hover:text-white mt-1 pt-1 border-t border-gray-700 font-bold text-lg leading-none pb-1">...</a>
    @endif
</div>