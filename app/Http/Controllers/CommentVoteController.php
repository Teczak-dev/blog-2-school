<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentVoteController extends Controller
{
    public function vote(Request $request, Comment $comment)
    {
        $request->validate([
            'vote_type' => ['required', 'in:like,dislike'],
        ]);

        $userId = Auth::id();
        $voteType = $request->input('vote_type');

        $existingVote = $comment->getUserVote($userId);

        if ($existingVote) {
            if ($existingVote->vote_type === $voteType) {
                // Remove vote if same type clicked again (toggle off)
                if ($voteType === CommentVote::VOTE_LIKE) {
                    $comment->decrementLikes();
                } else {
                    $comment->decrementDislikes();
                }
                $existingVote->delete();
            } else {
                // Change vote type
                if ($existingVote->vote_type === CommentVote::VOTE_LIKE) {
                    $comment->decrementLikes();
                    $comment->incrementDislikes();
                } else {
                    $comment->decrementDislikes();
                    $comment->incrementLikes();
                }
                $existingVote->update(['vote_type' => $voteType]);
            }
        } else {
            // Create new vote
            CommentVote::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'vote_type' => $voteType,
            ]);

            if ($voteType === CommentVote::VOTE_LIKE) {
                $comment->incrementLikes();
            } else {
                $comment->incrementDislikes();
            }
        }

        $comment->refresh();

        return response()->json([
            'success' => true,
            'likes_count' => $comment->likes_count,
            'dislikes_count' => $comment->dislikes_count,
            'user_vote' => $comment->getUserVote($userId)?->vote_type,
        ]);
    }
}
