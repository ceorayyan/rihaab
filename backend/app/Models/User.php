<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; // ADD THIS

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // ADD HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'profile_picture',
        'bio',
        'dob',
        'marital_status',
        'education',
        'occupation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ADD THIS: Append computed attributes to JSON responses
    protected $appends = [
        'profile_picture_url',
        'key_friends_count',
        'pending_key_requests_count'
       
    ];

    // ============================================
    // RELATIONSHIPS (Keep your existing ones)
    // ============================================
    
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'user_id');
    }

   
    public function keyRequests(): HasMany
    {
        return $this->hasMany(KeyRequest::class, 'sender_id');
    }

    public function receivedKeyRequests(): HasMany
    {
        return $this->hasMany(KeyRequest::class, 'receiver_id');
    }

    public function storyViews()
    {
        return $this->hasMany(StoryView::class, 'viewer_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ============================================
    // ACCESSOR METHODS (Keep and modify)
    // ============================================

    // MODIFY THIS: Make it work with both public and storage paths
    public function getProfilePictureUrlAttribute(): ?string
    {
        if ($this->profile_picture) {
            // Check if it's already a full URL
            if (str_starts_with($this->profile_picture, 'http')) {
                return $this->profile_picture;
            }
            
            // Check if it's in public folder (old format: userprofilepicture/...)
            if (str_starts_with($this->profile_picture, 'userprofilepicture/')) {
                return asset($this->profile_picture);
            }
            
            // Otherwise, it's in storage (new format: profile_pictures/...)
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // Keep all your existing helper methods...
    public function getRouteKeyName()
    {
        return 'username';
    }



   
    public function getKeyFriendsAttribute()
    {
        $sentRequests = $this->keyRequests()
            ->where('status', 'accepted')
            ->pluck('receiver_id');
            
        $receivedRequests = $this->receivedKeyRequests()
            ->where('status', 'accepted')
            ->pluck('sender_id');
            
        return $sentRequests->merge($receivedRequests)->unique();
    }

    public function isKeyFriendWith(User $user): bool
    {
        return $this->keyRequests()
            ->where('receiver_id', $user->id)
            ->where('status', 'accepted')
            ->exists() 
            || 
            $this->receivedKeyRequests()
            ->where('sender_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function hasPendingKeyRequestFrom(User $user): bool
    {
        return $this->receivedKeyRequests()
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasSentKeyRequestTo(User $user): bool
    {
        return $this->keyRequests()
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function sendKeyRequestTo(User $user)
    {
        $existingRequest = KeyRequest::where(function ($query) use ($user) {
            $query->where('sender_id', $this->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $this->id);
        })->first();

        if (!$existingRequest) {
            return KeyRequest::create([
                'sender_id' => $this->id,
                'receiver_id' => $user->id,
                'status' => 'pending'
            ]);
        }

        return $existingRequest;
    }

    public function acceptKeyRequestFrom(User $user)
    {
        $request = $this->receivedKeyRequests()
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($request) {
            $request->update(['status' => 'accepted']);
            return $request;
        }

        return null;
    }

    public function declineKeyRequestFrom(User $user)
    {
        $request = $this->receivedKeyRequests()
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($request) {
            $request->update(['status' => 'rejected']);
            return $request;
        }

        return null;
    }

    public function getPendingKeyRequestsCountAttribute(): int
    {
        return $this->receivedKeyRequests()
            ->where('status', 'pending')
            ->count();
    }

    public function getKeyFriendsCountAttribute(): int
    {
        return $this->key_friends->count();
    }

   
   
}