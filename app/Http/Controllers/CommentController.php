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

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            $comment->load('user');
            
            // Add profile photo URL to response
            $comment->user->profile_photo_url = $comment->user->profile_photo_path 
                ? asset('storage/' . $comment->user->profile_photo_path) 
                : null;

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
