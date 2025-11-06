<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle like on a post
     */
    public function toggle(Post $post)
    {
        $user = auth()->user();

        // Check if already liked
        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $liked = false;
            $message = 'Post unliked';
        } else {
            // Like
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
            $message = 'Post liked';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'liked' => $liked,
            'likes_count' => $post->likes()->count()
        ]);
    }
}