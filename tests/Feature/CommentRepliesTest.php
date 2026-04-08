<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticated user can reply to a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'parent_id' => null,
    ]);

    $response = $this->actingAs($user)->post("/comments/{$parentComment->id}/reply", [
        'content' => 'This is a reply to the comment',
    ]);

    $response->assertRedirect(route('posts.show', $post->id));
    $response->assertSessionHas('success');

    expect(Comment::count())->toBe(2);
    
    $reply = Comment::where('parent_id', $parentComment->id)->first();
    expect($reply)->not->toBeNull();
    expect($reply->content)->toBe('This is a reply to the comment');
    expect($reply->parent_id)->toBe($parentComment->id);
    expect($reply->post_id)->toBe($post->id);
    expect($reply->user_id)->toBe($user->id);
    expect($reply->is_approved)->toBeTrue();
});

it('guest can reply to a comment with moderation', function () {
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'parent_id' => null,
    ]);

    $response = $this->post("/comments/{$parentComment->id}/reply", [
        'author_name' => 'Guest User',
        'author_email' => 'guest@example.com',
        'content' => 'This is a guest reply',
    ]);

    $response->assertRedirect(route('posts.show', $post->id));
    $response->assertSessionHas('success');

    $reply = Comment::where('parent_id', $parentComment->id)->first();
    expect($reply)->not->toBeNull();
    expect($reply->author_name)->toBe('Guest User');
    expect($reply->author_email)->toBe('guest@example.com');
    expect($reply->content)->toBe('This is a guest reply');
    expect($reply->is_approved)->toBeFalse();
});

it('replies are displayed as nested under parent comment', function () {
    $post = Post::factory()->create();
    $user = User::factory()->create();
    
    $parentComment = Comment::factory()->for($post)->create([
        'content' => 'Parent comment',
        'is_approved' => true,
        'parent_id' => null,
    ]);
    
    $reply = Comment::factory()->for($post)->for($user)->create([
        'content' => 'Reply to parent',
        'is_approved' => true,
        'parent_id' => $parentComment->id,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    $response->assertSee('Parent comment');
    $response->assertSee('Reply to parent');
    
    // Verify reply is associated with parent
    $parentComment->refresh();
    expect($parentComment->approvedReplies)->toHaveCount(1);
    expect($parentComment->approvedReplies->first()->id)->toBe($reply->id);
});

it('cannot reply to a reply (max 1 level deep)', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    
    $parentComment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'parent_id' => null,
    ]);
    
    $reply = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'parent_id' => $parentComment->id,
    ]);

    $response = $this->actingAs($user)->post("/comments/{$reply->id}/reply", [
        'content' => 'This should not work',
    ]);

    $response->assertSessionHas('error');
    
    // Should still only have 2 comments (parent + first reply)
    expect(Comment::count())->toBe(2);
});

it('reply validation works correctly', function ($field, $value, $expectedError) {
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->for($post)->create([
        'is_approved' => true,
        'parent_id' => null,
    ]);

    $validData = [
        'author_name' => 'Valid Name',
        'author_email' => 'valid@example.com',
        'content' => 'Valid reply content',
    ];

    $invalidData = array_merge($validData, [$field => $value]);

    $response = $this->post("/comments/{$parentComment->id}/reply", $invalidData);

    $response->assertSessionHasErrors($field);
})->with([
    'empty name' => ['author_name', '', 'required'],
    'empty email' => ['author_email', '', 'required'],
    'invalid email' => ['author_email', 'not-an-email', 'email'],
    'empty content' => ['content', '', 'required'],
    'too long content' => ['content', str_repeat('a', 1001), 'max'],
]);

it('canReply method works correctly', function () {
    $post = Post::factory()->create();
    
    $topLevelComment = Comment::factory()->for($post)->create([
        'parent_id' => null,
    ]);
    
    $replyComment = Comment::factory()->for($post)->create([
        'parent_id' => $topLevelComment->id,
    ]);
    
    expect($topLevelComment->canReply())->toBeTrue();
    expect($replyComment->canReply())->toBeFalse();
});
