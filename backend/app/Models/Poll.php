<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'question',
        'duration_days',
        'allow_multiple',
        'expires_at',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the post that owns the poll
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get all options for this poll
     */
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Get all votes for this poll
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Check if the poll has expired
     */
    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Get total votes for this poll
     */
    public function getTotalVotesAttribute()
    {
        return $this->options->sum('votes_count');
    }

    /**
     * Check if a specific user has voted
     */
    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get the vote(s) of a specific user
     */
    public function getUserVotes($userId)
    {
        return $this->votes()->where('user_id', $userId)->get();
    }
}