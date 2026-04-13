<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- Existing Dashboard Routes ---
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// --- Requirement 4: Posts Module ---

/**
 * Public Routes (4-1 & 4-4)
 * index: Paginated list, excludes drafts/scheduled
 * show: Single post, returns 404 if draft/scheduled
 */
Route::get('posts', [PostController::class, 'index'])->name('posts.index');

// Auth-Protected Routes (4-2, 4-3, 4-5, 4-6, 4-7)
Route::middleware(['auth'])->group(function () {
    // 4-2. Show create form (Must be ABOVE {post} wildcard)
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');

    // 4-3. Store new post
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');

    // 4-5, 4-6, 4-7. Edit, Update, and Delete (Only author via PostPolicy)
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::patch('posts/{post}', [PostController::class, 'update']);
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

/**
 * 4-4. Public Single View
 * Moved to the bottom to ensure 'posts/create' is matched first.
 */
Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');

// --- Standard Auth & Settings ---
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
