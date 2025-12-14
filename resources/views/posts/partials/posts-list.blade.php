<div id="posts-grid" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($posts as $post)
        @include('posts.partials.post-card', ['post' => $post])
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
        </div>
    @endforelse
</div>

<div class="mt-12" id="pagination-container">
    {{ $posts->links() }}
</div>
