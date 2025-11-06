<?php
// app/Models/CommunityChannel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommunityChannel extends Model
{
    protected $fillable = [
        'community_id',
        'name',
        'slug',
        'description',
        'type',
        'is_private',
        'position'
    ];

    protected $casts = [
        'is_private' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($channel) {
            if (empty($channel->slug)) {
                $channel->slug = Str::slug($channel->name);
            }
        });
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function posts()
    {
        return $this->hasMany(CommunityPost::class, 'channel_id');
    }

    public function moderators()
    {
        return $this->hasMany(ChannelModerator::class, 'channel_id');
    }

    public function moderatorUsers()
    {
        return $this->belongsToMany(User::class, 'channel_moderators', 'channel_id', 'user_id')
            ->withTimestamps();
    }

    public function accessRequests()
    {
        return $this->hasMany(ChannelAccessRequest::class, 'channel_id');
    }

    public function pendingAccessRequests()
    {
        return $this->hasMany(ChannelAccessRequest::class, 'channel_id')
            ->where('status', 'pending');
    }

    // Check if user has access to this channel
    public function hasAccess($userId)
    {
        // Must be an approved community member first
        if (!$this->community->isMember($userId)) {
            return false;
        }

        // Community admins always have access
        if ($this->community->isAdmin($userId)) {
            return true;
        }

        // Channel moderators always have access
        if ($this->isChannelModerator($userId)) {
            return true;
        }

        // For PUBLIC communities - all approved members have access to all channels
        if (!$this->community->is_private) {
            return true;
        }

        // For PRIVATE communities:
        // Announcement channel is default - all approved members have access
        if ($this->type === 'announcement') {
            return true;
        }

        // For private channels - check if user has approved access
        if ($this->is_private) {
            return ChannelAccessRequest::where('channel_id', $this->id)
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->exists();
        }

        // Non-private channels in private communities - all approved members have access
        return true;
    }

    // Check if user has pending access request
    public function hasPendingAccessRequest($userId)
    {
        return ChannelAccessRequest::where('channel_id', $this->id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();
    }

    // Check if user can view this channel
    public function canView($userId)
    {
        return $this->hasAccess($userId);
    }

    // Check if user can post in this channel
    public function canPost($userId)
    {
        // Must have access first
        if (!$this->hasAccess($userId)) {
            return false;
        }

        // Announcement channels - only community admins OR channel moderators can post
        if ($this->type === 'announcement') {
            return $this->community->isAdmin($userId) || $this->isChannelModerator($userId);
        }
        
        // Restricted channels - only community admins, community moderators, OR channel moderators
        if ($this->type === 'restricted') {
            return $this->community->isModerator($userId) || $this->isChannelModerator($userId);
        }
        
        // General channels - all members with access can post
        return true;
    }

    // Check if user can moderate posts in this channel
    public function canModerate($userId)
    {
        return $this->community->isAdmin($userId) || 
               $this->community->isModerator($userId) || 
               $this->isChannelModerator($userId);
    }

    // Check if user is a channel-specific moderator
    public function isChannelModerator($userId)
    {
        return $this->moderators()->where('user_id', $userId)->exists();
    }

    // Check if user can edit channel settings
    public function canEdit($userId)
    {
        return $this->community->isAdmin($userId);
    }

    // Check if user can request access
    public function canRequestAccess($userId)
    {
        // Must be a community member
        if (!$this->community->isMember($userId)) {
            return false;
        }

        // Must be a private channel
        if (!$this->is_private) {
            return false;
        }

        // Community must be private
        if (!$this->community->is_private) {
            return false;
        }

        // Don't show for announcement channel (automatic access)
        if ($this->type === 'announcement') {
            return false;
        }

        // Already has access
        if ($this->hasAccess($userId)) {
            return false;
        }

        // Already has pending request
        if ($this->hasPendingAccessRequest($userId)) {
            return false;
        }

        return true;
    }
}