<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_text',
        'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    /**
     * Get the poll that owns this option
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get all votes for this option
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_option_id');
    }

    /**
     * Get the percentage of votes for this option
     * 
     * @return float
     */
    public function getPercentageAttribute()
    {
        $totalVotes = $this->poll->total_votes;
        
        if ($totalVotes === 0) {
            return 0;
        }
        
        return round(($this->votes_count / $totalVotes) * 100, 1);
    }

    /**
     * Check if a specific user voted for this option
     * 
     * @param int $userId
     * @return bool
     */
    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Increment the vote count
     * 
     * @return bool
     */
    public function incrementVote()
    {
        return $this->increment('votes_count');
    }

    /**
     * Decrement the vote count
     * 
     * @return bool
     */
    public function decrementVote()
    {
        if ($this->votes_count > 0) {
            return $this->decrement('votes_count');
        }
        
        return false;
    }
}