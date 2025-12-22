<div x-data="editPostModal()" 
     @open-edit-modal.window="loadAndOpen($event.detail.id)"
     x-show="showEditModal" 
     style="display: none;" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     x-cloak>
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             aria-hidden="true" 
             @click="showEditModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            
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
                                    <label for="edit_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select name="category_id" id="edit_category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                        <!-- Categories will be populated via JS -->
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_image" class="block text-sm font-medium text-gray-700">Image</label>
                                    <div id="edit_current_image" class="mb-2 hidden">
                                        <img src="" alt="Current Image" class="h-20 w-auto rounded object-cover">
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

<script>
    function editPostModal() {
        return {
            showEditModal: false,
            
            async loadAndOpen(id) {
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
                        
                        // Populate categories
                        const categorySelect = document.getElementById('edit_category_id');
                        categorySelect.innerHTML = '';
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            if (category.id === data.post.category_id) {
                                option.selected = true;
                            }
                            categorySelect.appendChild(option);
                        });

                        const currentImageDiv = document.getElementById('edit_current_image');
                        const currentImage = currentImageDiv.querySelector('img');
                        
                        if (data.image_url) {
                            currentImage.src = data.image_url;
                            currentImageDiv.classList.remove('hidden');
                        } else {
                            currentImageDiv.classList.add('hidden');
                        }
                        
                        this.showEditModal = true;
                    }
                } catch (error) {
                    console.error('Error fetching post:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Could not load post data', 'error');
                    }
                }
            },
            
            async updatePost(e) {
                const form = e.target;
                const formData = new FormData(form);
                const url = document.getElementById('edit_post_url').value;
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                submitButton.disabled = true;
                submitButton.innerHTML = 'Updating...';
                
                // Add _method field for PUT request
                formData.append('_method', 'PUT');
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showEditModal = false;
                        
                        // Handle update based on current page
                        const postId = document.getElementById('edit_post_id').value;
                        
                        // 1. Index Page (Grid Card)
                        const postCard = document.getElementById('post-' + postId);
                        // Check if it's the card (index) or the full article (show)
                        // The card usually has a specific structure.
                        // However, on Show page, the article also has id="post-{id}".
                        // We can distinguish by checking for specific classes or elements.
                        
                        const isShowPage = window.location.pathname.includes('/posts/' + postId);
                        
                        if (isShowPage) {
                            // Update Show Page Elements
                            const titleEl = document.getElementById('post-title');
                            if (titleEl) titleEl.textContent = data.post.title;
                            
                            if (contentEl) {
                                // Escape HTML to prevent XSS
                                const escapedContent = data.post.content
                                    .replace(/&/g, "&amp;")
                                    .replace(/</g, "&lt;")
                                    .replace(/>/g, "&gt;")
                                    .replace(/"/g, "&quot;")
                                    .replace(/'/g, "&#039;");
                                contentEl.innerHTML = escapedContent.replace(/\n/g, '<br>');
                            }
                            
                            if (data.post.image_path) {
                                const imgContainer = document.getElementById('post-image-container');
                                if (imgContainer) {
                                    const img = imgContainer.querySelector('img');
                                    if (img) img.src = '/storage/' + data.post.image_path;
                                }
                            }
                            
                            if (data.post.category) {
                                const catEl = document.getElementById('post-category');
                                if (catEl) catEl.textContent = data.post.category.name;
                            }

                        } else if (postCard) {
                            // Update Index Page Card
                            if (data.html) {
                                postCard.outerHTML = data.html;
                            }
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                         // Handle validation errors
                         if (data.errors) {
                            let errorMsg = '';
                            Object.values(data.errors).forEach(err => errorMsg += err + '\n');
                            Swal.fire('Validation Error', errorMsg, 'error');
                         } else {
                            Swal.fire('Error', data.message || 'Update failed', 'error');
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
