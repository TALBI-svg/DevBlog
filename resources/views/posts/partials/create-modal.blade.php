<div x-data="createPostModal()" 
     @open-create-modal.window="showCreateModal = true"
     x-show="showCreateModal" 
     style="display: none;" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     x-cloak>
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showCreateModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             aria-hidden="true" 
             @click="showCreateModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showCreateModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            
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
                                    <label for="create_title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" name="title" id="create_title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="Enter post title">
                                </div>
                                
                                <div>
                                    <label for="create_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select name="category_id" id="create_category_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                        <option value="">Select a Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="create_content" class="block text-sm font-medium text-gray-700">Content</label>
                                    <textarea name="content" id="create_content" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="Write your content here..."></textarea>
                                </div>
                                
                                <div>
                                    <label for="create_image" class="block text-sm font-medium text-gray-700">Image (Optional)</label>
                                    <div id="create_preview_container" class="mb-2 hidden">
                                        <img id="create_preview_image" src="" alt="Preview" class="h-32 w-full object-cover rounded-md">
                                        <button type="button" @click="removeImage" class="mt-1 text-xs text-red-600 hover:text-red-800">Remove Image</button>
                                    </div>
                                    <input type="file" name="image" id="create_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" @change="previewImage">
                                </div>
                            </div>
                            
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Publish
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

<script>
    function createPostModal() {
        return {
            showCreateModal: false,
            
            previewImage(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('create_preview_image').src = e.target.result;
                        document.getElementById('create_preview_container').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            },

            removeImage() {
                document.getElementById('create_image').value = '';
                document.getElementById('create_preview_container').classList.add('hidden');
                document.getElementById('create_preview_image').src = '';
            },
            
            async createPost(e) {
                const form = e.target;
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                submitButton.disabled = true;
                submitButton.innerHTML = 'Publishing...';
                
                // Clear previous errors
                form.querySelectorAll('.border-red-300').forEach(el => el.classList.remove('border-red-300'));
                
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
                        
                        // Add to grid
                        const grid = document.getElementById('posts-grid');
                        if (grid) {
                            grid.insertAdjacentHTML('afterbegin', data.html);
                            
                            // Remove "No posts" message if it exists
                            const noPosts = grid.querySelector('.col-span-full');
                            if (noPosts) noPosts.remove();
                        }
                        
                        // Reset form
                        form.reset();
                        this.removeImage();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                         if (data.errors) {
                            let errorMsg = '';
                            Object.keys(data.errors).forEach(key => {
                                const input = document.getElementById('create_' + key);
                                if (input) input.classList.add('border-red-300');
                                errorMsg += data.errors[key][0] + '\n';
                            });
                            Swal.fire('Validation Error', errorMsg, 'error');
                         } else {
                            Swal.fire('Error', data.message || 'Creation failed', 'error');
                         }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            }
        }
    }
</script>
