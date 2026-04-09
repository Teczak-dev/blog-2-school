<?php

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can display comments on post page', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create([
        'author_name' => 'Jan Kowalski',
        'content' => 'Test comment content',
        'is_approved' => true,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    $response->assertSee('Jan Kowalski');
    $response->assertSee('Test comment content');
    $response->assertSee('Komentarze (');
    $response->assertSee('id="comments-count-value">1<', false);
});

it('can add a comment to a post', function () {
    $post = Post::factory()->create();

    $response = $this->post("/posts/{$post->id}/comments", [
        'author_name' => 'Anna Nowak',
        'author_email' => 'anna@example.com',
        'content' => 'This is a great post!',
    ]);

    $response->assertRedirect("/posts/{$post->id}");
    $response->assertSessionHas('success');

    expect(Comment::count())->toBe(1);
    
    $comment = Comment::first();
    expect($comment->post_id)->toBe($post->id);
    expect($comment->author_name)->toBe('Anna Nowak');
    expect($comment->author_email)->toBe('anna@example.com');
    expect($comment->content)->toBe('This is a great post!');
});

it('validates comment form data', function ($field, $value, $expectedError) {
    $post = Post::factory()->create();

    $validData = [
        'author_name' => 'Valid Name',
        'author_email' => 'valid@example.com',
        'content' => 'Valid content',
    ];

    $invalidData = array_merge($validData, [$field => $value]);

    $response = $this->post("/posts/{$post->id}/comments", $invalidData);

    $response->assertSessionHasErrors($field);
})->with([
    'empty name' => ['author_name', '', 'required'],
    'too long name' => ['author_name', str_repeat('a', 256), 'max'],
    'empty email' => ['author_email', '', 'required'],
    'invalid email' => ['author_email', 'not-an-email', 'email'],
    'empty content' => ['content', '', 'required'],
    'too long content' => ['content', str_repeat('a', 1001), 'max'],
]);

it('displays empty state when no comments', function () {
    $post = Post::factory()->create();

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    $response->assertSee('Komentarze (');
    $response->assertSee('id="comments-count-value">0<', false);
    $response->assertSee('Brak komentarzy');
});

it('sorts comments by creation date descending', function () {
    $post = Post::factory()->create();
    
    $oldComment = Comment::factory()->for($post)->create([
        'author_name' => 'First Commenter',
        'created_at' => now()->subHours(2),
        'is_approved' => true,
    ]);
    
    $newComment = Comment::factory()->for($post)->create([
        'author_name' => 'Second Commenter', 
        'created_at' => now()->subHours(1),
        'is_approved' => true,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $responseContent = $response->getContent();
    $firstPos = strpos($responseContent, 'Second Commenter');
    $secondPos = strpos($responseContent, 'First Commenter');
    
    expect($firstPos)->toBeLessThan($secondPos);
});

it('preserves form data when validation fails', function () {
    $post = Post::factory()->create();

    $response = $this->post("/posts/{$post->id}/comments", [
        'author_name' => 'Valid Name',
        'author_email' => 'invalid-email',
        'content' => 'Valid content',
    ]);

    $response->assertSessionHasErrors('author_email');
    expect(old('author_name'))->toBe('Valid Name');
    expect(old('content'))->toBe('Valid content');
});

it('shows social share actions on post page', function () {
    $post = Post::factory()->create([
        'title' => 'Super post testowy',
    ]);

    $response = $this->get(route('posts.show', $post->id));

    $response->assertSuccessful();
    $response->assertSee('data-share-platform="facebook"', false);
    $response->assertSee('data-share-platform="x"', false);
    $response->assertSee('data-share-platform="linkedin"', false);
    $response->assertSee('data-share-platform="messenger"', false);
    $response->assertSee('data-share-platform="instagram"', false);
    $response->assertSee('data-share-platform="whatsapp"', false);
    $response->assertSee('data-share-url="' . route('posts.show', $post->id) . '"', false);
});
