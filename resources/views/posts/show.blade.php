@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs font-medium text-gray-500 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('posts.index') }}" class="hover:text-primary-600 transition-colors">Home</a>
            <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-900 truncate max-w-xs">{{ $post->title }}</span>
        </nav>

        <!-- Post Content -->
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 sm:mb-12">
            @if($post->image_path)
                <div class="w-full h-48 sm:h-96 overflow-hidden">
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                </div>
            @endif
            
            <div class="p-4 sm:p-12">
                <header class="mb-4 sm:mb-8">
                    <!-- Author Info -->
                    <div class="flex items-center mb-4 sm:mb-6">
                        @if($post->user)
                        <a href="{{ route('users.show', $post->user) }}" class="flex-shrink-0 group">
                            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden group-hover:border-primary-400 transition-colors">
                                @if($post->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($post->user->name, 0, 1) }}
                                @endif
                            </div>
                        </a>
                        @else
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden">
                                U
                            </div>
                        </div>
                        @endif

                        <div class="ml-3 sm:ml-4">
                            <p class="text-xs sm:text-base font-semibold text-gray-900">
                                @if($post->user)
                                    <a href="{{ route('users.show', $post->user) }}" class="hover:text-primary-600 transition-colors">
                                        {{ $post->user->name }}
                                    </a>
                                @else
                                    Unknown User
                                @endif
                            </p>
                            <div class="flex items-center text-xs sm:text-sm text-gray-500">
                                <time datetime="{{ $post->created_at }}">{{ $post->created_at->format('F d, Y') }}</time>
                                <span class="mx-2">&bull;</span>
                                @if($post->category)
                                    <span class="bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $post->category->name }}</span>
                                @else
                                    <span class="bg-gray-50 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium">Uncategorized</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h1 class="text-lg sm:text-4xl font-extrabold text-gray-900 leading-tight mb-3 sm:mb-6">
                        {{ $post->title }}
                    </h1>
                </header>

                <div class="prose prose-sm sm:prose-lg prose-indigo max-w-none text-gray-600 leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-8 sm:py-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
                <div class="flex items-center w-full sm:w-auto justify-between sm:justify-start">
                    <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-primary-600 font-medium flex items-center transition-colors text-sm sm:text-base mr-6">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </a>
                    
                    <div class="relative group/like">
                        <button onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}" class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors">
                            <svg class="w-6 h-6 {{ auth()->check() && $post->isLikedBy(auth()->user()) ? 'text-red-500 fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span id="like-count-{{ $post->id }}" class="font-medium">{{ $post->likes->count() }}</span>
                        </button>

                        @if($post->likes->count() > 0)
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover/like:block w-48 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 z-50 shadow-xl">
                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                                <div class="relative z-10 max-h-48 overflow-y-auto custom-scrollbar">
                                    <div class="font-semibold mb-1 border-b border-gray-700 pb-1 text-[10px] uppercase tracking-wider text-gray-400">Liked by</div>
                                    @foreach($post->likes->take(10) as $user)
                                        <div class="truncate py-0.5">{{ $user->name }}</div>
                                    @endforeach
                                    @if($post->likes->count() > 10)
                                        <a href="{{ route('posts.likes', $post) }}" class="block text-center text-gray-400 hover:text-white mt-1 pt-1 border-t border-gray-700 font-bold text-lg leading-none pb-1">...</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                @auth
                @if(auth()->id() === $post->user_id)
                <div class="flex space-x-2 sm:space-x-3 w-full sm:w-auto justify-end mt-3 sm:mt-0">
                    <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 shadow-sm text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-3 w-3 sm:h-4 sm:w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form id="delete-post-form" action="{{ route('posts.destroy', $post) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-transparent shadow-sm text-xs sm:text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="-ml-1 mr-2 h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Related Posts -->
        @if(isset($relatedPosts) && $relatedPosts->count() > 0)
        <div class="mb-8 sm:mb-12">
            <h3 class="text-xl font-bold text-gray-900 mb-6 px-2">Related Posts</h3>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($relatedPosts as $relatedPost)
                    @include('posts.partials.post-card', ['post' => $relatedPost, 'compact' => true])
                @endforeach
            </div>
        </div>
        @endif

        <!-- Comments Section -->
        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-10">
                <div class="flex items-center justify-between mb-6 sm:mb-8">
                    <h3 class="text-lg sm:text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Comments 
                        <span id="comments-count" class="ml-2 sm:ml-3 bg-gray-100 text-gray-600 text-xs sm:text-sm font-semibold px-2 sm:px-2.5 py-0.5 rounded-full">
                            {{ $post->comments->count() }}
                        </span>
                    </h3>
                </div>

                <!-- Add Comment Form -->
                <div class="bg-gray-50 rounded-xl p-3 sm:p-6 border border-gray-100 mb-6 sm:mb-10">
                    <h4 class="text-sm sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Leave a comment</h4>
                    @auth
                    <form id="add-comment-form" action="{{ route('comments.store', $post) }}" method="POST">
                        @csrf
                        <div class="mb-3 sm:mb-4">
                            <label for="content" class="sr-only">Your Comment</label>
                            <textarea name="content" id="content" rows="3" class="shadow-sm block w-full focus:ring-primary-500 focus:border-primary-500 focus:outline-none text-xs sm:text-sm border-gray-300 rounded-lg p-3 sm:p-4 transition-shadow duration-200 focus:shadow-md" placeholder="Share your thoughts..." required></textarea>
                            @error('content')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-2.5 border border-transparent text-xs sm:text-sm font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5">
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

                <div id="comments-list" class="space-y-8">
                    @forelse ($post->comments as $comment)
                        <div class="flex space-x-3 sm:space-x-4 group" id="comment-{{ $comment->id }}">
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
                                <div class="bg-gray-50 rounded-2xl rounded-tl-none px-4 py-3 sm:px-6 sm:py-4 relative group-hover:bg-gray-100 transition-colors duration-200">
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


            </div>
        </section>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-resize textarea
        const textarea = document.getElementById('content');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // AJAX Comment Submission
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                        
                        // Update count
                        const countSpan = document.getElementById('comments-count');
                        if(countSpan) countSpan.textContent = parseInt(countSpan.textContent) + 1;

                        const commentHtml = `
                            <div class="flex space-x-3 sm:space-x-4 group" id="comment-${data.comment.id}">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden">
                                        ${data.comment.user && data.comment.user.profile_photo_url ? 
                                            `<img src="${data.comment.user.profile_photo_url}" alt="${data.comment.user.name}" class="h-full w-full object-cover">` :
                                            (data.comment.user ? data.comment.user.name : 'User').charAt(0).toUpperCase()
                                        }
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none px-4 py-3 sm:px-6 sm:py-4 relative group-hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">${data.comment.user ? data.comment.user.name : 'User'}</span>
                                            <span class="text-xs text-gray-500">Just now</span>
                                        </div>
                                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">${data.comment.content}</p>
                                        
                                        <form action="/comments/${data.comment.id}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 delete-comment-form">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
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
                        
                        commentsList.insertAdjacentHTML('afterbegin', commentHtml);
                        
                        // Reset form
                        this.reset();
                        if (textarea) textarea.style.height = 'auto';

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'bottom-end',
                                icon: 'success',
                                title: 'Comment added successfully',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
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
                
                const confirmDelete = () => {
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
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'success',
                                    title: 'Comment deleted successfully',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            });
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
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
                            confirmDelete();
                        }
                    });
                } else {
                    if(confirm('Are you sure you want to delete this comment?')) {
                        confirmDelete();
                    }
                }
            }
        });

        // Delete Post
        const deletePostForm = document.getElementById('delete-post-form');
        if (deletePostForm) {
            deletePostForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (typeof Swal !== 'undefined') {
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
                            submitDeletePost(this);
                        }
                    });
                } else {
                    if(confirm('Type "delete" to confirm.')) {
                         submitDeletePost(this);
                    }
                }
            });
        }
        
        function submitDeletePost(form) {
             const submitButton = form.querySelector('button[type="submit"]');
             const originalText = submitButton.innerHTML;
             submitButton.disabled = true;
             submitButton.innerHTML = 'Deleting...'; 

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
                     if (typeof Swal !== 'undefined') {
                        Swal.fire({
                             icon: 'success',
                             title: 'Deleted!',
                             text: 'Your post has been deleted.',
                             showConfirmButton: false,
                             timer: 1500
                         }).then(() => {
                             window.location.href = data.redirect || '/posts';
                         });
                     } else {
                         window.location.href = data.redirect || '/posts';
                     }
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 if (typeof Swal !== 'undefined') {
                    Swal.fire({
                         icon: 'error',
                         title: 'Oops...',
                         text: 'Something went wrong!',
                     });
                 }
                 submitButton.disabled = false;
                 submitButton.innerHTML = originalText;
             });
        }
    });
</script>
@endsection