<?php

namespace App\Http\Controllers;

use App\Models\KeyRequest;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeyRequestController extends Controller
{
    public function send($receiverId)
    {
        $senderId = auth()->id();

        // Check if request already exists
        $existing = KeyRequest::where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })
        ->orWhere(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })
        ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Request already sent or exists!');
        }

        // Create new request
        KeyRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Request sent successfully!');
    }

    public function accept($id)
    {
        $request = KeyRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $request->update(['status' => 'accepted']);

        return back()->with('success', 'Key request accepted!');
    }

    public function incoming()
    {
        $userId = auth()->id();

        // Get pending key requests
        $requests = KeyRequest::where('receiver_id', $userId)
            ->where('status', 'pending')
            ->with('sender')
            ->latest()
            ->get();

        // Get recent likes on user's posts (last 30 days)
        $likes = Like::whereHas('post', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId) // Exclude own likes
            ->with(['user', 'post'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        // Get recent comments on user's posts (last 30 days)
        $comments = Comment::whereHas('post', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId) // Exclude own comments
            ->with(['user', 'post'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('notifications.incoming', compact('requests', 'likes', 'comments'));
    }

    public function myKeys()
    {
        $userId = auth()->id();

        // Keys where I accepted someone's request OR they accepted mine
        $keys = KeyRequest::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->where('status', 'accepted')
            ->with(['sender', 'receiver'])
            ->get();

        return view('keys.mykeys', compact('keys'));
    }

    public function reject($id)
    {
        $request = KeyRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Key request rejected!');
    }
}