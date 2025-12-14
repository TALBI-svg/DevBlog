@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('posts.index') }}" class="hover:text-primary-600 transition-colors">Home</a>
            <svg class="w-5 h-5 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-900 truncate max-w-xs">{{ $post->title }}</span>
        </nav>

        <!-- Post Content -->
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            @if($post->image_path)
                <div class="w-full h-64 sm:h-96 overflow-hidden">
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                </div>
            @endif
            
            <div class="p-8 sm:p-12">
                <header class="mb-8">
                    <!-- Author Info -->
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden">
                                @if($post->user && $post->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($post->user ? $post->user->name : 'U', 0, 1) }}
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-semibold text-gray-900">
                                {{ $post->user ? $post->user->name : 'Unknown User' }}
                            </p>
                            <div class="flex items-center text-sm text-gray-500">
                                <time datetime="{{ $post->created_at }}">{{ $post->created_at->format('F d, Y') }}</time>
                                <span class="mx-2">&bull;</span>
                                <span class="bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full text-xs font-medium">Article</span>
                            </div>
                        </div>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-6">
                        {{ $post->title }}
                    </h1>
                </header>

                <div class="prose prose-lg prose-indigo max-w-none text-gray-600 leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
            
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-primary-600 font-medium flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Posts
                </a>
                
                @auth
                @if(auth()->id() === $post->user_id)
                <div class="flex space-x-3">
                    <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form id="delete-post-form" action="{{ route('posts.destroy', $post) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
        </article>

        <!-- Comments Section -->
        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 sm:p-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Comments 
                        <span id="comments-count" class="ml-3 bg-gray-100 text-gray-600 text-sm font-semibold px-2.5 py-0.5 rounded-full">
                            {{ $post->comments->count() }}
                        </span>
                    </h3>
                </div>

                <div id="comments-list" class="space-y-8 mb-10">
                    @forelse ($post->comments as $comment)
                        <div class="flex space-x-4 group" id="comment-{{ $comment->id }}">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden">
                                    @if($comment->user && $comment->user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $comment->user->profile_photo_path) }}" alt="{{ $comment->user->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr($comment->user ? $comment->user->name : 'User', 0, 1)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow">
                                <div class="bg-gray-50 rounded-2xl rounded-tl-none px-6 py-4 relative group-hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-900">{{ $comment->user ? $comment->user->name : 'User' }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $comment->content }}</p>
                                    
                                    @auth
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 delete-comment-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded-full hover:bg-white" title="Delete comment">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-comments-message" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-500 italic">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Leave a comment</h4>
                    @auth
                    <form id="add-comment-form" action="{{ route('comments.store', $post) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="content" class="sr-only">Your Comment</label>
                            <textarea name="content" id="content" rows="3" class="shadow-sm block w-full focus:ring-primary-500 focus:border-primary-500 sm:text-sm border-gray-300 rounded-lg p-4 transition-shadow duration-200 focus:shadow-md" placeholder="Share your thoughts..." required></textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5">
                                Post Comment
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                        <p class="text-gray-600 mb-4">Please log in to share your thoughts.</p>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Register
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </section>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Comment
        const commentForm = document.getElementById('add-comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.action;
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                // Disable button
                submitButton.disabled = true;
                submitButton.innerHTML = 'Posting...';

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add comment to DOM
                        const commentsList = document.getElementById('comments-list');
                        const emptyState = document.getElementById('no-comments-message');
                        if (emptyState) emptyState.remove();

                        const commentHtml = `
                            <div class="flex space-x-4 group" id="comment-${data.comment.id}">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden">
                                        ${data.comment.user && data.comment.user.profile_photo_url ? 
                                            `<img src="${data.comment.user.profile_photo_url}" alt="${data.comment.user.name}" class="h-full w-full object-cover">` :
                                            (data.comment.user ? data.comment.user.name : 'User').charAt(0).toUpperCase()
                                        }
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none px-6 py-4 relative group-hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">${data.comment.user ? data.comment.user.name : 'User'}</span>
                                            <span class="text-xs text-gray-500">Just now</span>
                                        </div>
                                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">${data.comment.content}</p>
                                        
                                        <form action="/comments/${data.comment.id}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 delete-comment-form">
                                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded-full hover:bg-white" title="Delete comment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `;
                        commentsList.insertAdjacentHTML('beforeend', commentHtml);
                        
                        // Update count
                        const countSpan = document.getElementById('comments-count');
                        if(countSpan) countSpan.textContent = parseInt(countSpan.textContent) + 1;

                        commentForm.reset();
                        
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'Comment added successfully',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });
        }

        // Delete Comment (Delegation)
        document.getElementById('comments-list').addEventListener('submit', function(e) {
            if (e.target.classList.contains('delete-comment-form')) {
                e.preventDefault();
                const form = e.target;
                
                Swal.fire({
                    title: 'Delete Comment?',
                    text: "Are you sure you want to remove this comment?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3b82f6',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                            },
                            body: new FormData(form)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const commentElement = form.closest('.group');
                                commentElement.style.transition = 'all 0.3s ease';
                                commentElement.style.opacity = '0';
                                commentElement.style.transform = 'scale(0.9)';
                                
                                setTimeout(() => {
                                    commentElement.remove();
                                    // Check if list is empty
                                    const commentsList = document.getElementById('comments-list');
                                    if (commentsList.children.length === 0) {
                                        commentsList.innerHTML = `
                                            <div id="no-comments-message" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                                <p class="text-gray-500 italic">No comments yet. Be the first to share your thoughts!</p>
                                            </div>
                                        `;
                                    }
                                }, 300);

                                // Update count
                                const countSpan = document.getElementById('comments-count');
                                if(countSpan) countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
                                
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'success',
                                    title: 'Comment deleted successfully',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            });
                        });
                    }
                });
            }
        });

        // Delete Post
        const deletePostForm = document.getElementById('delete-post-form');
        if (deletePostForm) {
            deletePostForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Post?',
                    html: 'This action cannot be undone.<br>Please type <b>delete</b> to confirm.',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off',
                        placeholder: 'Type "delete"'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3b82f6',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true,
                    focusCancel: true,
                    preConfirm: (value) => {
                        if (value !== 'delete') {
                            Swal.showValidationMessage('You need to type "delete" to confirm!')
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const submitButton = this.querySelector('button[type="submit"]');
                        const originalText = submitButton.innerHTML;
                        submitButton.disabled = true;
                        submitButton.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Deleting...
                        `;

                        fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value
                            },
                            body: new FormData(this)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your post has been deleted.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = data.redirect || '/posts';
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            });
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        });
                    }
                });
            });
        }
    });
</script>
@endsection