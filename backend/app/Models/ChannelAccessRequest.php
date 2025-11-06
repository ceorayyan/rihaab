<?php
// app/Models/ChannelAccessRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelAccessRequest extends Model
{
    protected $fillable = [
        'channel_id',
        'user_id',
        'status',
        'message',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    public function channel()
    {
        return $this->belongsTo(CommunityChannel::class, 'channel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}