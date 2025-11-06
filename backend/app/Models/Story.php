<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'content',
        'caption',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the user that owns the story
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the views for this story
     */
    public function views()
    {
        return $this->hasMany(StoryView::class);
    }

    /**
     * Scope to get only active stories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    /**
     * Check if the story is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get time remaining for the story
     */
    public function getTimeRemaining()
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $timeLeft = now()->diffInHours($this->expires_at);
        
        if ($timeLeft < 1) {
            $minutes = now()->diffInMinutes($this->expires_at);
            return $minutes . 'm';
        }
        
        return $timeLeft . 'h';
    }

    /**
     * Check if story has been viewed by a specific user
     */
    public function hasBeenViewedBy($userId)
    {
        return $this->views()->where('viewer_id', $userId)->exists();
    }

    /**
     * Get the count of viewers
     */
    public function getViewersCount()
    {
        return $this->views()->count();
    }
}