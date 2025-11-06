<?php
// app/Http/Controllers/CommunityPostController.php

namespace App\Http\Controllers;

use App\Models\CommunityChannel;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    // Store new post
    public function store(Request $request, $communitySlug, $channelSlug)
    {
        $channel = CommunityChannel::whereHas('community', function($q) use ($communitySlug) {
            $q->where('slug', $communitySlug);
        })->where('slug', $channelSlug)->firstOrFail();

        // SECURITY: Check if user can post
        if (!$channel->canPost(Auth::id())) {
            abort(403, 'You do not have permission to post in this channel.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|file|max:10240'
        ]);

        $postData = [
            'channel_id' => $channel->id,
            'user_id' => Auth::id(),
            'content' => $validated['content']
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $postData['media_path'] = $file->store('community_posts', 'public');
            $postData['media_type'] = $this->getMediaType($file->getMimeType());
        }

        CommunityPost::create($postData);

        return back()->with('success', 'Post created successfully!');
    }

    // Update post
    public function update(Request $request, $postId)
    {
        $post = CommunityPost::findOrFail($postId);

        // SECURITY: Check if user can edit
        if (!$post->canEdit(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $post->update($validated);

        return back()->with('success', 'Post updated successfully!');
    }

    // Delete post
    public function destroy($postId)
    {
        $post = CommunityPost::findOrFail($postId);

        // SECURITY: Check if user can delete
        if (!$post->canDelete(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return back()->with('success', 'Post deleted successfully!');
    }

    // Pin/Unpin post (admin/moderator only)
    public function togglePin($postId)
    {
        $post = CommunityPost::findOrFail($postId);

        // SECURITY: Check if user can moderate
        if (!$post->channel->canModerate(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $post->update(['is_pinned' => !$post->is_pinned]);

        return back()->with('success', $post->is_pinned ? 'Post pinned!' : 'Post unpinned!');
    }

    private function getMediaType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        return 'file';
    }
}