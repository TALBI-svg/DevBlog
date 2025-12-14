@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
        <p class="mt-2 text-gray-600">Manage your account settings and preferences.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" @submit.prevent="updateProfile">
                @csrf
                @method('PUT')
                
                <div class="space-y-12">
                    <!-- Profile Information -->
                    <div class="border-b border-gray-900/10 pb-12">
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Personal Information</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">Update your profile photo and personal details.</p>

                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <!-- Photo Upload -->
                            <div class="col-span-full">
                                <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">Photo</label>
                                <div class="mt-2 flex items-center gap-x-3">
                                    <div id="photo-preview-container" class="h-20 w-20 flex-shrink-0 rounded-full bg-gray-100 border-2 border-white shadow-sm overflow-hidden relative">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-primary-100 text-primary-600 font-bold text-2xl">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="relative">
                                            <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                                            <label for="profile_photo" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer transition-colors">
                                                Change photo
                                            </label>
                                        </div>
                                        <p class="mt-2 text-xs leading-5 text-gray-500">JPG, GIF or PNG. Max 2MB.</p>
                                    </div>
                                </div>
                                @error('profile_photo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="sm:col-span-4">
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
                                <div class="mt-2">
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" disabled>
                                    <p class="mt-1 text-xs text-gray-500">Name changes are currently disabled.</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="sm:col-span-4">
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" disabled>
                                </div>
                            </div>

                            <!-- Join Date -->
                            <div class="sm:col-span-4">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Member since</label>
                                <div class="mt-2 text-sm text-gray-600">
                                    {{ $user->created_at->format('F d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" class="text-sm font-semibold leading-6 text-gray-900" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">Save changes</button>
                </div>
            </form>
        </div>
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
                    const navAvatar = document.querySelector('nav .h-8.w-8 img, nav .h-10.w-10 img');
                    if (navAvatar) {
                        navAvatar.src = data.image_url;
                    }
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
                const imgContainer = document.getElementById('photo-preview-container');
                // Remove existing content
                imgContainer.innerHTML = '';
                // Add new image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'h-full w-full object-cover';
                imgContainer.appendChild(img);
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
