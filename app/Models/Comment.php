<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'parent_id',
        'user_id',
        'author_name', 
        'author_email',
        'content',
        'is_approved',
        'approved_by',
        'approved_at',
        'likes_count',
        'dislikes_count',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function approvedReplies(): HasMany
    {
        return $this->replies()->where('is_approved', true);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CommentVote::class);
    }

    // Scope for approved comments
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    // Scope for pending comments (guests only)
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false)
                    ->whereNull('user_id');
    }

    // Scope for top-level comments only (not replies)
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // Check if comment is from logged user (auto-approved)
    public function isFromLoggedUser(): bool
    {
        return !is_null($this->user_id);
    }

    // Get display name for comment author
    public function getAuthorDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->author_name;
    }

    // Get author email for display
    public function getAuthorDisplayEmailAttribute(): string
    {
        return $this->user ? $this->user->email : $this->author_email;
    }

    public function getUserVote(?int $userId): ?CommentVote
    {
        if (!$userId) {
            return null;
        }

        return $this->votes()->where('user_id', $userId)->first();
    }

    public function canReply(): bool
    {
        // Only allow replies to top-level comments (max 1 level deep)
        return is_null($this->parent_id);
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

    public function incrementDislikes(): void
    {
        $this->increment('dislikes_count');
    }

    public function decrementDislikes(): void
    {
        $this->decrement('dislikes_count');
    }
}
