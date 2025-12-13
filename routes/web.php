<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::get('/hello/{name}', function ($name) {
    return view('hello', ['name' => $name]);
});

Route::get('/profile/{id}', function ($id) {
    return "Profil utilisateur ID : " . $id;
})->name('user.profile');

Route::get('/user/profile', [UserController::class, 'index']);

Route::resource('posts', PostController::class);

// Comment routes
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
