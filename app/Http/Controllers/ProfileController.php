<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $posts = $user->posts()
            ->with(['category', 'user', 'likes'])
            ->latest()
            ->get();
            
        $likedPosts = $user->likedPosts()
            ->with(['category', 'user', 'likes'])
            ->latest('pivot_created_at')
            ->get();

        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
            'likedPosts' => $likedPosts
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'image_url' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
