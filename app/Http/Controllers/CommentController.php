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
        ]);

        $comment = $post->comments()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);
        }

        return back()->with('success', 'Comment added successfully');
    }

    // DELETE comment
    public function destroy(Comment $comment)
    {
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
