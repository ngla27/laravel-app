<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MetaController;
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
    Route::get('/editPost/{id}', [PostController::class, 'showEditPost'])->name('show.editPost'); // can see post but unable to submit edit
   
    Route::controller(PostController::class)
    ->group(function () {
        Route::post('/publishPost/{id}', 'publishPost')->middleware('role:editor,admin')->name('publishPost');
        Route::post('/unPublishPost/{id}', 'unPublishPost')->middleware('role:editor,admin')->name('unPublishPost');
        Route::get('/createPost', 'showCreatePost')->middleware('role:editor,author')->name('show.createPost');
        Route::post('/createPost', 'createPost')->middleware('role:editor,author')->name('createPost');
        Route::post('/editPost/{id}', 'editPost')->middleware('role:editor,author')->name('editPost');
    });

    Route::controller(AuthController::class)
    ->group(function () {
        Route::get('/showUsers', 'showUsers')->middleware('role:admin')->name('show.showUsers');
        Route::get('/createUser', 'showCreateUser')->middleware('role:admin')->name('show.createUser');
        Route::post('/createUser', 'createUser')->middleware('role:admin')->name('createUser');
    });

    Route::controller(MetaController::class)
    ->group(function () {
        Route::post('/generateMeta', 'generateMeta')->middleware('role:editor,author')->name('generateMeta');
    });

    Route::controller(ImageController::class)
    ->group(function () {
        Route::post('/uploadImage', 'uploadImage')->middleware('role:editor,author')->name('uploadImage');
    });
});
