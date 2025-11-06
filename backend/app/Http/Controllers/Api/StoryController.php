<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\KeyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /**
     * Get all stories from connected users
     */
    public function index()
    {
        $userId = auth()->id();
        $connectedUserIds = $this->getConnectedUserIds($userId);
        $connectedUserIds[] = $userId; // Include own stories

        $stories = Story::where('is_active', 1)
            ->where('expires_at', '>', now())
            ->whereIn('user_id', $connectedUserIds)
            ->with(['user', 'views'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userStories) use ($userId) {
                $firstStory = $userStories->first();
                return [
                    'user' => [
                        'id' => $firstStory->user->id,
                        'name' => $firstStory->user->name,
                        'username' => $firstStory->user->username,
                        'profile_picture' => $firstStory->user->profile_picture_url ?? null,
                    ],
                    'stories_count' => $userStories->count(),
                    'has_unseen' => $userStories->contains(function ($story) use ($userId) {
                        return !$story->hasBeenViewedBy($userId);
                    }),
                    'latest_story_time' => $firstStory->created_at->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $stories
        ]);
    }

    /**
     * Store a new story
     */
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:image,video,text',
            'caption' => 'nullable|string|max:500',
        ];

        if ($request->type === 'text') {
            $rules['content'] = 'required|string|max:5000';
        } else {
            $rules['file'] = 'required|file|max:10240';
            if ($request->type === 'image') {
                $rules['file'] .= '|mimes:jpeg,jpg,png,gif';
            } elseif ($request->type === 'video') {
                $rules['file'] .= '|mimes:mp4,mov,ogg,qt';
            }
        }

        $validated = $request->validate($rules);

        $content = '';

        if (in_array($request->type, ['image', 'video'])) {
            $file = $request->file('file');
            $content = $file->store('stories/' . $request->type . 's', 'public');
        } else {
            $content = $validated['content'];
        }

        $story = Story::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'content' => $content,
            'caption' => $validated['caption'] ?? null,
            'expires_at' => now()->addHours(24),
            'is_active' => true,
        ]);

        $story->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Story created successfully',
            'data' => $this->formatStory($story)
        ], 201);
    }

    /**
     * Get user's own stories
     */
    public function myStories()
    {
        $stories = Story::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDay())
            ->with(['views.viewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stories->map(function($story) {
                return array_merge($this->formatStory($story), [
                    'views_count' => $story->views->count(),
                    'viewers' => $story->views->map(function($view) {
                        return [
                            'user' => [
                                'id' => $view->viewer->id,
                                'name' => $view->viewer->name,
                                'username' => $view->viewer->username,
                                'profile_picture' => $view->viewer->profile_picture_url ?? null,
                            ],
                            'viewed_at' => $view->viewed_at->toISOString(),
                        ];
                    })
                ]);
            })
        ]);
    }

    /**
     * Get stories from a specific user
     */
    public function getUserStories($userId)
    {
        // Check if user can view stories
        if ($userId != auth()->id() && !in_array($userId, $this->getConnectedUserIds(auth()->id()))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view these stories'
            ], 403);
        }

        $stories = Story::where('is_active', 1)
            ->where('expires_at', '>', now())
            ->where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stories->map(function($story) {
                return array_merge($this->formatStory($story), [
                    'has_been_viewed' => $story->hasBeenViewedBy(auth()->id())
                ]);
            })
        ]);
    }

    /**
     * Get a specific story
     */
    public function show(Story $story)
    {
        if (!$this->canViewStory($story)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this story'
            ], 403);
        }

        $this->markStoryAsViewed($story);

        return response()->json([
            'success' => true,
            'data' => $this->formatStory($story)
        ]);
    }

    /**
     * Mark story as viewed
     */
    public function markAsViewed(Story $story)
    {
        if (!$this->canViewStory($story)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->markStoryAsViewed($story);

        return response()->json([
            'success' => true,
            'message' => 'Story marked as viewed'
        ]);
    }

    /**
     * Get viewers of a story
     */
    public function viewers(Story $story)
    {
        if ($story->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $viewers = $story->views()->with('viewer')->orderBy('viewed_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $viewers->map(function($view) {
                return [
                    'user' => [
                        'id' => $view->viewer->id,
                        'name' => $view->viewer->name,
                        'username' => $view->viewer->username,
                        'profile_picture' => $view->viewer->profile_picture_url ?? null,
                    ],
                    'viewed_at' => $view->viewed_at->toISOString(),
                    'viewed_human' => $view->viewed_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Delete a story
     */
    public function destroy(Story $story)
    {
        if ($story->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this story'
            ], 403);
        }

        // Delete file if exists
        if (in_array($story->type, ['image', 'video']) && $story->content) {
            Storage::disk('public')->delete($story->content);
        }

        $story->delete();

        return response()->json([
            'success' => true,
            'message' => 'Story deleted successfully'
        ]);
    }

    // Helper Methods
    private function getConnectedUserIds($userId)
    {
        return KeyRequest::where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)->where('status', 'accepted');
        })->orWhere(function ($query) use ($userId) {
            $query->where('receiver_id', $userId)->where('status', 'accepted');
        })->get()
        ->map(function ($keyRequest) use ($userId) {
            return $keyRequest->sender_id === $userId 
                ? $keyRequest->receiver_id 
                : $keyRequest->sender_id;
        })->unique()->toArray();
    }

    private function canViewStory($story)
    {
        $userId = auth()->id();
        if ($story->user_id === $userId) {
            return true;
        }
        return in_array($story->user_id, $this->getConnectedUserIds($userId));
    }

    private function markStoryAsViewed($story)
    {
        $userId = auth()->id();
        if ($story->user_id === $userId) {
            return;
        }

        $existingView = StoryView::where('story_id', $story->id)
            ->where('viewer_id', $userId)
            ->first();

        if (!$existingView) {
            StoryView::create([
                'story_id' => $story->id,
                'viewer_id' => $userId,
                'viewed_at' => now()
            ]);
        }
    }

    private function formatStory($story)
    {
        $contentUrl = null;
        if (in_array($story->type, ['image', 'video'])) {
            $contentUrl = asset('storage/' . $story->content);
        }

        return [
            'id' => $story->id,
            'type' => $story->type,
            'content' => $story->type === 'text' ? $story->content : null,
            'content_url' => $contentUrl,
            'caption' => $story->caption,
            'user' => [
                'id' => $story->user->id,
                'name' => $story->user->name,
                'username' => $story->user->username,
                'profile_picture' => $story->user->profile_picture_url ?? null,
            ],
            'created_at' => $story->created_at->toISOString(),
            'expires_at' => $story->expires_at->toISOString(),
            'is_active' => $story->is_active,
        ];
    }
}