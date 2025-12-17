<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // READ all posts (Blade)
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'likes'])->latest();

        // Search by title or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by start date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by end date
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Show 6 posts per page and preserve query parameters
        $posts = $query->paginate(6)->withQueryString();
        
        $categories = Category::all(); // Pass categories for filter if needed in future

        if ($request->ajax()) {
            return view('posts.partials.posts-list', compact('posts'));
        }
        
        return view('posts.index', compact('posts', 'categories'));
    }

    // Show create form
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    // STORE post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image_path'] = $path;
        }

        unset($validated['image']); // Remove image from validated array as it's not a column
        
        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'html' => view('posts.partials.post-card', compact('post'))->render(),
            ]);
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Post created successfully');
    }

    // SHOW single post
    public function show(Post $post)
    {
        $post->load(['comments' => function ($query) {
            $query->latest();
        }, 'comments.user', 'user', 'category', 'likes']); // Eager load comments (sorted by latest), post owner, category, and likes
        
        // Fetch related posts (same category, excluding current post)
        $relatedPosts = collect();
        if ($post->category_id) {
            $relatedPosts = Post::with('user', 'category', 'likes')
                ->where('category_id', $post->category_id)
                ->where('id', '!=', $post->id)
                ->latest()
                ->take(3)
                ->get();
        }

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    // Show edit form
    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();

        if (request()->wantsJson()) {
            return response()->json([
                'post' => $post,
                'categories' => $categories,
                'image_url' => $post->image_path ? asset('storage/' . $post->image_path) : null,
                'update_url' => route('posts.update', $post)
            ]);
        }

        return view('posts.edit', compact('post', 'categories'));
    }

    // UPDATE post
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $path = $request->file('image')->store('posts', 'public');
            $validated['image_path'] = $path;
        }

        unset($validated['image']);

        $post->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'html' => view('posts.partials.post-card', compact('post'))->render(),
            ]);
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Post updated successfully');
    }

    // DELETE post
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
                'redirect' => route('posts.index')
            ]);
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Post deleted successfully');
    }

    // Show Post Likes
    public function likes(Post $post)
    {
        $likes = $post->likes()->paginate(20); // Paginate for better performance if many likes
        return view('posts.likes', compact('post', 'likes'));
    }

    // Toggle Like
    public function toggleLike(Post $post)
    {
        $user = auth()->user();
        
        if ($post->isLikedBy($user)) {
            $post->likes()->detach($user);
            $liked = false;
        } else {
            $post->likes()->attach($user);
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $post->likes()->count()
            ]);
        }

        return back();
    }
}
