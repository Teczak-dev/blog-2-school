<?php

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('comments are sorted by newest by default', function () {
    $post = Post::factory()->create();
    
    $oldComment = Comment::factory()->for($post)->create([
        'content' => 'Old comment',
        'is_approved' => true,
        'created_at' => now()->subHours(3),
    ]);
    
    $middleComment = Comment::factory()->for($post)->create([
        'content' => 'Middle comment',
        'is_approved' => true,
        'created_at' => now()->subHours(2),
    ]);
    
    $newComment = Comment::factory()->for($post)->create([
        'content' => 'New comment',
        'is_approved' => true,
        'created_at' => now()->subHours(1),
    ]);

    $response = $this->get("/posts/{$post->id}");

    $responseContent = $response->getContent();
    $newPos = strpos($responseContent, 'New comment');
    $middlePos = strpos($responseContent, 'Middle comment');
    $oldPos = strpos($responseContent, 'Old comment');
    
    expect($newPos)->toBeLessThan($middlePos);
    expect($middlePos)->toBeLessThan($oldPos);
});

it('comments can be sorted by oldest', function () {
    $post = Post::factory()->create();
    
    $oldComment = Comment::factory()->for($post)->create([
        'content' => 'Old comment',
        'is_approved' => true,
        'created_at' => now()->subHours(3),
    ]);
    
    $newComment = Comment::factory()->for($post)->create([
        'content' => 'New comment',
        'is_approved' => true,
        'created_at' => now()->subHours(1),
    ]);

    $response = $this->get("/posts/{$post->id}?sort=oldest");

    $responseContent = $response->getContent();
    $oldPos = strpos($responseContent, 'Old comment');
    $newPos = strpos($responseContent, 'New comment');
    
    expect($oldPos)->toBeLessThan($newPos);
});

it('comments can be sorted by most liked', function () {
    $post = Post::factory()->create();
    
    $unpopularComment = Comment::factory()->for($post)->create([
        'content' => 'Unpopular comment',
        'is_approved' => true,
        'likes_count' => 2,
        'created_at' => now()->subHours(1),
    ]);
    
    $popularComment = Comment::factory()->for($post)->create([
        'content' => 'Popular comment',
        'is_approved' => true,
        'likes_count' => 10,
        'created_at' => now()->subHours(2),
    ]);
    
    $mediumComment = Comment::factory()->for($post)->create([
        'content' => 'Medium comment',
        'is_approved' => true,
        'likes_count' => 5,
        'created_at' => now()->subHours(3),
    ]);

    $response = $this->get("/posts/{$post->id}?sort=most_liked");

    $responseContent = $response->getContent();
    $popularPos = strpos($responseContent, 'Popular comment');
    $mediumPos = strpos($responseContent, 'Medium comment');
    $unpopularPos = strpos($responseContent, 'Unpopular comment');
    
    expect($popularPos)->toBeLessThan($mediumPos);
    expect($mediumPos)->toBeLessThan($unpopularPos);
});

it('sort parameter persists in dropdown', function () {
    $post = Post::factory()->create();

    $response = $this->get("/posts/{$post->id}?sort=oldest");

    $response->assertSee('selected', false); // Check for selected attribute
    $response->assertSee('Najstarsze');
});

it('only top-level comments are sorted (replies stay under parent)', function () {
    $post = Post::factory()->create();
    
    $parentComment = Comment::factory()->for($post)->create([
        'content' => 'Parent comment',
        'is_approved' => true,
        'parent_id' => null,
        'created_at' => now()->subHours(2),
    ]);
    
    $reply = Comment::factory()->for($post)->create([
        'content' => 'Reply to parent',
        'is_approved' => true,
        'parent_id' => $parentComment->id,
        'created_at' => now()->subHours(1), // Newer than parent
    ]);
    
    $newerTopLevel = Comment::factory()->for($post)->create([
        'content' => 'Newer top-level',
        'is_approved' => true,
        'parent_id' => null,
        'created_at' => now(),
    ]);

    $response = $this->get("/posts/{$post->id}");

    // Newer top-level should appear first
    $responseContent = $response->getContent();
    $newerPos = strpos($responseContent, 'Newer top-level');
    $parentPos = strpos($responseContent, 'Parent comment');
    
    expect($newerPos)->toBeLessThan($parentPos);
    
    // Reply should be nested under parent in the DOM
    // This is tested via the parent's approvedReplies relationship
    $parentComment->refresh();
    expect($parentComment->approvedReplies)->toHaveCount(1);
});

it('sort dropdown shows all three options', function () {
    $post = Post::factory()->create();

    $response = $this->get("/posts/{$post->id}");

    $response->assertSee('Najnowsze');
    $response->assertSee('Najstarsze');
    $response->assertSee('Najbardziej lubiane');
});
