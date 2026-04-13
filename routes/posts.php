<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// 1. Static Public routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// 2. Protected routes
Route::middleware(['auth'])->group(function () {
    // Specific paths (like /create) MUST come before wildcards (like /{id})
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::patch('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// 3. Public Wildcard routes (Move this to the end)
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
