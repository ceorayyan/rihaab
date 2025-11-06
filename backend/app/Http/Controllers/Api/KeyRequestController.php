<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeyRequest;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeyRequestController extends Controller
{
    /**
     * Send a key request
     */
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
            return response()->json([
                'success' => false,
                'message' => 'Request already sent or exists!'
            ], 400);
        }

        // Create new request
        $keyRequest = KeyRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request sent successfully!',
            'data' => $keyRequest->load(['sender', 'receiver'])
        ], 201);
    }

    /**
     * Accept a key request
     */
    public function accept($id)
    {
        $request = KeyRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $request->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Key request accepted!',
            'data' => $request->load(['sender', 'receiver'])
        ]);
    }

    /**
     * Reject a key request
     */
    public function reject($id)
    {
        $request = KeyRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $request->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Key request rejected!',
            'data' => $request
        ]);
    }

    /**
     * Get incoming notifications (key requests, likes, comments)
     */
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
            ->where('user_id', '!=', $userId)
            ->with(['user', 'post'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        // Get recent comments on user's posts (last 30 days)
        $comments = Comment::whereHas('post', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->with(['user', 'post'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
                'likes' => $likes,
                'comments' => $comments,
            ]
        ]);
    }

    /**
     * Get user's keys (accepted connections)
     */
    public function myKeys()
    {
        $userId = auth()->id();

        $keys = KeyRequest::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->where('status', 'accepted')
            ->with(['sender', 'receiver'])
            ->get();

        // Transform to show the other person in the connection
        $connections = $keys->map(function ($key) use ($userId) {
            $otherUser = $key->sender_id == $userId ? $key->receiver : $key->sender;
            
            return [
                'id' => $key->id,
                'user' => $otherUser,
                'connected_at' => $key->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $connections
        ]);
    }

    /**
     * Get list of sent key requests
     */
    public function sent()
    {
        $requests = KeyRequest::where('sender_id', auth()->id())
            ->with('receiver')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }
}