<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Dev Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }

        // Global Post Functions
        window.editPost = function(id) {
            // Dispatch event for Alpine to catch if on index page
            const event = new CustomEvent('open-edit-modal', { detail: { id: id } });
            window.dispatchEvent(event);
            
            // If the edit modal form doesn't exist, fallback to standard navigation
            if (!document.getElementById('edit-post-form')) {
                window.location.href = `/posts/${id}/edit`;
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
                            const el = document.getElementById('post-' + id);
                            if (el) el.remove();
                            
                            Swal.fire(
                                'Deleted!',
                                data.message,
                                'success'
                            );
                            
                            // If we are on the show page and deleted the main post (unlikely via this function as it's for cards), redirect
                            if (data.redirect && window.location.pathname.includes('/posts/' + id)) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    });
                }
            });
        };

        window.toggleLike = function(postId) {
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update all instances of this post's like button
                    const btns = document.querySelectorAll(`.like-btn-${postId}`);
                    btns.forEach(btn => {
                        const countSpan = btn.querySelector('.like-count');
                        const svg = btn.querySelector('svg');
                        
                        if (countSpan) countSpan.textContent = data.likes_count;
                        
                        if (svg) {
                            if (data.liked) {
                                svg.classList.add('text-red-500', 'fill-current');
                            } else {
                                svg.classList.remove('text-red-500', 'fill-current');
                            }
                        }
                    });
                }
            });
        };

        window.toggleFollow = function(userId, isUnfollow = false) {
            const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
            const method = isUnfollow ? 'DELETE' : 'POST';

            return fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    throw new Error('Unauthorized');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                    return data;
                }
                throw new Error(data.message);
            });
        };

        window.toggleFavorite = function(postId) {
            console.log('Toggling favorite for post:', postId);
            fetch(`/posts/${postId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    const btn = document.getElementById(`favorite-btn-${postId}`);
                    if (btn) {
                        const svg = btn.querySelector('svg');
                        
                        if (svg) {
                            if (data.favorited) {
                                svg.classList.add('text-yellow-500', 'fill-current');
                            } else {
                                svg.classList.remove('text-yellow-500', 'fill-current');
                            }
                        }
                    }
                    
                    // Optional: Show toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.favorited ? 'Added to favorites' : 'Removed from favorites'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong! Please try again.',
                });
            });
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased text-gray-900" x-data="{ open: false }" x-init="$watch('open', value => value ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden'))">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('posts.index') }}" class="flex items-center space-x-2 group">
                            <div class="bg-primary-600 text-white p-1.5 rounded-lg group-hover:bg-primary-700 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-gray-900 tracking-tight">DevBlog</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="{{ route('posts.index') }}" class="border-primary-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Latest Posts
                        </a>
                    </div>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                    <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5">
                        <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Write Post
                    </a>
                    @auth
                        <!-- Notification Bell -->
                        <div class="ml-3 relative" x-data="{ open: false, count: {{ auth()->user()->unreadNotifications->count() }} }" @click.outside="open = false">
                            <button @click="open = !open; if(open) { fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => count = 0); }" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 relative">
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="count > 0" x-text="count" x-cloak class="absolute top-0 right-0 block h-4 w-4 transform -translate-y-1/2 translate-x-1/2 rounded-full ring-2 ring-white bg-red-500 text-white text-[10px] font-bold text-center leading-4"></span>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 border-b border-gray-100 bg-gray-50">
                                    Notifications
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                        <a href="{{ route('posts.show', $notification->data['post_id'] ?? 0) }}" class="flex items-start px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0 {{ $notification->read_at ? 'opacity-75' : '' }}">
                                            <div class="flex-shrink-0 mr-3 relative">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border border-gray-200" x-data="{ imgError: false }">
                                                    @if(isset($notification->data['user_profile_photo']) && $notification->data['user_profile_photo'])
                                                        <img src="{{ asset('storage/' . $notification->data['user_profile_photo']) }}" 
                                                             alt="{{ $notification->data['user_name'] ?? 'User' }}" 
                                                             class="h-full w-full object-cover"
                                                             x-show="!imgError"
                                                             x-on:error="imgError = true">
                                                        <span class="font-bold text-gray-500 text-[10px]" x-show="imgError" x-cloak>
                                                            {{ substr($notification->data['user_name'] ?? 'U', 0, 1) }}
                                                        </span>
                                                    @else
                                                        <span class="font-bold text-gray-500 text-[10px]">{{ substr($notification->data['user_name'] ?? 'U', 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                @php
                                                    $type = $notification->data['type'] ?? 'info';
                                                    $bgColor = 'bg-blue-500';
                                                    $icon = '';
                                                    if($type === 'like') {
                                                        $bgColor = 'bg-red-500';
                                                        $icon = '<svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" /></svg>';
                                                    } elseif($type === 'favorite') {
                                                        $bgColor = 'bg-yellow-500';
                                                        $icon = '<svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>';
                                                    } elseif($type === 'follow') {
                                                        $bgColor = 'bg-indigo-500';
                                                        $icon = '<svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" /></svg>';
                                                    } elseif($type === 'comment') {
                                                        $bgColor = 'bg-blue-500';
                                                        $icon = '<svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>';
                                                    } else {
                                                        $bgColor = 'bg-gray-500';
                                                        $icon = '<svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>';
                                                    }
                                                @endphp
                                                <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full flex items-center justify-center border border-white {{ $bgColor }}">
                                                    {!! $icon !!}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 line-clamp-2">
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
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                                            No notifications
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-xs text-center text-primary-600 hover:text-primary-700 font-medium bg-gray-50 hover:bg-gray-100 transition-colors">
                                    View all notifications
                                </a>
                            </div>
                        </div>

                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200 overflow-hidden">
                                        @if(Auth::user()->profile_photo_path)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        @endif
                                    </div>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                                <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                                    Signed in as<br>
                                    <span class="font-bold text-gray-900 truncate block">{{ Auth::user()->name }}</span>
                                </div>
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                <a href="{{ route('users.following') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Following</a>
                                <a href="{{ route('posts.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">My Favorites</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Log in</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white hover:bg-primary-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">Register</a>
                    @endauth
                </div>
                
                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" :class="{'hidden': open, 'block': !open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" :class="{'block': open, 'hidden': !open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed bottom-4 right-4 z-50 rounded-lg bg-green-50 p-4 shadow-lg border-l-4 border-green-400 flex items-center max-w-sm">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" type="button" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Section -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                         <div class="bg-primary-600 text-white p-1.5 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 tracking-tight">DevBlog</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        A modern platform for developers to share knowledge, tutorials, and experiences. Built with passion using Laravel and Tailwind CSS.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Navigation</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('posts.index') }}" class="text-base text-gray-500 hover:text-primary-600 transition-colors">
                                Latest Posts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('posts.create') }}" class="text-base text-gray-500 hover:text-primary-600 transition-colors">
                                Write a Post
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Social & Legal -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Connect</h3>
                    <div class="flex space-x-6 mb-6">
                        <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 border-t border-gray-100 pt-8">
                <p class="text-base text-gray-400 text-center">
                    &copy; {{ date('Y') }} DevBlog. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile menu overlay -->
    <div x-show="open" class="fixed inset-0 z-[2000] bg-gray-900/80 backdrop-blur-sm sm:hidden" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         aria-hidden="true"
         x-cloak>
    </div>

    <!-- Mobile menu panel -->
    <div class="fixed inset-y-0 left-0 z-[3000] w-full max-w-xs bg-white shadow-2xl sm:hidden transform transition ease-in-out duration-300 flex flex-col"
         id="mobile-menu" 
         x-show="open" 
         x-transition:enter="transition ease-in-out duration-300 transform" 
         x-transition:enter-start="-translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in-out duration-300 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="-translate-x-full" 
         x-cloak>
         
        <div class="px-6 pt-8 pb-6 border-b border-gray-50 bg-gray-50">
            <div class="flex items-center justify-between mb-6">
                 <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-primary-600 to-primary-700 text-white p-2.5 rounded-xl shadow-lg shadow-primary-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">DevBlog</span>
                </div>
                <button @click="open = false" type="button" class="rounded-full p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- User Profile Section -->
            <div class="flex items-center space-x-3 px-1">
                @auth
                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold border border-primary-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                @else
                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold border border-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Guest User</div>
                        <div class="text-xs text-gray-500">Please sign in</div>
                    </div>
                @endauth
            </div>
        </div>

        <div class="px-3 py-6 space-y-2 flex-1 overflow-y-auto">
            <div class="px-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Menu
            </div>
            <a href="{{ route('posts.index') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('posts.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }} transition-all duration-200">
                <svg class="mr-4 h-6 w-6 {{ request()->routeIs('posts.index') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Latest Posts
            </a>
            
            @auth
                <a href="{{ route('posts.favorites') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('posts.favorites') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }} transition-all duration-200">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('posts.favorites') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    My Favorites
                </a>
                <a href="{{ route('posts.create') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('posts.create') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }} transition-all duration-200">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('posts.create') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Create New Post
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full group flex items-center px-4 py-3 text-base font-medium rounded-xl text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
                        <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('login') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }} transition-all duration-200">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('login') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Log in
                </a>
                <a href="{{ route('register') }}" class="group flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('register') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }} transition-all duration-200">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('register') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Register
                </a>
            @endauth
        </div>

        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <p class="text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} DevBlog. <br> Crafted with Laravel & Tailwind.
            </p>
        </div>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('form[action$="/logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged Out',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = data.redirect || '/';
                            });
                        } else {
                             // Fallback
                             window.location.href = '/';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Fallback to standard submission if AJAX fails
                        // form.submit(); // careful of infinite loop if preventDefault wasn't enough or if we re-trigger
                        window.location.href = '/';
                    });
                });
            });
        });
    </script>
</body>
</html>