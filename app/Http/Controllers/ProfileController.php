<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', [
            'user' => auth()->user()
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
