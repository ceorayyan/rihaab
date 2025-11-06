<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AuthController;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Get all communities
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        
        $communities = Community::with('creator', 'members')
            ->withCount('members')
            ->latest()
            ->paginate($perPage);

        $myCommunities = Community::whereHas('members', function($q) {
            $q->where('user_id', Auth::id())
              ->where('status', 'approved');
        })->get();

        return response()->json([
            'success' => true,
            'data' => [
                'communities' => $communities->map(fn($c) => $this->formatCommunity($c)),
                'my_communities' => $myCommunities->map(fn($c) => $this->formatCommunity($c, true)),
                'pagination' => [
                    'current_page' => $communities->currentPage(),
                    'last_page' => $communities->lastPage(),
                    'per_page' => $communities->perPage(),
                    'total' => $communities->total()
                ]
            ]
        ]);
    }

    /**
     * Store new community
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:communities',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
            'is_private' => 'boolean'
        ]);

        $validated['creator_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('community_icons', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('community_banners', 'public');
        }

        $community = Community::create($validated);

        // Add creator as admin member
        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
            'status' => 'approved'
        ]);

        // Create default channels
        CommunityChannel::create([
            'community_id' => $community->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
            'description' => 'Official announcements from admins',
            'type' => 'announcement',
            'position' => 0
        ]);

        CommunityChannel::create([
            'community_id' => $community->id,
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussion',
            'type' => 'general',
            'position' => 1
        ]);

        $community->load(['creator', 'members', 'channels']);

        return response()->json([
            'success' => true,
            'message' => 'Community created successfully!',
            'data' => [
                'community' => $this->formatCommunity($community, true)
            ]
        ], 201);
    }

    /**
     * Show single community
     */
    public function show($slug)
    {
        $community = Community::where('slug', $slug)
            ->with(['channels', 'members.user', 'pendingMembers'])
            ->withCount('members')
            ->firstOrFail();

        $isMember = $community->isMember(Auth::id());
        $userRole = $community->getMemberRole(Auth::id());

        return response()->json([
            'success' => true,
            'data' => [
                'community' => $this->formatCommunity($community, true),
                'is_member' => $isMember,
                'user_role' => $userRole,
                'channels' => $community->channels->map(fn($ch) => $this->formatChannel($ch))
            ]
        ]);
    }

    /**
     * Join community
     */
    public function join($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if ($community->isMember(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member!'
            ], 400);
        }

        if ($community->hasPendingRequest(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Your request is already pending!'
            ], 400);
        }

        $status = $community->is_private ? 'pending' : 'approved';

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'status' => $status
        ]);

        $message = $community->is_private 
            ? 'Join request sent! Waiting for admin approval.'
            : 'You have joined the community!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'status' => $status
            ]
        ]);
    }

    /**
     * Leave community
     */
    public function leave($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if ($community->creator_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Community creator cannot leave!'
            ], 403);
        }

        CommunityMember::where('community_id', $community->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have left the community!'
        ]);
    }

    /**
     * Update community settings
     */
    public function update(Request $request, $slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
            'is_private' => 'boolean'
        ]);

        if ($request->hasFile('icon')) {
            if ($community->icon) {
                Storage::disk('public')->delete($community->icon);
            }
            $validated['icon'] = $request->file('icon')->store('community_icons', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($community->banner) {
                Storage::disk('public')->delete($community->banner);
            }
            $validated['banner'] = $request->file('banner')->store('community_banners', 'public');
        }

        $community->update($validated);
        $community->load(['creator', 'members', 'channels']);

        return response()->json([
            'success' => true,
            'message' => 'Community updated successfully!',
            'data' => [
                'community' => $this->formatCommunity($community, true)
            ]
        ]);
    }

    /**
     * Get community members
     */
    public function members($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $approvedMembers = $community->approvedMembers()
            ->with('user')
            ->orderBy('role')
            ->get();

        $pendingMembers = $community->pendingMembers()
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'approved_members' => $approvedMembers->map(fn($m) => $this->formatMember($m)),
                'pending_members' => $pendingMembers->map(fn($m) => $this->formatMember($m))
            ]
        ]);
    }

    /**
     * Approve member request
     */
    public function approveMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        $member->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Member approved!'
        ]);
    }

    /**
     * Reject member request
     */
    public function rejectMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member request rejected!'
        ]);
    }

    /**
     * Remove member from community
     */
    public function removeMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        if ($member->user_id === $community->creator_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the community creator!'
            ], 400);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member removed!'
        ]);
    }

    /**
     * Update member role
     */
    public function updateMemberRole($slug, $memberId, Request $request)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,moderator,member'
        ]);

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        if ($member->user_id === $community->creator_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change the creator\'s role!'
            ], 400);
        }

        $member->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Member role updated!'
        ]);
    }

    /**
     * Delete community
     */
    public function destroy($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        if ($community->icon) {
            Storage::disk('public')->delete($community->icon);
        }
        if ($community->banner) {
            Storage::disk('public')->delete($community->banner);
        }

        $community->delete();

        return response()->json([
            'success' => true,
            'message' => 'Community deleted successfully!'
        ]);
    }

    /**
     * Format community data
     */
    private function formatCommunity($community, $detailed = false)
    {
        $data = [
            'id' => $community->id,
            'name' => $community->name,
            'slug' => $community->slug,
            'description' => $community->description,
            'icon' => $community->icon ? asset('storage/' . $community->icon) : null,
            'banner' => $community->banner ? asset('storage/' . $community->banner) : null,
            'is_private' => $community->is_private,
            'members_count' => $community->members_count ?? $community->members->count(),
            'created_at' => $community->created_at->toISOString(),
            'creator' => [
                'id' => $community->creator->id,
                'name' => $community->creator->name,
                'username' => $community->creator->username ?? null
            ]
        ];

        if ($detailed) {
            $data['permissions'] = [
                'is_admin' => $community->isAdmin(Auth::id()),
                'is_member' => $community->isMember(Auth::id()),
                'user_role' => $community->getMemberRole(Auth::id())
            ];
        }

        return $data;
    }

    /**
     * Format channel data
     */
    private function formatChannel($channel)
    {
        return [
            'id' => $channel->id,
            'name' => $channel->name,
            'slug' => $channel->slug,
            'description' => $channel->description,
            'type' => $channel->type,
            'is_private' => $channel->is_private ?? false,
            'position' => $channel->position,
            'permissions' => [
                'can_access' => $channel->hasAccess(Auth::id()),
                'can_post' => $channel->canPost(Auth::id()),
                'can_moderate' => $channel->canModerate(Auth::id())
            ]
        ];
    }

    /**
     * Format member data
     */
    private function formatMember($member)
    {
        return [
            'id' => $member->id,
            'role' => $member->role,
            'status' => $member->status,
            'joined_at' => $member->created_at->toISOString(),
            'user' => [
                'id' => $member->user->id,
                'name' => $member->user->name,
                'username' => $member->user->username ?? null,
                'avatar' => $member->user->avatar ? asset('storage/' . $member->user->avatar) : null
            ]
        ];
    }
}
