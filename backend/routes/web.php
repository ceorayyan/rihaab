<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\KeyRequestController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollVotesController;
use App\Http\Controllers\MessageController;
// Add these routes to your web.php file

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityChannelController;
use App\Http\Controllers\ChannelAccessController;

use App\Http\Controllers\CommunityPostController;

// Communities Routes
// Communities Routes
Route::middleware(['auth'])->group(function () {
    // Community Management
    Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/create', [CommunityController::class, 'create'])->name('communities.create');
    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.store');
    Route::get('/communities/{slug}/settings', [CommunityController::class, 'settings'])->name('communities.settings');
    Route::get('/communities/{slug}/members', [CommunityController::class, 'members'])->name('communities.members'); // NEW
    Route::put('/communities/{slug}', [CommunityController::class, 'update'])->name('communities.update');
    Route::delete('/communities/{slug}', [CommunityController::class, 'destroy'])->name('communities.destroy');
    Route::get('/communities/{slug}', [CommunityController::class, 'show'])->name('communities.show');
    Route::post('/communities/{slug}/join', [CommunityController::class, 'join'])->name('communities.join');
    Route::post('/communities/{slug}/leave', [CommunityController::class, 'leave'])->name('communities.leave');
    
    // Member Management (NEW)
    Route::post('/communities/{slug}/members/{member}/approve', [CommunityController::class, 'approveMember'])->name('communities.members.approve');
    Route::post('/communities/{slug}/members/{member}/reject', [CommunityController::class, 'rejectMember'])->name('communities.members.reject');
    Route::delete('/communities/{slug}/members/{member}', [CommunityController::class, 'removeMember'])->name('communities.members.remove');
    Route::put('/communities/{slug}/members/{member}/role', [CommunityController::class, 'updateMemberRole'])->name('communities.members.update-role');
    
    // Channel Management
    Route::get('/communities/{community}/channel/{channel}', [CommunityChannelController::class, 'show'])->name('communities.channel');
    Route::post('/communities/{community}/channels', [CommunityChannelController::class, 'store'])->name('communities.channels.store');
    Route::put('/communities/{community}/channels/{channel}', [CommunityChannelController::class, 'update'])->name('communities.channels.update');
    Route::delete('/communities/{community}/channels/{channel}', [CommunityChannelController::class, 'destroy'])->name('communities.channels.destroy');
    
    // Post Management
    Route::post('/communities/{community}/channels/{channel}/posts', [CommunityPostController::class, 'store'])->name('communities.posts.store');
    Route::put('/communities/posts/{post}', [CommunityPostController::class, 'update'])->name('communities.posts.update');
    Route::delete('/communities/posts/{post}', [CommunityPostController::class, 'destroy'])->name('communities.posts.destroy');
    Route::post('/communities/posts/{post}/toggle-pin', [CommunityPostController::class, 'togglePin'])->name('communities.posts.toggle-pin');

    // Channel Moderator Management (add after channel routes)
Route::get('/communities/{community}/channels/{channel}/moderators', [CommunityChannelController::class, 'moderators'])->name('communities.channels.moderators');
Route::post('/communities/{community}/channels/{channel}/moderators', [CommunityChannelController::class, 'addModerator'])->name('communities.channels.add-moderator');
Route::delete('/communities/{community}/channels/{channel}/moderators/{moderator}', [CommunityChannelController::class, 'removeModerator'])->name('communities.channels.remove-moderator');
// Channel Access Management
Route::post('/communities/{community}/channels/{channel}/request-access', [ChannelAccessController::class, 'requestAccess'])->name('communities.channels.request-access');
Route::get('/communities/{community}/channels/{channel}/access-requests', [ChannelAccessController::class, 'pendingRequests'])->name('communities.channels.access-requests');
Route::post('/communities/{community}/channels/{channel}/access-requests/{request}/approve', [ChannelAccessController::class, 'approveRequest'])->name('communities.channels.approve-access');
Route::post('/communities/{community}/channels/{channel}/access-requests/{request}/reject', [ChannelAccessController::class, 'rejectRequest'])->name('communities.channels.reject-access');
Route::delete('/communities/{community}/channels/{channel}/revoke-access/{user}', [ChannelAccessController::class, 'revokeAccess'])->name('communities.channels.revoke-access');
});
// ==========================================
// Public Routes (only welcome + auth routes)
// ==========================================
Route::get('/', function () {
    return redirect()->route('login'); // Redirect directly to login
});

// ==========================================
// Protected Routes (auth + verified required)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ========================
    // Profile Routes
    // ========================
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // My feed (only my profile posts)
    Route::get('/profile/feed/{id}', [ProfileController::class, 'feed'])->name('profile.feed');

    // Public user profiles (but still require login)
    Route::get('/users', [ProfileController::class, 'people'])->name('people');
    Route::get('/users/{user}', [ProfileController::class, 'publicProfile'])->name('profile.public');
    Route::get('/user/{user}/feed/{postId?}', [ProfileController::class, 'userFeed'])->name('user.feed');

    // ========================
    // Stories
    // ========================
    Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/create', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');
    Route::get('/my-stories', [StoryController::class, 'myStories'])->name('stories.my-stories');
    Route::get('/stories/{story}/viewers', [StoryController::class, 'viewers'])->name('stories.viewers');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    // API endpoints
    Route::get('/api/stories/user/{userId}', [StoryController::class, 'getUserStories']);
    Route::post('/api/stories/{story}/view', [StoryController::class, 'markAsViewed']);

    // ========================
    // Posts, Likes & Comments
    // ========================
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware(['auth', 'verified']);
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comment', [CommentController::class, 'store'])->name('posts.comment');

    // ========================
    // Key Requests
    // ========================
    Route::post('/key-request/send/{receiver}', [KeyRequestController::class, 'send'])->name('keyrequest.send');
    Route::post('/key-request/accept/{id}', [KeyRequestController::class, 'accept'])->name('keyrequest.accept');
    Route::post('/key-request/reject/{id}', [KeyRequestController::class, 'reject'])->name('keyrequest.reject');
    Route::get('/notifications/incoming', [KeyRequestController::class, 'incoming'])->name('keyrequest.incoming');
    Route::get('/my-keys', [KeyRequestController::class, 'myKeys'])->name('keys.my');

    // ========================
    // Messages (you can uncomment when ready)
    // ========================
    /*
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/new', [MessageController::class, 'getNewMessages'])->name('messages.new');
    Route::post('/messages/{user}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/api/users', [MessageController::class, 'getUsers'])->name('api.users');
    Route::get('/api/unread-count', [MessageController::class, 'getUnreadCount'])->name('api.unread-count');
    */
});

// ==========================================
// Authentication routes (login, register, etc.)
// ==========================================
require __DIR__.'/auth.php';
