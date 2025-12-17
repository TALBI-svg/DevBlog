@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center">
        <a href="{{ route('posts.show', $post) }}" class="mr-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Liked by</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($likes as $user)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <a href="{{ route('users.show', $user) }}" class="flex items-center space-x-3 group">
                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden group-hover:border-primary-400 transition-colors">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                {{ substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors">{{ $user->name }}</span>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    No likes yet.
                </div>
            @endforelse
        </div>
        
        @if($likes->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $likes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection