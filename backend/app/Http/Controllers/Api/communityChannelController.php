<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityChannelController extends Controller
{
    /**
     * Show channel with posts
     */
    public function show($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isMember(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You must be an approved member to view channels!'
            ], 403);
        }

        if (!$channel->hasAccess(Auth::id())) {
            if ($channel->canRequestAccess(Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access required. You can request access to this channel.',
                    'can_request_access' => true
                ], 403);
            }
            
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
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'channel' => $this->formatChannel($channel, $community),
                'posts' => $posts->map(fn($p) => $this->formatPost($p)),
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
     * Create new channel
     */
    public function store(Request $request, $communitySlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:announcement,general,restricted',
            'is_private' => 'boolean'
        ]);

        $validated['community_id'] = $community->id;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['position'] = $community->channels()->max('position') + 1;

        $channel = CommunityChannel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Channel created successfully!',
            'data' => [
                'channel' => $this->formatChannel($channel, $community)
            ]
        ], 201);
    }

    /**
     * Update channel
     */
    public function update(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$channel->canEdit(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:announcement,general,restricted'
        ]);

        $channel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Channel updated successfully!',
            'data' => [
                'channel' => $this->formatChannel($channel, $community)
            ]
        ]);
    }

    /**
     * Delete channel
     */
    public function destroy($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $channel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Channel deleted successfully!'
        ]);
    }

    /**
     * Get channel moderators
     */
    public function moderators($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $channelModerators = $channel->moderators()->with('user')->get();
        $availableMembers = $community->approvedMembers()
            ->whereNotIn('user_id', $channelModerators->pluck('user_id'))
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'moderators' => $channelModerators->map(function($mod) {
                    return [
                        'id' => $mod->id,
                        'user' => [
                            'id' => $mod->user->id,
                            'name' => $mod->user->name,
                            'username' => $mod->user->username ?? null,
                            'avatar' => $mod->user->avatar ? asset('storage/' . $mod->user->avatar) : null
                        ],
                        'created_at' => $mod->created_at->toISOString()
                    ];
                }),
                'available_members' => $availableMembers->map(function($member) {
                    return [
                        'user_id' => $member->user_id,
                        'name' => $member->user->name,
                        'username' => $member->user->username ?? null
                    ];
                })
            ]
        ]);
    }

    /**
     * Add channel moderator
     */
    public function addModerator(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$community->isMember($validated['user_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'User must be a community member!'
            ], 400);
        }

        if ($channel->isChannelModerator($validated['user_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a channel moderator!'
            ], 400);
        }

        $moderator = \App\Models\ChannelModerator::create([
            'channel_id' => $channel->id,
            'user_id' => $validated['user_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Channel moderator added!',
            'data' => [
                'moderator_id' => $moderator->id
            ]
        ], 201);
    }

    /**
     * Remove channel moderator
     */
    public function removeModerator($communitySlug, $channelSlug, $moderatorId)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $moderator = \App\Models\ChannelModerator::where('channel_id', $channel->id)
            ->where('id', $moderatorId)
            ->firstOrFail();

        $moderator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Channel moderator removed!'
        ]);
    }

    private function formatChannel($channel, $community)
    {
        return [
            'id' => $channel->id,
            'name' => $channel->name,
            'slug' => $channel->slug,
            'description' => $channel->description,
            'type' => $channel->type,
            'is_private' => $channel->is_private ?? false,
            'position' => $channel->position,
            'created_at' => $channel->created_at->toISOString(),
            'permissions' => [
                'can_post' => $channel->canPost(Auth::id()),
                'can_moderate' => $channel->canModerate(Auth::id()),
                'can_edit' => $channel->canEdit(Auth::id()),
                'is_channel_moderator' => $channel->isChannelModerator(Auth::id())
            ]
        ];
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
}
