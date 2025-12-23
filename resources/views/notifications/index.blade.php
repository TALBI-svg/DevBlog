@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="p-4 sm:p-6 flex items-start space-x-4 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50/50' }}">
                    <div class="flex-shrink-0 relative">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border border-gray-200" x-data="{ imgError: false }">
                            @if(isset($notification->data['user_profile_photo']) && $notification->data['user_profile_photo'])
                                <img src="{{ asset('storage/' . $notification->data['user_profile_photo']) }}" 
                                     alt="{{ $notification->data['user_name'] ?? 'User' }}" 
                                     class="h-full w-full object-cover"
                                     x-show="!imgError"
                                     x-on:error="imgError = true">
                                <span class="font-bold text-gray-500 text-xs" x-show="imgError" x-cloak>
                                    {{ substr($notification->data['user_name'] ?? 'U', 0, 1) }}
                                </span>
                            @else
                                <span class="font-bold text-gray-500 text-xs">{{ substr($notification->data['user_name'] ?? 'U', 0, 1) }}</span>
                            @endif
                        </div>
                        
                        @php
                            $type = $notification->data['type'] ?? 'info';
                            $bgColor = 'bg-blue-500';
                            $icon = '';
                            
                            if($type === 'like') {
                                $bgColor = 'bg-red-500';
                                $icon = '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" /></svg>';
                            } elseif($type === 'favorite') {
                                $bgColor = 'bg-yellow-500';
                                $icon = '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>';
                            } elseif($type === 'follow') {
                                $bgColor = 'bg-indigo-500';
                                $icon = '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" /></svg>';
                            } elseif($type === 'comment') {
                                $bgColor = 'bg-blue-500';
                                $icon = '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>';
                            } else {
                                $bgColor = 'bg-gray-500';
                                $icon = '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>';
                            }
                        @endphp

                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full flex items-center justify-center border-2 border-white {{ $bgColor }}">
                            {!! $icon !!}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">
                            <span class="font-bold text-primary-600">{{ $notification->data['user_name'] ?? 'User' }}</span>
                            @if(isset($notification->data['type']))
                                @if($notification->data['type'] === 'like')
                                    liked your post.
                                @elseif($notification->data['type'] === 'comment')
                                    commented on your post.
                                @elseif($notification->data['type'] === 'favorite')
                                    saved your post as favorite.
                                @elseif($notification->data['type'] === 'follow')
                                    started following you.
                                @else
                                    {{ str_replace($notification->data['user_name'] ?? '', '', $notification->data['message'] ?? '') }}
                                @endif
                            @else
                                {{ $notification->data['message'] ?? 'New notification' }}
                            @endif
                        </p>
                        @if(isset($notification->data['post_title']))
                            <a href="{{ route('posts.show', $notification->data['post_id'] ?? 0) }}" class="text-sm text-gray-500 hover:text-primary-600 block mt-1">
                                "{{ Str::limit($notification->data['post_title'], 50) }}"
                            </a>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(!$notification->read_at)
                        <div class="flex-shrink-0 self-center">
                            <div class="h-2 w-2 rounded-full bg-primary-600"></div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
