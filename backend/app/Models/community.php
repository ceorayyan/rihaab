<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'banner',
        'creator_id',
        'is_private'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(CommunityMember::class);
    }

    public function approvedMembers()
    {
        return $this->hasMany(CommunityMember::class)->where('status', 'approved');
    }

    public function pendingMembers()
    {
        return $this->hasMany(CommunityMember::class)->where('status', 'pending');
    }

    public function channels()
    {
        return $this->hasMany(CommunityChannel::class)->orderBy('position');
    }

    public function isMember($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    public function hasPendingRequest($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();
    }

    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
    }

    public function isModerator($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->whereIn('role', ['admin', 'moderator'])
            ->where('status', 'approved')
            ->exists();
    }

    public function getMemberRole($userId)
    {
        $member = $this->members()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->first();
        return $member ? $member->role : null;
    }

    public function admins()
    {
        return $this->members()
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->with('user');
    }
}