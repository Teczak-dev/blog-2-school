<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CommentVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'vote_type',
    ];

    const VOTE_LIKE = 'like';
    const VOTE_DISLIKE = 'dislike';

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLikes(Builder $query): Builder
    {
        return $query->where('vote_type', self::VOTE_LIKE);
    }

    public function scopeDislikes(Builder $query): Builder
    {
        return $query->where('vote_type', self::VOTE_DISLIKE);
    }
}
