<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/hello-world/{name}', [HelloController::class, 'index']);

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
});

Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/posts/{post}/comments/load-more', [CommentController::class, 'loadMore'])->name('comments.load-more');
Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');

Route::middleware('auth')->group(function () {
    Route::post('/comments/{comment}/vote', [\App\Http\Controllers\CommentVoteController::class, 'vote'])->name('comments.vote');
});

require __DIR__.'/auth.php';
