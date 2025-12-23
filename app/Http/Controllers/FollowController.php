<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserFollowed;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(User $user)
    {
        $follower = auth()->user();

        if ($follower->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        if (!$follower->isFollowing($user)) {
            $follower->following()->attach($user->id);
            $user->notify(new UserFollowed($follower));
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'You are now following ' . $user->name,
                'following' => true
            ]);
        }

        return back()->with('success', 'You are now following ' . $user->name);
    }

    public function destroy(User $user)
    {
        $follower = auth()->user();

        if ($follower->isFollowing($user)) {
            $follower->following()->detach($user->id);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'You have unfollowed ' . $user->name,
                'following' => false
            ]);
        }

        return back()->with('success', 'You have unfollowed ' . $user->name);
    }

    public function following()
    {
        $users = auth()->user()->following()->paginate(20);
        return view('users.following', compact('users'));
    }
}
