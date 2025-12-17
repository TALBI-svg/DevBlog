@extends('layouts.app')

@section('content')
<div x-data="{ activeTab: 'posts' }" class="max-w-5xl mx-auto">
    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <!-- Cover Image -->
        <div class="h-48 sm:h-64 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        <!-- Profile Info -->
        <div class="px-6 sm:px-10 pb-8 relative">
            <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-16 sm:-mt-20 mb-6 sm:mb-8 text-center sm:text-left">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="h-32 w-32 sm:h-40 sm:w-40 rounded-full border-4 border-white shadow-md overflow-hidden bg-white">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-primary-100 text-primary-600 font-bold text-4xl">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <!-- Quick Edit Photo Button (Optional) -->
                    @if(auth()->id() === $user->id)
                    <button @click="activeTab = 'settings'" class="absolute bottom-2 right-2 p-2 bg-white rounded-full shadow-sm border border-gray-200 text-gray-500 hover:text-primary-600 transition-colors" title="Change Photo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-700 font-medium">{{ $user->email }}</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex gap-3 justify-center sm:justify-start">
                            @if(auth()->id() === $user->id)
                            <button @click="activeTab = 'settings'" 
                                    :class="activeTab === 'settings' ? 'bg-gray-100 text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Edit Profile
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-center sm:justify-start gap-6 mt-6">
                        <div class="text-center sm:text-left">
                            <span class="block text-xl font-bold text-gray-900">{{ $posts->count() }}</span>
                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Posts</span>
                        </div>
                        @auth
                        <div class="text-center sm:text-left">
                            <span class="block text-xl font-bold text-gray-900">{{ $likedPosts->count() }}</span>
                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Liked</span>
                        </div>
                        <div class="text-center sm:text-left">
                            <span class="block text-xl font-bold text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Joined</span>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-100">
                <button @click="activeTab = 'posts'" 
                        :class="activeTab === 'posts' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 sm:flex-none pb-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200">
                    {{ auth()->id() === $user->id ? 'My Posts' : 'Posts' }}
                </button>
                @auth
                <button @click="activeTab = 'likes'" 
                        :class="activeTab === 'likes' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 sm:flex-none pb-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200">
                    Liked Posts
                </button>
                @endauth
                @if(auth()->id() === $user->id)
                <button @click="activeTab = 'settings'" 
                        :class="activeTab === 'settings' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 sm:flex-none pb-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200">
                    Settings
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div>
        <!-- My Posts Tab -->
        <div x-show="activeTab === 'posts'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($posts->count() > 0)
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        @include('posts.partials.post-card', ['post' => $post, 'compact' => true])
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                    <div class="mx-auto h-12 w-12 text-gray-300">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Share your thoughts with the world.</p>
                    <div class="mt-6">
                        <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            Create Post
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Liked Posts Tab -->
        <div x-show="activeTab === 'likes'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($likedPosts->count() > 0)
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($likedPosts as $post)
                        @include('posts.partials.post-card', ['post' => $post, 'compact' => true])
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                    <div class="mx-auto h-12 w-12 text-gray-300">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No liked posts</h3>
                    <p class="mt-1 text-sm text-gray-500">Posts you like will appear here.</p>
                </div>
            @endif
        </div>

        <!-- Settings Tab -->
        @if(auth()->id() === $user->id)
        <div x-show="activeTab === 'settings'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Profile Settings</h2>
                    <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" @submit.prevent="updateProfile">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-8">
                            <!-- Photo Upload -->
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900 mb-2">Profile Photo</label>
                                <div class="flex items-center gap-x-6">
                                    <div id="photo-preview-container-settings" class="h-24 w-24 flex-shrink-0 rounded-full bg-gray-100 border border-gray-200 shadow-sm overflow-hidden relative">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-primary-100 text-primary-600 font-bold text-2xl">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="relative">
                                            <input type="file" name="profile_photo" id="profile_photo_settings" class="hidden" accept="image/*" onchange="previewImage(this)">
                                            <label for="profile_photo_settings" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer transition-colors">
                                                Upload New Photo
                                            </label>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">JPG, GIF or PNG. Max 2MB.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
                                    <div class="mt-2">
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="block w-full rounded-lg border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-primary-600 focus:border-primary-600 sm:text-sm bg-gray-50" disabled>
                                        <p class="mt-1 text-xs text-gray-500">Name changes are disabled.</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                    <div class="mt-2">
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="block w-full rounded-lg border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-primary-600 focus:border-primary-600 sm:text-sm bg-white" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end border-t border-gray-100 pt-6">
                            <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function updateProfile(e) {
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerText;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';
        
        fetch("{{ route('profile.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update navbar avatar if exists
                if (data.image_url) {
                    const navAvatars = document.querySelectorAll('nav img.object-cover');
                    navAvatars.forEach(img => img.src = data.image_url);
                    
                    // Update current page avatars
                    const pageAvatars = document.querySelectorAll('.rounded-full img');
                    pageAvatars.forEach(img => img.src = data.image_url);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Something went wrong.',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
            });
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerText = originalBtnText;
        });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Basic validation
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file');
                input.value = '';
                return;
            }

            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Update settings preview
                const imgContainerSettings = document.getElementById('photo-preview-container-settings');
                if (imgContainerSettings) {
                    imgContainerSettings.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-full w-full object-cover';
                    imgContainerSettings.appendChild(img);
                }
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection