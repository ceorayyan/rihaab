<?php
// app/Http/Controllers/CommunityChannelController.php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityChannel;
use App\Models\CommunityPost;
use App\Models\ChannelModerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityChannelController extends Controller
{
    // Show channel with posts
public function show($communitySlug, $channelSlug)
{
    $community = Community::where('slug', $communitySlug)->firstOrFail();
    $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

    // SECURITY: Check if user is an approved member
    if (!$community->isMember(Auth::id())) {
        return redirect()->route('communities.show', $communitySlug)
            ->with('error', 'You must be an approved member to view channels!');
    }

    // SECURITY: Check if user has access to this channel
    if (!$channel->hasAccess(Auth::id())) {
        // Show access request page if they can request
        if ($channel->canRequestAccess(Auth::id())) {
            return view('communities.channel-request-access', compact('community', 'channel'));
        }
        
        // Otherwise, just block access
        abort(403, 'You do not have access to this channel.');
    }

    $posts = $channel->posts()
        ->with(['user'])
        ->withCount('reactions')
        ->latest('is_pinned')
        ->latest()
        ->paginate(20);

    $canPost = $channel->canPost(Auth::id());
    $canModerate = $channel->canModerate(Auth::id());
    $userRole = $community->getMemberRole(Auth::id());
    $isChannelModerator = $channel->isChannelModerator(Auth::id());
    $pendingAccessCount = $channel->pendingAccessRequests()->count();

    return view('communities.channel', compact(
        'community', 
        'channel', 
        'posts', 
        'canPost', 
        'canModerate',
        'userRole',
        'isChannelModerator',
        'pendingAccessCount'
    ));
}

    // Create new channel (admin only)
  public function store(Request $request, $communitySlug)
{
    $community = Community::where('slug', $communitySlug)->firstOrFail();

    if (!$community->isAdmin(Auth::id())) {
        abort(403, 'Unauthorized action.');
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'type' => 'required|in:announcement,general,restricted',
        'is_private' => 'boolean'
    ]);

    $validated['community_id'] = $community->id;
    $validated['position'] = $community->channels()->max('position') + 1;

    $channel = CommunityChannel::create($validated);

    return back()->with('success', 'Channel created successfully!');
}

    // Update channel
    public function update(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$channel->canEdit(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:announcement,general,restricted'
        ]);

        $channel->update($validated);

        return back()->with('success', 'Channel updated successfully!');
    }

    // Delete channel
    public function destroy($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $channel->delete();

        return redirect()->route('communities.show', $communitySlug)
            ->with('success', 'Channel deleted successfully!');
    }

    // Show channel moderators management page
    public function moderators($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $channelModerators = $channel->moderators()->with('user')->get();
        $availableMembers = $community->approvedMembers()
            ->whereNotIn('user_id', $channelModerators->pluck('user_id'))
            ->with('user')
            ->get();

        return view('communities.channel-moderators', compact(
            'community', 
            'channel', 
            'channelModerators',
            'availableMembers'
        ));
    }

    // Add channel moderator
    public function addModerator(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Check if user is a member
        if (!$community->isMember($validated['user_id'])) {
            return back()->with('error', 'User must be a community member!');
        }

        // Check if already a moderator
        if ($channel->isChannelModerator($validated['user_id'])) {
            return back()->with('error', 'User is already a channel moderator!');
        }

        ChannelModerator::create([
            'channel_id' => $channel->id,
            'user_id' => $validated['user_id']
        ]);

        return back()->with('success', 'Channel moderator added!');
    }

    // Remove channel moderator
    public function removeModerator($communitySlug, $channelSlug, $moderatorId)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $moderator = ChannelModerator::where('channel_id', $channel->id)
            ->where('id', $moderatorId)
            ->firstOrFail();

        $moderator->delete();

        return back()->with('success', 'Channel moderator removed!');
    }
}