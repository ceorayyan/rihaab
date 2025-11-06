<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\KeyRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Get feed posts (authenticated user + key friends)
     */
    public function index()
    {
        $user = auth()->user();

        // Get connected users (key friends)
        $connections = KeyRequest::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })->where('status', 'accepted')->get();

        $friendIds = $connections->map(function ($c) use ($user) {
            return $c->sender_id == $user->id ? $c->receiver_id : $c->sender_id;
        });

        // Include my posts + friends posts
        $posts = Post::whereIn('user_id', $friendIds->push($user->id))
            ->with(['user', 'likes', 'comments.user', 'poll.options'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts->map(function($post) use ($user) {
                return $this->formatPost($post, $user);
            }),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]
        ]);
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'standard');
        
        if ($type === 'poll') {
            // Validate poll
            $validator = Validator::make($request->all(), [
                'poll_question' => 'required|string|max:500',
                'poll_options' => 'required|array|min:2|max:10',
                'poll_options.*' => 'required|string|max:200',
                'poll_duration' => 'required|integer|in:1,3,7,14,30',
                'privacy' => 'required|in:public,friends,private',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            // Create post with poll question as content
            $post = Post::create([
                'user_id' => auth()->id(),
                'content' => $request->poll_question,
                'type' => 'poll',
                'privacy' => $request->privacy,
            ]);
            
            // Create poll
            $poll = Poll::create([
                'post_id' => $post->id,
                'question' => $request->poll_question,
                'duration_days' => $request->poll_duration,
                'allow_multiple' => $request->allow_multiple ?? false,
                'expires_at' => now()->addDays($request->poll_duration),
            ]);
            
            // Create poll options
            foreach ($request->poll_options as $optionText) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => trim($optionText),
                    'votes_count' => 0,
                ]);
            }
            
            // Load relationships for response
            $post->load('poll.options');
            
            return response()->json([
                'success' => true,
                'message' => 'Poll created successfully',
                'post' => $this->formatPost($post, auth()->user()),
            ], 201);
            
        } else if ($type === 'qa') {
            // Validate Q&A
            $validator = Validator::make($request->all(), [
                'qa_question' => 'required|string|max:500',
                'qa_details' => 'nullable|string|max:2000',
                'qa_category' => 'nullable|string',
                'privacy' => 'required|in:public,friends,private',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            // Create post with Q&A question as content
            $post = Post::create([
                'user_id' => auth()->id(),
                'content' => $request->qa_question,
                'type' => 'qa',
                'privacy' => $request->privacy,
                'metadata' => json_encode([
                    'qa_details' => $request->qa_details,
                    'qa_category' => $request->qa_category,
                    'qa_anonymous' => $request->qa_anonymous ?? false,
                ]),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Question posted successfully',
                'post' => $this->formatPost($post, auth()->user()),
            ], 201);
            
        } else {
            // Standard post - check if either content or media exists
            if (!$request->filled('content') && !$request->hasFile('media')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please add content or media to your post'
                ], 400);
            }
            
            // Standard post validation
            $validator = Validator::make($request->all(), [
                'content' => 'nullable|string|max:5000',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
                'privacy' => 'required|in:public,friends,private',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            // Create standard post
            $post = Post::create([
                'user_id' => auth()->id(),
                'content' => $request->content ?? '',
                'type' => 'standard',
                'privacy' => $request->privacy,
            ]);
            
            // Handle media upload if present
            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $path = $file->store('posts', 'public');
                
                $post->update([
                    'media_path' => $path,
                    'media_type' => $file->getMimeType(),
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'post' => $this->formatPost($post, auth()->user()),
            ], 201);
        }
    }

    /**
     * Vote on a poll
     */
    public function votePoll(Request $request, Post $post)
    {
        // Check if post is a poll
        if ($post->type !== 'poll' || !$post->poll) {
            return response()->json([
                'success' => false,
                'message' => 'This post is not a poll'
            ], 400);
        }

        $poll = $post->poll;

        // Check if poll has expired
        if (now()->greaterThan($poll->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'This poll has expired'
            ], 400);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'option_id' => 'required|integer|exists:poll_options,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $optionId = $request->option_id;

        // Verify option belongs to this poll
        $option = PollOption::where('id', $optionId)
            ->where('poll_id', $poll->id)
            ->first();

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid poll option'
            ], 400);
        }

        $user = auth()->user();

        // Check if user has already voted
        $existingVote = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            // If poll doesn't allow multiple votes, prevent voting again
            if (!$poll->allow_multiple) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted on this poll'
                ], 400);
            }

            // For multiple choice polls, check if they already voted for this option
            $alreadyVotedForOption = PollVote::where('poll_id', $poll->id)
                ->where('user_id', $user->id)
                ->where('poll_option_id', $optionId)
                ->exists();

            if ($alreadyVotedForOption) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted for this option'
                ], 400);
            }
        }

        // Record the vote
        PollVote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $optionId,
            'user_id' => $user->id,
        ]);

        // Increment vote count
        $option->increment('votes_count');

        // Reload poll with options
        $poll->load('options');

        // Return updated poll data
        return response()->json([
            'success' => true,
            'message' => 'Vote recorded successfully',
            'poll' => [
                'id' => $poll->id,
                'question' => $poll->question,
                'duration_days' => $poll->duration_days,
                'allow_multiple' => $poll->allow_multiple,
                'expires_at' => $poll->expires_at,
                'is_expired' => now()->greaterThan($poll->expires_at),
                'user_has_voted' => true,
                'options' => $poll->options->map(function($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text,
                        'votes_count' => $option->votes_count,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get a specific post
     */
    public function show(Post $post)
    {
        // Check if user can view this post
        if (!$this->canViewPost($post)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this post'
            ], 403);
        }

        $post->load(['user', 'likes', 'comments.user', 'poll.options']);

        return response()->json([
            'success' => true,
            'data' => $this->formatPost($post, auth()->user())
        ]);
    }

    /**
     * Update a post
     */
    public function update(Request $request, Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this post'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'privacy' => 'nullable|in:public,friends,private',
        ]);

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $this->formatPost($post, auth()->user())
        ]);
    }

    /**
     * Delete a post
     */
    public function destroy(Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post'
            ], 403);
        }

        // Delete media file if exists
        if ($post->media_path && Storage::disk('public')->exists($post->media_path)) {
            Storage::disk('public')->delete($post->media_path);
        }

        // Delete associated poll and options if exists
        if ($post->poll) {
            // Delete poll votes first
            PollVote::where('poll_id', $post->poll->id)->delete();
            $post->poll->options()->delete();
            $post->poll->delete();
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }

    /**
     * Format post data for API response
     */
    private function formatPost($post, $user)
    {
        $formatted = [
            'id' => $post->id,
            'content' => $post->content,
            'media_path' => $post->media_path ? asset('storage/' . $post->media_path) : null,
            'media_type' => $post->media_type,
            'type' => $post->type,
            'privacy' => $post->privacy,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'username' => $post->user->username,
                'profile_picture' => $post->user->profile_picture_url ?? null,
            ],
            'likes_count' => $post->likes->count(),
            'comments_count' => $post->comments->count(),
            'is_liked' => $post->likes->contains('user_id', $user->id),
            'created_at' => $post->created_at->toISOString(),
            'created_human' => $post->created_at->diffForHumans(),
            'updated_at' => $post->updated_at->toISOString(),
        ];

        // Add poll data if post is a poll
        if ($post->type === 'poll' && $post->poll) {
            // Check if user has voted
            $userHasVoted = PollVote::where('poll_id', $post->poll->id)
                ->where('user_id', $user->id)
                ->exists();

            $formatted['poll'] = [
                'id' => $post->poll->id,
                'question' => $post->poll->question,
                'duration_days' => $post->poll->duration_days,
                'allow_multiple' => $post->poll->allow_multiple,
                'expires_at' => $post->poll->expires_at,
                'is_expired' => now()->greaterThan($post->poll->expires_at),
                'user_has_voted' => $userHasVoted,
                'options' => $post->poll->options->map(function($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text,
                        'votes_count' => $option->votes_count,
                    ];
                }),
            ];
        }

        // Add Q&A metadata if post is Q&A
        if ($post->type === 'qa' && $post->metadata) {
            $metadata = json_decode($post->metadata, true);
            $formatted['qa'] = [
                'details' => $metadata['qa_details'] ?? '',
                'category' => $metadata['qa_category'] ?? '',
                'anonymous' => $metadata['qa_anonymous'] ?? false,
            ];
        }

        return $formatted;
    }

    /**
     * Check if user can view post
     */
    private function canViewPost($post)
    {
        $user = auth()->user();

        // Own post
        if ($post->user_id === $user->id) {
            return true;
        }

        // Public post
        if ($post->privacy === 'public') {
            return true;
        }

        // Friends only - check if connected
        if ($post->privacy === 'friends') {
            $isConnected = KeyRequest::where(function ($q) use ($user, $post) {
                $q->where('sender_id', $user->id)->where('receiver_id', $post->user_id)
                  ->orWhere('sender_id', $post->user_id)->where('receiver_id', $user->id);
            })->where('status', 'accepted')->exists();

            return $isConnected;
        }

        // Private post
        return false;
    }
}