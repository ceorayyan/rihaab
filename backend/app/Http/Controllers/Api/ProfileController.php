<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\KeyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $posts = Post::where('user_id', $user->id)
            ->with(['user', 'likes', 'comments'])
            ->latest()
            ->get();
        
        $keyRequests = KeyRequest::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'posts' => $posts,
                'postsCount' => $posts->count(),
                'storiesCount' => $user->stories()->where('is_active', 1)->count(),
                'keyRequests' => $keyRequests,
            ]
        ]);
    }

    /**
     * Get list of people (users) with search
     */
    public function people(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->where('id', '!=', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'username', 'email', 'profile_picture', 'bio')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get public profile by user ID or username
     */
    public function publicProfile($identifier)
    {
        $user = User::where('id', $identifier)
            ->orWhere('username', $identifier)
            ->orWhere('name', $identifier)
            ->firstOrFail();

        $user->load([
            'posts' => function ($q) {
                $q->with(['likes', 'comments.user'])
                  ->latest();
            },
            'stories' => function ($q) {
                $q->where('is_active', 1)
                    ->where('expires_at', '>', now());
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'postsCount' => $user->posts->count(),
                'storiesCount' => $user->stories->count(),
            ]
        ]);
    }

    /**
     * Update authenticated user's profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $username = strtolower(preg_replace('/\s+/', '', $user->username));
            $extension = $request->file('profile_picture')->getClientOriginalExtension();
            
            // Generate unique filename
            $filename = "{$username}_" . time() . ".{$extension}";
            
            // Store in storage/app/public/profile_pictures
            $path = $request->file('profile_picture')->storeAs(
                'profile_pictures',
                $filename,
                'public'
            );

            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    /**
     * Get user's feed (their posts)
     */
    public function feed($userId)
    {
        $posts = Post::where('user_id', $userId)
            ->with(['user', 'likes', 'comments.user'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

    /**
     * Get specific user's feed with media and thread separation
     */
    public function userFeed($identifier, $postId = null)
    {
        $user = User::where('username', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('id', $identifier)
            ->with(['posts' => function($query) {
                $query->with(['likes', 'comments.user'])->latest();
            }])
            ->firstOrFail();

        $posts = $user->posts;
        
        // Separate media posts and thread posts
        $mediaPosts = $posts->filter(fn($p) => in_array($p->media_type, ['image', 'video']))->values();
        $threadPosts = $posts->filter(fn($p) => !in_array($p->media_type, ['image', 'video']))->values();

        // Find scroll position if postId provided
        $scrollToIndex = null;
        if ($postId) {
            $scrollToIndex = $mediaPosts->search(fn($post) => $post->id == $postId);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'mediaPosts' => $mediaPosts,
                'threadPosts' => $threadPosts,
                'scrollToIndex' => $scrollToIndex !== false ? $scrollToIndex : null,
            ]
        ]);
    }
}