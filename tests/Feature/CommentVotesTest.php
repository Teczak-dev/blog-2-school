<?php

use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticated user can like a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'likes_count' => 0,
    ]);

    $response = $this->actingAs($user)->postJson("/comments/{$comment->id}/vote", [
        'vote_type' => 'like',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'likes_count' => 1,
        'dislikes_count' => 0,
        'user_vote' => 'like',
    ]);

    expect(CommentVote::count())->toBe(1);
    $vote = CommentVote::first();
    expect($vote->comment_id)->toBe($comment->id);
    expect($vote->user_id)->toBe($user->id);
    expect($vote->vote_type)->toBe('like');
    
    $comment->refresh();
    expect($comment->likes_count)->toBe(1);
});

it('authenticated user can dislike a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'dislikes_count' => 0,
    ]);

    $response = $this->actingAs($user)->postJson("/comments/{$comment->id}/vote", [
        'vote_type' => 'dislike',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'likes_count' => 0,
        'dislikes_count' => 1,
        'user_vote' => 'dislike',
    ]);

    $comment->refresh();
    expect($comment->dislikes_count)->toBe(1);
});

it('user can change vote from like to dislike', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'likes_count' => 1,
        'dislikes_count' => 0,
    ]);
    
    CommentVote::create([
        'comment_id' => $comment->id,
        'user_id' => $user->id,
        'vote_type' => 'like',
    ]);

    $response = $this->actingAs($user)->postJson("/comments/{$comment->id}/vote", [
        'vote_type' => 'dislike',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'likes_count' => 0,
        'dislikes_count' => 1,
        'user_vote' => 'dislike',
    ]);
    
    expect(CommentVote::count())->toBe(1);
    $vote = CommentVote::first();
    expect($vote->vote_type)->toBe('dislike');
});

it('user can toggle off a like', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'likes_count' => 1,
    ]);
    
    CommentVote::create([
        'comment_id' => $comment->id,
        'user_id' => $user->id,
        'vote_type' => 'like',
    ]);

    $response = $this->actingAs($user)->postJson("/comments/{$comment->id}/vote", [
        'vote_type' => 'like',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'likes_count' => 0,
        'dislikes_count' => 0,
        'user_vote' => null,
    ]);
    
    expect(CommentVote::count())->toBe(0);
});

it('unauthenticated user cannot vote', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create(['is_approved' => true]);

    $response = $this->postJson("/comments/{$comment->id}/vote", [
        'vote_type' => 'like',
    ]);

    $response->assertStatus(401);
    expect(CommentVote::count())->toBe(0);
});

it('vote counts are correctly updated', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'likes_count' => 0,
        'dislikes_count' => 0,
    ]);

    // User 1 likes
    $this->actingAs($user1)->postJson("/comments/{$comment->id}/vote", ['vote_type' => 'like']);
    $comment->refresh();
    expect($comment->likes_count)->toBe(1);
    expect($comment->dislikes_count)->toBe(0);

    // User 2 likes
    $this->actingAs($user2)->postJson("/comments/{$comment->id}/vote", ['vote_type' => 'like']);
    $comment->refresh();
    expect($comment->likes_count)->toBe(2);
    expect($comment->dislikes_count)->toBe(0);

    // User 3 dislikes
    $this->actingAs($user3)->postJson("/comments/{$comment->id}/vote", ['vote_type' => 'dislike']);
    $comment->refresh();
    expect($comment->likes_count)->toBe(2);
    expect($comment->dislikes_count)->toBe(1);
    
    // User 2 changes to dislike
    $this->actingAs($user2)->postJson("/comments/{$comment->id}/vote", ['vote_type' => 'dislike']);
    $comment->refresh();
    expect($comment->likes_count)->toBe(1);
    expect($comment->dislikes_count)->toBe(2);
});
