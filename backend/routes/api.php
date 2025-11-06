<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\KeyRequestController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\CommunityChannelController;
use App\Http\Controllers\Api\ChannelAccessController;
use App\Http\Controllers\Api\CommunityPostController;

// ==========================================
// Public API Routes
// ==========================================

Route::get('/test', function () {
    return response()->json([
        'message' => 'Rihaab API is working!',
        'version' => app()->version(),
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// Protected Routes (Require auth:sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth Utilities
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => response()->json($request->user()));

    // ========================
    // Profile Routes
    // ========================
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile']); // GET /api/profile
        Route::get('/edit', [ProfileController::class, 'edit']); // GET /api/profile/edit
        Route::put('/update', [ProfileController::class, 'update']); // PUT /api/profile/update
        Route::delete('/', [ProfileController::class, 'destroy']); // DELETE /api/profile
        Route::get('/feed/{id}', [ProfileController::class, 'feed']); // GET /api/profile/feed/{id}
    });

    // Public profiles
    Route::get('/users', [ProfileController::class, 'people']); // GET /api/users
    Route::get('/users/{user}', [ProfileController::class, 'publicProfile']); // GET /api/users/{user}
    Route::get('/user/{user}/feed/{postId?}', [ProfileController::class, 'userFeed']); // GET /api/user/{id}/feed/{postId?}

    // ========================
    // Stories
    // ========================
    Route::prefix('stories')->group(function () {
        Route::get('/', [StoryController::class, 'index']);
        Route::post('/', [StoryController::class, 'store']);
        Route::get('/my-stories', [StoryController::class, 'myStories']);
        Route::get('/user/{userId}', [StoryController::class, 'getUserStories']);
        Route::get('/{story}', [StoryController::class, 'show']);
        Route::post('/{story}/view', [StoryController::class, 'markAsViewed']);
        Route::get('/{story}/viewers', [StoryController::class, 'viewers']);
        Route::delete('/{story}', [StoryController::class, 'destroy']);
    });

    // ========================
    // Posts, Likes & Comments
    // ========================
    Route::prefix('posts')->group(function () {
        // Main post routes
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{post}', [PostController::class, 'show']);
        Route::put('/{post}', [PostController::class, 'update']);
        Route::delete('/{post}', [PostController::class, 'destroy']);
        
        // Post interactions
        Route::post('/{post}/like', [LikeController::class, 'toggle']);
        Route::post('/{post}/comment', [CommentController::class, 'store']);
        
        // ⭐ POLL VOTING - CRITICAL ROUTES
        Route::post('/{post}/poll/vote', [PostController::class, 'votePoll']);
        Route::post('/{post}/poll/unvote', [PostController::class, 'unvotePoll']);
    });

    // ========================
    // Key Requests
    // ========================
    Route::prefix('key-requests')->group(function () {
        Route::post('/send/{receiver}', [KeyRequestController::class, 'send']);
        Route::post('/accept/{id}', [KeyRequestController::class, 'accept']);
        Route::post('/reject/{id}', [KeyRequestController::class, 'reject']);
        Route::get('/incoming', [KeyRequestController::class, 'incoming']);
        Route::get('/my-keys', [KeyRequestController::class, 'my Keys']);
    });

    // ========================
    // Communities
    // ========================
    Route::prefix('communities')->group(function () {
        // Basic community management
        Route::get('/', [CommunityController::class, 'index']);
        Route::post('/', [CommunityController::class, 'store']);
        Route::get('/{slug}', [CommunityController::class, 'show']);
        Route::put('/{slug}', [CommunityController::class, 'update']);
        Route::delete('/{slug}', [CommunityController::class, 'destroy']);

        // Membership
        Route::post('/{slug}/join', [CommunityController::class, 'join']);
        Route::post('/{slug}/leave', [CommunityController::class, 'leave']);
        Route::get('/{slug}/settings', [CommunityController::class, 'settings']);
        Route::get('/{slug}/members', [CommunityController::class, 'members']);
        Route::post('/{slug}/members/{member}/approve', [CommunityController::class, 'approveMember']);
        Route::post('/{slug}/members/{member}/reject', [CommunityController::class, 'rejectMember']);
        Route::delete('/{slug}/members/{member}', [CommunityController::class, 'removeMember']);
        Route::put('/{slug}/members/{member}/role', [CommunityController::class, 'updateMemberRole']);

        // Channels
        Route::get('/{community}/channels/{channel}', [CommunityChannelController::class, 'show']);
        Route::post('/{community}/channels', [CommunityChannelController::class, 'store']);
        Route::put('/{community}/channels/{channel}', [CommunityChannelController::class, 'update']);
        Route::delete('/{community}/channels/{channel}', [CommunityChannelController::class, 'destroy']);

        // Channel moderators
        Route::get('/{community}/channels/{channel}/moderators', [CommunityChannelController::class, 'moderators']);
        Route::post('/{community}/channels/{channel}/moderators', [CommunityChannelController::class, 'addModerator']);
        Route::delete('/{community}/channels/{channel}/moderators/{moderator}', [CommunityChannelController::class, 'removeModerator']);

        // Channel access
        Route::post('/{community}/channels/{channel}/request-access', [ChannelAccessController::class, 'requestAccess']);
        Route::get('/{community}/channels/{channel}/access-requests', [ChannelAccessController::class, 'pendingRequests']);
        Route::post('/{community}/channels/{channel}/access-requests/{request}/approve', [ChannelAccessController::class, 'approveRequest']);
        Route::post('/{community}/channels/{channel}/access-requests/{request}/reject', [ChannelAccessController::class, 'rejectRequest']);
        Route::delete('/{community}/channels/{channel}/revoke-access/{user}', [ChannelAccessController::class, 'revokeAccess']);

        // Community posts
        Route::post('/{community}/channels/{channel}/posts', [CommunityPostController::class, 'store']);
        Route::put('/posts/{post}', [CommunityPostController::class, 'update']);
        Route::delete('/posts/{post}', [CommunityPostController::class, 'destroy']);
        Route::post('/posts/{post}/toggle-pin', [CommunityPostController::class, 'togglePin']);
    });

    // ========================
    // Messages (optional)
    // ========================
    /*
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/{user}', [MessageController::class, 'show']);
        Route::post('/', [MessageController::class, 'store']);
        Route::get('/{user}/new', [MessageController::class, 'getNewMessages']);
        Route::post('/{user}/read', [MessageController::class, 'markAsRead']);
        Route::get('/users/list', [MessageController::class, 'getUsers']);
        Route::get('/unread/count', [MessageController::class, 'getUnreadCount']);
    });
    */
});