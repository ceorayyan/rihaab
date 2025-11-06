<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    /**
     * Vote on a poll
     */
    public function vote(Request $request, Post $post)
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

            // If same option, don't allow duplicate vote
            if ($existingVote->poll_option_id == $optionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted for this option'
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
     * Remove vote from a poll (for multiple choice polls)
     */
    public function removeVote(Request $request, Post $post)
    {
        if ($post->type !== 'poll' || !$post->poll) {
            return response()->json([
                'success' => false,
                'message' => 'This post is not a poll'
            ], 400);
        }

        $poll = $post->poll;

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

        $user = auth()->user();
        $optionId = $request->option_id;

        // Find the vote
        $vote = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->where('poll_option_id', $optionId)
            ->first();

        if (!$vote) {
            return response()->json([
                'success' => false,
                'message' => 'Vote not found'
            ], 404);
        }

        // Find the option and decrement vote count
        $option = PollOption::find($optionId);
        if ($option && $option->votes_count > 0) {
            $option->decrement('votes_count');
        }

        // Delete the vote
        $vote->delete();

        // Reload poll with options
        $poll->load('options');

        return response()->json([
            'success' => true,
            'message' => 'Vote removed successfully',
            'poll' => [
                'id' => $poll->id,
                'question' => $poll->question,
                'duration_days' => $poll->duration_days,
                'allow_multiple' => $poll->allow_multiple,
                'expires_at' => $poll->expires_at,
                'is_expired' => now()->greaterThan($poll->expires_at),
                'user_has_voted' => PollVote::where('poll_id', $poll->id)
                    ->where('user_id', $user->id)
                    ->exists(),
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
     * Get poll results
     */
    public function results(Post $post)
    {
        if ($post->type !== 'poll' || !$post->poll) {
            return response()->json([
                'success' => false,
                'message' => 'This post is not a poll'
            ], 400);
        }

        $poll = $post->poll;
        $poll->load('options');

        $totalVotes = $poll->options->sum('votes_count');

        return response()->json([
            'success' => true,
            'poll' => [
                'id' => $poll->id,
                'question' => $poll->question,
                'duration_days' => $poll->duration_days,
                'allow_multiple' => $poll->allow_multiple,
                'expires_at' => $poll->expires_at,
                'is_expired' => now()->greaterThan($poll->expires_at),
                'total_votes' => $totalVotes,
                'options' => $poll->options->map(function($option) use ($totalVotes) {
                    $percentage = $totalVotes > 0 
                        ? round(($option->votes_count / $totalVotes) * 100, 1) 
                        : 0;
                    
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text,
                        'votes_count' => $option->votes_count,
                        'percentage' => $percentage,
                    ];
                }),
            ]
        ]);
    }
}