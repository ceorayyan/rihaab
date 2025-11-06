<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityChannel;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    // Show all communities
    public function index()
    {
        $communities = Community::with('creator', 'members')
            ->withCount('members')
            ->latest()
            ->paginate(12);

        $myCommunities = Community::whereHas('members', function($q) {
            $q->where('user_id', Auth::id())
              ->where('status', 'approved');
        })->get();

        return view('communities.index', compact('communities', 'myCommunities'));
    }

    // Show create community form
    public function create()
    {
        return view('communities.create');
    }

    // Store new community
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

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('community_icons', 'public');
        }

        // Handle banner upload
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

        // Create default announcement channel
        CommunityChannel::create([
            'community_id' => $community->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
            'description' => 'Official announcements from admins',
            'type' => 'announcement',
            'position' => 0
        ]);

        // Create default general channel
        CommunityChannel::create([
            'community_id' => $community->id,
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussion',
            'type' => 'general',
            'position' => 1
        ]);

        return redirect()->route('communities.show', $community->slug)
            ->with('success', 'Community created successfully!');
    }

    // Show single community
    public function show($slug)
    {
        $community = Community::where('slug', $slug)
            ->with(['channels', 'members.user', 'pendingMembers'])
            ->firstOrFail();

        $isMember = $community->isMember(Auth::id());
        $userRole = $community->getMemberRole(Auth::id());

        // Get first channel or default to general
        $defaultChannel = $community->channels->first();

        return view('communities.show', compact('community', 'isMember', 'userRole', 'defaultChannel'));
    }

    // Join community
    public function join($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if ($community->isMember(Auth::id())) {
            return back()->with('error', 'You are already a member!');
        }

        if ($community->hasPendingRequest(Auth::id())) {
            return back()->with('error', 'Your request is already pending!');
        }

        // For private communities, create pending request
        // For public communities, auto-approve
        $status = $community->is_private ? 'pending' : 'approved';

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'status' => $status
        ]);

        if ($community->is_private) {
            return back()->with('success', 'Join request sent! Waiting for admin approval.');
        }

        return back()->with('success', 'You have joined the community!');
    }

    // Leave community
    public function leave($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if ($community->creator_id === Auth::id()) {
            return back()->with('error', 'Community creator cannot leave!');
        }

        CommunityMember::where('community_id', $community->id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('communities.index')
            ->with('success', 'You have left the community!');
    }

    // Show community settings (admin only)
    public function settings($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        return view('communities.settings', compact('community'));
    }

    // Update community settings
    public function update(Request $request, $slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
            'is_private' => 'boolean'
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            if ($community->icon) {
                Storage::disk('public')->delete($community->icon);
            }
            $validated['icon'] = $request->file('icon')->store('community_icons', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($community->banner) {
                Storage::disk('public')->delete($community->banner);
            }
            $validated['banner'] = $request->file('banner')->store('community_banners', 'public');
        }

        $community->update($validated);

        return back()->with('success', 'Community updated successfully!');
    }

    // Show member management page
    public function members($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $approvedMembers = $community->approvedMembers()
            ->with('user')
            ->orderBy('role')
            ->get();

        $pendingMembers = $community->pendingMembers()
            ->with('user')
            ->get();

        return view('communities.members', compact('community', 'approvedMembers', 'pendingMembers'));
    }

    // Approve member request
    public function approveMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        $member->update(['status' => 'approved']);

        return back()->with('success', 'Member approved!');
    }

    // Reject member request
    public function rejectMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        $member->delete();

        return back()->with('success', 'Member request rejected!');
    }

    // Remove member from community
    public function removeMember($slug, $memberId)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        // Prevent removing the creator
        if ($member->user_id === $community->creator_id) {
            return back()->with('error', 'Cannot remove the community creator!');
        }

        $member->delete();

        return back()->with('success', 'Member removed!');
    }

    // Change member role
    public function updateMemberRole($slug, $memberId, Request $request)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,moderator,member'
        ]);

        $member = CommunityMember::where('community_id', $community->id)
            ->where('id', $memberId)
            ->firstOrFail();

        // Prevent changing creator's role
        if ($member->user_id === $community->creator_id) {
            return back()->with('error', 'Cannot change the creator\'s role!');
        }

        $member->update(['role' => $validated['role']]);

        return back()->with('success', 'Member role updated!');
    }

    // Delete community
    public function destroy($slug)
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        if (!$community->isAdmin(Auth::id())) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated files
        if ($community->icon) {
            Storage::disk('public')->delete($community->icon);
        }
        if ($community->banner) {
            Storage::disk('public')->delete($community->banner);
        }

        $community->delete();

        return redirect()->route('communities.index')
            ->with('success', 'Community deleted successfully!');
    }
}