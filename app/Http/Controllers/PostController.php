<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // READ all posts (Blade)
    public function index()
    {
        // Show 6 posts per page
        $posts = Post::latest()->paginate(6);
        return view('posts.index', compact('posts'));
    }

    // Show create form
    public function create()
    {
        return view('posts.create');
    }

    // STORE post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image_path'] = $path;
        }

        unset($validated['image']); // Remove image from validated array as it's not a column

        Post::create($validated);

        return redirect()->route('posts.index')
                         ->with('success', 'Post created successfully');
    }

    // SHOW single post
    public function show(Post $post)
    {
        $post->load('comments'); // Eager load comments
        return view('posts.show', compact('post'));
    }

    // Show edit form
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    // UPDATE post
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
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

        return redirect()->route('posts.index')
                         ->with('success', 'Post updated successfully');
    }

    // DELETE post
    public function destroy(Post $post)
    {
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
}
