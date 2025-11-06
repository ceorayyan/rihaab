<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityPostController extends Controller
{
    /**
     * Get posts for a channel
     */
    public function index(Request $request, $communitySlug, $channelSlug)
    {
        $channel = CommunityChannel::whereHas('community', function($q) use ($communitySlug) {
            $q->where('slug', $communitySlug);
        })->where('slug', $channelSlug)->firstOrFail();

        if (!$channel->hasAccess(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this channel.'
            ], 403);
        }

        $posts = $channel->posts()
            ->with(['user'])
            ->withCount('reactions')
            ->latest('is_pinned')
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'posts' => $posts->map(fn($post) => $this->formatPost($post)),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total()
                ]
            ]
        ]);
    }

    /**
     * Store new post
     */
    public function store(Request $request, $communitySlug, $channelSlug)
    {
        $channel = CommunityChannel::whereHas('community', function($q) use ($communitySlug) {
            $q->where('slug', $communitySlug);
        })->where('slug', $channelSlug)->firstOrFail();

        if (!$channel->canPost(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to post in this channel.'
            ], 403);
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

        $post = CommunityPost::create($postData);
        $post->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully!',
            'data' => [
                'post' => $this->formatPost($post)
            ]
        ], 201);
    }

    /**
     * Get single post
     */
    public function show($postId)
    {
        $post = CommunityPost::with(['user', 'channel.community'])
            ->withCount('reactions')
            ->findOrFail($postId);

        if (!$post->channel->hasAccess(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this channel.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'post' => $this->formatPost($post)
            ]
        ]);
    }

    /**
     * Update post
     */
    public function update(Request $request, $postId)
    {
        $post = CommunityPost::findOrFail($postId);

        if (!$post->canEdit(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $post->update($validated);
        $post->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully!',
            'data' => [
                'post' => $this->formatPost($post)
            ]
        ]);
    }

    /**
     * Delete post
     */
    public function destroy($postId)
    {
        $post = CommunityPost::findOrFail($postId);

        if (!$post->canDelete(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully!'
        ]);
    }

    /**
     * Pin/Unpin post
     */
    public function togglePin($postId)
    {
        $post = CommunityPost::findOrFail($postId);

        if (!$post->channel->canModerate(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $post->update(['is_pinned' => !$post->is_pinned]);
        $post->load('user');

        return response()->json([
            'success' => true,
            'message' => $post->is_pinned ? 'Post pinned!' : 'Post unpinned!',
            'data' => [
                'post' => $this->formatPost($post)
            ]
        ]);
    }

    private function formatPost($post)
    {
        return [
            'id' => $post->id,
            'content' => $post->content,
            'is_pinned' => $post->is_pinned,
            'media_path' => $post->media_path ? asset('storage/' . $post->media_path) : null,
            'media_type' => $post->media_type,
            'reactions_count' => $post->reactions_count ?? 0,
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'username' => $post->user->username ?? null,
                'avatar' => $post->user->avatar ? asset('storage/' . $post->user->avatar) : null
            ],
            'permissions' => [
                'can_edit' => $post->canEdit(Auth::id()),
                'can_delete' => $post->canDelete(Auth::id())
            ]
        ];
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

