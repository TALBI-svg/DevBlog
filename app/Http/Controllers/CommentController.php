<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // STORE comment
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        if ($request->wantsJson()) {
            $comment->load('user');
            
            // Render the comment HTML
            $html = view('posts.partials.comment', [
                'comment' => $comment,
                'post' => $post,
                'grouped_comments' => collect() // New comment has no replies yet
            ])->render();

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment,
                'html' => $html
            ]);
        }

        return back()->with('success', 'Comment added successfully');
    }

    // UPDATE comment
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $validated['content']
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => $comment
            ]);
        }

        return back()->with('success', 'Comment updated successfully');
    }

    // DELETE comment
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        }

        return back()->with('success', 'Comment deleted successfully');
    }
}
