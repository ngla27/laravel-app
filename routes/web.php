<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{id}', [PostController::class, 'showPost'])->name('show.showPost');

Route::middleware('guest')
    ->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('show.login');
    Route::post('/login', 'login')->name('login');
});

Route::middleware('auth')
    ->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account', [AccountController::class, 'showAccountManagement'])->name('show.account');
    
    Route::middleware(['role:admin'])
        ->group(function () {
        Route::get('/createUser', [AuthController::class, 'showCreateUser'])->name('show.createUser');
        Route::post('/createUser', [AuthController::class, 'createUser'])->name('createUser');
    });

    Route::middleware(['role:editor,author'])
        ->controller(PostController::class)
        ->group(function () {
        Route::get('/createPost', 'showCreatePost')->name('show.createPost');
        Route::post('/createPost', 'createPost')->name('createPost');
        Route::get('/editPost/{id}', 'showEditPost')->name('show.editPost');
        Route::post('/editPost/{id}', 'editPost')->name('editPost');
    });

    Route::middleware(['role:admin,editor'])
        ->controller(PostController::class)
        ->group(function () {
        Route::post('/publishPost', 'publishPost')->name('publishPost');
    });
});
