@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('posts.index') }}" class="hover:text-primary-600 transition-colors">Home</a>
            <svg class="w-5 h-5 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <a href="{{ route('posts.show', $post) }}" class="hover:text-primary-600 transition-colors truncate max-w-xs">{{ $post->title }}</a>
            <svg class="w-5 h-5 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-900">Edit</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 sm:p-10">
                <header class="mb-8 border-b border-gray-100 pb-8">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Post</h1>
                    <p class="mt-2 text-gray-500">Update your article content or fix any typos.</p>
                </header>

                <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" name="title" id="title" 
                                class="block w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 sm:text-sm p-3 transition-shadow duration-200 focus:shadow-md @error('title') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Enter a catchy title..." 
                                value="{{ old('title', $post->title) }}" 
                                required>
                            @error('title')
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Cover Image (Optional)</label>
                        @if($post->image_path)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $post->image_path) }}" alt="Current cover image" class="h-48 w-full object-cover rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors">
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                        <div class="relative rounded-md shadow-sm">
                            <textarea name="content" id="content" rows="10" 
                                class="block w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 sm:text-sm p-4 transition-shadow duration-200 focus:shadow-md @error('content') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Write your article content here..." 
                                required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <div class="absolute top-4 right-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                        <a href="{{ route('posts.show', $post) }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm px-4 py-2 transition-colors">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
