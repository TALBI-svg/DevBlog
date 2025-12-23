@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Following</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($users as $user)
                <div class="p-4 sm:p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                             <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border border-gray-200">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="font-bold text-gray-500 text-lg">{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('users.show', $user) }}" class="text-sm font-bold text-gray-900 hover:text-primary-600">
                                {{ $user->name }}
                            </a>
                            <p class="text-xs text-gray-500">Member since {{ $user->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                    
                    <div>
                         <div x-data="{ following: true }">
                            <button 
                                @click="toggleFollow({{ $user->id }}, following).then(data => { if(data.success) following = !following; })"
                                :class="following ? 'text-red-700 bg-red-100 hover:bg-red-200 focus:ring-red-500' : 'text-primary-700 bg-primary-100 hover:bg-primary-200 focus:ring-primary-500'"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                <span x-text="following ? 'Unfollow' : 'Follow'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">You are not following anyone</h3>
                    <p class="mt-1 text-sm text-gray-500">Discover users to follow on the home page!</p>
                    <div class="mt-6">
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Explore Users
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
