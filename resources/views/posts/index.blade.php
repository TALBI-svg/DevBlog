@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="relative overflow-hidden mb-12 rounded-2xl bg-gradient-to-r from-primary-600 to-indigo-700 text-white shadow-xl">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="relative px-6 py-16 sm:px-12 sm:py-20 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-4">
                Welcome to DevBlog
            </h1>
            <p class="text-lg sm:text-xl text-primary-100 max-w-2xl mx-auto mb-8">
                Explore the latest insights, tutorials, and stories from our community of developers.
            </p>
            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-primary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-600 focus:ring-white transition-all duration-200 shadow-lg transform hover:-translate-y-0.5">
                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                Start Writing
            </a>
        </div>
    </div>

    <!-- Latest Posts Header -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <span class="bg-primary-100 text-primary-600 p-2 rounded-lg mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </span>
            Latest Articles
        </h2>
    </div>

    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($posts as $post)
                <article class="flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group h-full">
                    <div class="flex-1 p-6 flex flex-col">
                        <div class="flex items-center text-xs font-medium text-gray-500 mb-4 space-x-2">
                            <span class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">Article</span>
                            <span class="text-gray-300">&bull;</span>
                            <time datetime="{{ $post->created_at }}">{{ $post->created_at->format('M d, Y') }}</time>
                        </div>
                        
                        <a href="{{ route('posts.show', $post) }}" class="block group-hover:text-primary-600 transition-colors duration-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 leading-tight">
                                {{ $post->title }}
                            </h3>
                            <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">
                                {{ Str::limit($post->content, 150) }}
                            </p>
                        </a>
                        
                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                            <a href="{{ route('posts.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-semibold text-sm flex items-center group/link">
                                Read More
                                <svg class="ml-1 w-4 h-4 transform group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                            
                            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <a href="{{ route('posts.edit', $post) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No posts yet</h3>
            <p class="text-gray-500 mb-6 max-w-sm mx-auto">Get started by creating your first article to share with the world.</p>
            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                Create Post
            </a>
        </div>
    @endif
@endsection
