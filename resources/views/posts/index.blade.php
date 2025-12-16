@extends('layouts.app')

@section('content')
<div x-data="postManager()">
    <!-- Hero Section -->
    <div class="text-center py-8 sm:py-16 lg:py-20 bg-gradient-to-br from-primary-50 to-white rounded-3xl mb-6 sm:mb-12 shadow-sm border border-primary-100/50">
        <h1 class="text-xl sm:text-5xl font-extrabold text-gray-900 mb-3 sm:mb-6 tracking-tight">
            Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">My Blog</span>
        </h1>
        <p class="text-xs sm:text-lg text-gray-600 mb-6 sm:mb-10 max-w-2xl mx-auto leading-relaxed px-4">
            Discover stories, thinking, and expertise from writers on any topic.
        </p>
        
        @auth
        <a href="{{ route('posts.create') }}" class="inline-flex items-center px-5 py-2.5 sm:px-8 sm:py-4 border border-transparent text-xs sm:text-lg font-medium rounded-full text-white bg-primary-600 hover:bg-primary-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Post
        </a>
        @else
        <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 sm:px-8 sm:py-4 border border-transparent text-xs sm:text-lg font-medium rounded-full text-white bg-primary-600 hover:bg-primary-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Post
        </a>
        @endauth
    </div>

    <!-- Search Section -->
    <div class="max-w-2xl mx-auto mb-8 sm:mb-12 px-4">
        <form action="{{ route('posts.index') }}" method="GET" class="relative">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    class="block w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-4 border-2 border-gray-100 rounded-full leading-5 bg-white placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:border-primary-500 focus:ring-0 shadow-sm hover:border-gray-200 transition-all duration-200 text-xs sm:text-lg" 
                    placeholder="Search by title or author...">
            </div>
        </form>
    </div>

    <!-- Posts Grid -->
    <div id="posts-wrapper">
        @include('posts.partials.posts-list')
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const postsWrapper = document.getElementById('posts-wrapper');
            let searchTimeout;

            // Prevent form submission on enter
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();
            });

            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    const query = this.value;
                    const url = new URL("{{ route('posts.index') }}");
                    if (query) {
                        url.searchParams.set('search', query);
                    }

                    // Add loading state opacity
                    postsWrapper.style.opacity = '0.5';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        postsWrapper.innerHTML = html;
                        postsWrapper.style.opacity = '1';
                        
                        // Update browser URL without reload
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        postsWrapper.style.opacity = '1';
                    });
                }, 300); // Debounce delay
            });
        });
    </script>
    @endpush


    <!-- Create Post Modal -->
    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showCreateModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" @click="showCreateModal = false" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Create New Post</h3>
                        <div class="mt-4">
                            <form id="create-post-form" @submit.prevent="createPost" enctype="multipart/form-data">
                                <div class="space-y-4">
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                        <textarea name="content" id="content" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                                        <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Create Post
                                    </button>
                                    <button type="button" @click="showCreateModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showEditModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" @click="showEditModal = false" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Post</h3>
                        <div class="mt-4">
                            <form id="edit-post-form" @submit.prevent="updatePost" enctype="multipart/form-data">
                                <input type="hidden" id="edit_post_id" name="id">
                                <input type="hidden" id="edit_post_url" name="_url">
                                <div class="space-y-4">
                                    <div>
                                        <label for="edit_title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="edit_title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="edit_content" class="block text-sm font-medium text-gray-700">Content</label>
                                        <textarea name="content" id="edit_content" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label for="edit_image" class="block text-sm font-medium text-gray-700">Image</label>
                                        <div id="edit_current_image" class="mb-2 hidden">
                                            <img src="" alt="Current Image" class="h-20 w-auto rounded">
                                        </div>
                                        <input type="file" name="image" id="edit_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Update Post
                                    </button>
                                    <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function postManager() {
        return {
            showCreateModal: false,
            showEditModal: false,
            
            openCreateModal() {
                this.showCreateModal = true;
            },
            
            async createPost(e) {
                const form = e.target;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch("{{ route('posts.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showCreateModal = false;
                        form.reset();
                        
                        // Add new post to grid
                        const postsGrid = document.getElementById('posts-grid');
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.html;
                        postsGrid.prepend(tempDiv.firstElementChild);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            },
            
            async updatePost(e) {
                const form = e.target;
                const formData = new FormData(form);
                const url = document.getElementById('edit_post_url').value;
                
                // Add _method field for PUT request
                formData.append('_method', 'PUT');
                
                try {
                    const response = await fetch(url, {
                        method: 'POST', // Use POST with _method=PUT
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showEditModal = false;
                        
                        // Update post in DOM
                        const postId = document.getElementById('edit_post_id').value;
                        const postElement = document.getElementById('post-' + postId);
                        if (postElement) {
                            postElement.outerHTML = data.html;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        }
    }

    // Global functions to be called from the post card (outside Alpine scope)
    window.editPost = async function(id) {
        try {
            const response = await fetch(`/posts/${id}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.post) {
                document.getElementById('edit_post_id').value = data.post.id;
                document.getElementById('edit_post_url').value = data.update_url;
                document.getElementById('edit_title').value = data.post.title;
                document.getElementById('edit_content').value = data.post.content;
                
                const imgContainer = document.getElementById('edit_current_image');
                if (data.image_url) {
                    imgContainer.querySelector('img').src = data.image_url;
                    imgContainer.classList.remove('hidden');
                } else {
                    imgContainer.classList.add('hidden');
                }
                
                // Access Alpine component to show modal
                const alpineComponent = document.querySelector('[x-data="postManager()"]').__x.$data;
                alpineComponent.showEditModal = true;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    window.deletePost = function(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/posts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('post-' + id).remove();
                        Swal.fire(
                            'Deleted!',
                            data.message,
                            'success'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                });
            }
        });
    };
</script>
@endsection
