<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_badge',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_badge', 'badge');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $badge)
    {
        return $query->where('user_badge', $badge);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function markAsUnread()
    {
        $this->update(['is_read' => false]);
    }

    public function timeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getTypeClass(): string
    {
        return match($this->type) {
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            default => 'text-info'
        };
    }
}
