<?php
// app/Models/CommunityPost.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'channel_id',
        'user_id',
        'content',
        'media_path',
        'media_type',
        'is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean'
    ];

    public function channel()
    {
        return $this->belongsTo(CommunityChannel::class, 'channel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(CommunityPostReaction::class, 'post_id');
    }

    public function canEdit($userId)
    {
        // Post author can edit their own post
        if ($this->user_id === $userId) {
            return true;
        }
        
        // Channel moderators can edit
        if ($this->channel->canModerate($userId)) {
            return true;
        }

        return false;
    }

    public function canDelete($userId)
    {
        return $this->canEdit($userId);
    }
}