<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // Load user relation so it's not null
        $comment->load('user');

        return response()->json([
            'id'      => $comment->id,
            'content' => $comment->content,
            'user'    => $comment->user->name,
            'created' => $comment->created_at->diffForHumans(),
        ]);
    }
}
