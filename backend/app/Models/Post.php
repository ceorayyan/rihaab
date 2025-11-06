<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'media_path',
        'media_type',
        'type',
        'privacy',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all likes for the post
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get all comments for the post
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the poll associated with this post (if it's a poll post)
     */
    public function poll()
    {
        return $this->hasOne(Poll::class);
    }

    /**
     * Check if the post is a poll
     */
    public function isPoll()
    {
        return $this->type === 'poll';
    }

    /**
     * Check if the post is a Q&A
     */
    public function isQA()
    {
        return $this->type === 'qa';
    }

    /**
     * Check if the post is a standard post
     */
    public function isStandard()
    {
        return $this->type === 'standard';
    }

    /**
     * Check if the post is public
     */
    public function isPublic()
    {
        return $this->privacy === 'public';
    }

    /**
     * Check if the post is friends only
     */
    public function isFriendsOnly()
    {
        return $this->privacy === 'friends';
    }

    /**
     * Check if the post is private
     */
    public function isPrivate()
    {
        return $this->privacy === 'private';
    }

    /**
     * Scope a query to only include public posts
     */
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    /**
     * Scope a query to only include posts of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include poll posts
     */
    public function scopePolls($query)
    {
        return $query->where('type', 'poll');
    }

    /**
     * Scope a query to only include Q&A posts
     */
    public function scopeQA($query)
    {
        return $query->where('type', 'qa');
    }
}