<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
})->name('home');

// auth
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
  Route::get('/login', 'showLogin')->name('show.login');
  Route::post('/login', 'login')->name('login');
});

Route::middleware(['role:admin'])->controller(AuthController::class)->group(function () {
  Route::get('/createUser', [AuthController::class, 'showCreateUser'])->name('show.createUser');
  Route::post('/createUser', [AuthController::class, 'createUser'])->name('createUser');
});