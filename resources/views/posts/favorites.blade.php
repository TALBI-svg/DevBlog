@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Favorites</h1>
        <p class="text-gray-600">Posts you've saved for later.</p>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($posts as $post)
                @include('posts.partials.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No favorites yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start saving posts you like!</p>
            <div class="mt-6">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    Browse Posts
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
