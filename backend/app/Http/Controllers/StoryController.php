<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\KeyRequest;
use App\Models\User;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get users who have accepted key relationships with current user
        $connectedUserIds = $this->getConnectedUserIds($userId);
        
        // Get active stories from connected users (including own stories)
        $connectedUserIds[] = $userId; // Include own stories
        
        $stories = Story::active()
            ->whereIn('user_id', $connectedUserIds)
            ->with(['user', 'views'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');
$myActiveStories = auth()->user()->stories()
        ->where('created_at', '>=', now()->subDay())
        ->latest()
        ->get();
    
    return view('stories.index', compact('stories', 'myActiveStories'));
    //  return view('stories.index', compact('stories'));
    }

    public function create()
    {
        return view('stories.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:image,video,text',
            'caption' => 'nullable|string|max:500',
        ];

        // Add conditional validation based on type
        if ($request->type === 'text') {
            $rules['content'] = 'required|string|max:5000';
        } else {
            $rules['file'] = 'required|file|max:10240'; // 10MB max
            if ($request->type === 'image') {
                $rules['file'] = 'required|file|mimes:jpeg,jpg,png,gif|max:10240';
            } elseif ($request->type === 'video') {
                $rules['file'] = 'required|file|mimes:mp4,mov,ogg,qt|max:10240';
            }
        }

        $request->validate($rules);

        $content = '';
        
        if (in_array($request->type, ['image', 'video'])) {
            $file = $request->file('file');
            $path = $file->store('stories/' . $request->type . 's', 'public');
            $content = $path;
        } else {
            $content = $request->content;
        }

        Story::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'content' => $content,
            'caption' => $request->caption,
            'expires_at' => now()->addHours(24), // Stories expire after 24 hours
            'is_active' => true,
        ]);

        return redirect()->route('stories.index')->with('success', 'Story created successfully!');
    }

    public function show(Story $story)
    {
        // Check if user can view this story
        if (!$this->canViewStory($story)) {
            abort(403, 'You do not have permission to view this story.');
        }

        // Mark story as viewed
        $this->markStoryAsViewed($story);

        return view('stories.show', compact('story'));
    }

    public function myStories()
    {
        $stories = Story::where('user_id', auth()->id())
            ->with(['views.viewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stories.my-stories', compact('stories'));
    }

    public function viewers(Story $story)
    {
        // Check if user owns this story
        if ($story->user_id !== auth()->id()) {
            abort(403, 'You can only view statistics for your own stories.');
        }

        $viewers = $story->views()->with('viewer')->orderBy('viewed_at', 'desc')->get();

        return view('stories.viewers', compact('story', 'viewers'));
    }

    public function destroy(Story $story)
    {
        // Check if user owns this story
        if ($story->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own stories.');
        }

        // Delete the file if it exists
        if (in_array($story->type, ['image', 'video']) && $story->content) {
            Storage::disk('public')->delete($story->content);
        }

        $story->delete();

        return redirect()->back()->with('success', 'Story deleted successfully!');
    }

    // API Methods for AJAX requests
    public function getUserStories($userId)
    {
        // Check if user can view stories from this user
        if ($userId != auth()->id() && !in_array($userId, $this->getConnectedUserIds(auth()->id()))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stories = Story::active()
            ->where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'asc') // Order by oldest first for proper story sequence
            ->get()
            ->map(function ($story) {
                return [
                    'id' => $story->id,
                    'type' => $story->type,
                    'content' => $story->content,
                    'caption' => $story->caption,
                    'created_at' => $story->created_at->toISOString(),
                    'user' => [
                        'id' => $story->user->id,
                        'name' => $story->user->name,
                    ],
                    'has_been_viewed' => $story->hasBeenViewedBy(auth()->id())
                ];
            });

        return response()->json($stories);
    }

    public function markAsViewed(Story $story)
    {
        // Check if user can view this story
        if (!$this->canViewStory($story)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->markStoryAsViewed($story);

        return response()->json(['success' => true]);
    }

    // Helper Methods - UPDATED TO MATCH YOUR KEY_REQUESTS TABLE STRUCTURE
    private function getConnectedUserIds($userId)
    {
        return KeyRequest::where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('status', 'accepted');
        })->orWhere(function ($query) use ($userId) {
            $query->where('receiver_id', $userId)
                  ->where('status', 'accepted');
        })->get()
        ->map(function ($keyRequest) use ($userId) {
            return $keyRequest->sender_id === $userId 
                ? $keyRequest->receiver_id 
                : $keyRequest->sender_id;
        })->unique()->toArray();
    }

    private function canViewStory(Story $story)
    {
        $userId = auth()->id();
        
        // User can view their own stories
        if ($story->user_id === $userId) {
            return true;
        }
        
        // Check if users are connected
        $connectedUserIds = $this->getConnectedUserIds($userId);
        
        return in_array($story->user_id, $connectedUserIds);
    }

    private function markStoryAsViewed(Story $story)
    {
        $userId = auth()->id();
        
        // Don't mark own stories as viewed
        if ($story->user_id === $userId) {
            return;
        }
        
        // Check if already viewed
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
}
