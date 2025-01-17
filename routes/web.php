<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{id}', [PostController::class, 'showPost'])->name('show.showPost');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
  Route::get('/login', 'showLogin')->name('show.login');
  Route::post('/login', 'login')->name('login');
});

Route::middleware('auth')->controller(AuthController::class)->group(function () {
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
  Route::get('/account', function () { return view('accountManagement'); })->name('account');
  
  Route::middleware(['role:admin'])->controller(AuthController::class)->group(function () {
    Route::get('/createUser', [AuthController::class, 'showCreateUser'])->name('show.createUser');
    Route::post('/createUser', [AuthController::class, 'createUser'])->name('createUser');
  });

  Route::middleware(['role:editor,author'])->controller(AuthController::class)->group(function () {
    Route::get('/createPost', [AuthController::class, 'showCreatePost'])->name('show.createPost');
    Route::post('/createPost', [AuthController::class, 'createPost'])->name('createPost');
    Route::get('/editPost', [AuthController::class, 'showEditPost'])->name('show.editPost');
    Route::post('/editPost', [AuthController::class, 'editPost'])->name('editPost');
  });

  Route::middleware(['role:admin,editor'])->controller(AuthController::class)->group(function () {
    Route::post('/publishPost', [AuthController::class, 'publishPost'])->name('publishPost');
  });
});
