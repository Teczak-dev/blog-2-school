<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        // Different validation for logged vs guest users
        if (Auth::check()) {
            $parameters = $request->validate([
                'content' => ['required', 'string', 'max:1000'],
            ]);
            
            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'content' => $parameters['content'],
                'is_approved' => true, // Logged users are auto-approved
            ]);

            $message = 'Komentarz został dodany pomyślnie!';
        } else {
            $parameters = $request->validate([
                'author_name' => ['required', 'string', 'max:255'],
                'author_email' => ['required', 'email', 'max:255'],
                'content' => ['required', 'string', 'max:1000'],
            ]);

            $comment = Comment::create([
                'post_id' => $post->id,
                'author_name' => $parameters['author_name'],
                'author_email' => $parameters['author_email'],
                'content' => $parameters['content'],
                'is_approved' => false, // Guests need approval
            ]);

            $message = 'Komentarz został wysłany i oczekuje na moderację. Zostanie opublikowany po zatwierdzeniu przez administratora.';
        }

        // If this is an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'comment' => [
                    'id' => $comment->id,
                    'author_name' => $comment->author_display_name,
                    'content' => $comment->content,
                    'is_from_logged_user' => $comment->isFromLoggedUser(),
                    'is_approved' => $comment->is_approved,
                    'created_at' => $comment->created_at->format('d.m.Y H:i'),
                ]
            ]);
        }

        return redirect()->route('posts.show', $id)
            ->with('success', $message)
            ->with('cache_buster', time());
    }

    public function loadMore(Request $request, string $postId)
    {
        $offset = $request->get('offset', 0);
        $limit = 5; // Load 5 more comments at a time
        
        $post = Post::findOrFail($postId);
        
        $comments = $post->approvedComments()
            ->skip($offset)
            ->take($limit)
            ->get();
            
        $hasMore = $post->approvedComments()->count() > ($offset + $limit);
        
        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'author_name' => $comment->author_display_name,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->format('d.m.Y H:i'),
                    'is_from_logged_user' => $comment->isFromLoggedUser(),
                ];
            }),
            'hasMore' => $hasMore,
        ]);
    }

    public function reply(Request $request, Comment $comment)
    {
        if (!$comment->canReply()) {
            return back()->with('error', 'Nie można odpowiadać na odpowiedzi. Odpowiedzi są możliwe tylko na komentarze główne.');
        }

        // Different validation for logged vs guest users
        if (Auth::check()) {
            $parameters = $request->validate([
                'content' => ['required', 'string', 'max:1000'],
            ]);
            
            $reply = Comment::create([
                'post_id' => $comment->post_id,
                'parent_id' => $comment->id,
                'user_id' => Auth::id(),
                'content' => $parameters['content'],
                'is_approved' => true, // Logged users are auto-approved
            ]);

            $message = 'Odpowiedź została dodana pomyślnie!';
        } else {
            $parameters = $request->validate([
                'author_name' => ['required', 'string', 'max:255'],
                'author_email' => ['required', 'email', 'max:255'],
                'content' => ['required', 'string', 'max:1000'],
            ]);

            $reply = Comment::create([
                'post_id' => $comment->post_id,
                'parent_id' => $comment->id,
                'author_name' => $parameters['author_name'],
                'author_email' => $parameters['author_email'],
                'content' => $parameters['content'],
                'is_approved' => false, // Guests need approval
            ]);

            $message = 'Odpowiedź została wysłana i oczekuje na moderację. Zostanie opublikowana po zatwierdzeniu przez administratora.';
        }

        // If this is an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'reply' => [
                    'id' => $reply->id,
                    'parent_id' => $reply->parent_id,
                    'author_name' => $reply->author_display_name,
                    'content' => $reply->content,
                    'is_from_logged_user' => $reply->isFromLoggedUser(),
                    'is_approved' => $reply->is_approved,
                    'created_at' => $reply->created_at->format('d.m.Y H:i'),
                ]
            ]);
        }

        return redirect()->route('posts.show', $comment->post_id)
            ->with('success', $message);
    }
}
