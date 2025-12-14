@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="text-center py-16 lg:py-20 bg-gradient-to-br from-primary-50 to-white rounded-3xl mb-12 shadow-sm border border-primary-100/50">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-6 tracking-tight">
            Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">My Blog</span>
        </h1>
        <p class="text-lg text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
            Discover stories, thinking, and expertise from writers on any topic.
        </p>
        <a href="{{ route('posts.create') }}" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-full text-white bg-primary-600 hover:bg-primary-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Post
        </a>
    </div>

    <!-- Posts Grid -->
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                @if($post->image_path)
                    <div class="h-48 w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                    </div>
                @endif
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex items-center justify-between text-xs font-medium text-gray-500 mb-4">
                        <span class="bg-primary-50 text-primary-700 px-2.5 py-1 rounded-full">Article</span>
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
                        
                        <a href="{{ route('posts.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm inline-flex items-center group">
                            Read more
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $posts->links() }}
    </div>
@endsection
