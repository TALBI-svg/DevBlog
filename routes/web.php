<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/hello/{name}', function ($name) {
    return view('hello', ['name' => $name]);
});

// Public Profile Route
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');

Route::get('/user/profile', [UserController::class, 'index']);

// Public Post Routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// Protected Post Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return redirect()->route('users.show', auth()->user());
    })->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    
    // Comment Routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Public Post Show Route (Must be after specific routes)
Route::get('/posts/{post}/likes', [PostController::class, 'likes'])->name('posts.likes');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
