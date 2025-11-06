<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChannelAccessController extends Controller
{
    /**
     * Request access to a private channel
     */
    public function requestAccess(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isMember(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You must be a community member to request channel access.'
            ], 403);
        }

        if (!$channel->canRequestAccess(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot request access to this channel.'
            ], 400);
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:500'
        ]);

        $accessRequest = \App\Models\ChannelAccessRequest::create([
            'channel_id' => $channel->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'message' => $validated['message'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access request sent! Waiting for approval.',
            'data' => [
                'request_id' => $accessRequest->id
            ]
        ], 201);
    }

    /**
     * View pending access requests
     */
    public function pendingRequests($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id()) && !$channel->isChannelModerator(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $pendingRequests = $channel->pendingAccessRequests()
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $pendingRequests->map(function($req) {
                    return [
                        'id' => $req->id,
                        'message' => $req->message,
                        'status' => $req->status,
                        'created_at' => $req->created_at->toISOString(),
                        'user' => [
                            'id' => $req->user->id,
                            'name' => $req->user->name,
                            'username' => $req->user->username ?? null,
                            'avatar' => $req->user->avatar ? asset('storage/' . $req->user->avatar) : null
                        ]
                    ];
                })
            ]
        ]);
    }

    /**
     * Approve access request
     */
    public function approveRequest($communitySlug, $channelSlug, $requestId)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id()) && !$channel->isChannelModerator(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request = \App\Models\ChannelAccessRequest::where('channel_id', $channel->id)
            ->where('id', $requestId)
            ->firstOrFail();

        $request->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access request approved!'
        ]);
    }

    /**
     * Reject access request
     */
    public function rejectRequest($communitySlug, $channelSlug, $requestId)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id()) && !$channel->isChannelModerator(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request = \App\Models\ChannelAccessRequest::where('channel_id', $channel->id)
            ->where('id', $requestId)
            ->firstOrFail();

        $request->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access request rejected.'
        ]);
    }

    /**
     * Revoke user's channel access
     */
    public function revokeAccess($communitySlug, $channelSlug, $userId)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id()) && !$channel->isChannelModerator(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        \App\Models\ChannelAccessRequest::where('channel_id', $channel->id)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Channel access revoked.'
        ]);
    }

    /**
     * Bulk approve access requests
     */
    public function bulkApprove(Request $request, $communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id()) && !$channel->isChannelModerator(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:channel_access_requests,id'
        ]);

        \App\Models\ChannelAccessRequest::whereIn('id', $validated['request_ids'])
            ->where('channel_id', $channel->id)
            ->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Access requests approved!'
        ]);
    }

    /**
     * Grant access to all community members
     */
    public function grantAccessToAll($communitySlug, $channelSlug)
    {
        $community = Community::where('slug', $communitySlug)->firstOrFail();
        $channel = $community->channels()->where('slug', $channelSlug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $members = $community->approvedMembers()->get();
        $granted = 0;

        foreach ($members as $member) {
            if (!$channel->hasAccess($member->user_id)) {
                \App\Models\ChannelAccessRequest::create([
                    'channel_id' => $channel->id,
                    'user_id' => $member->user_id,
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now()
                ]);
                $granted++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Access granted to {$granted} members!"
        ]);
    }
}
