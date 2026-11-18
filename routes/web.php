<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth', 'wajib-ganti-password'])->group(function () {
    Route::view('/beranda', 'beranda')->name('beranda');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::middleware('permission:user.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:user.create')->name('users.store');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('permission:user.reset-password')->name('users.reset');
        Route::post('/users/{user}/hapus', [UserController::class, 'destroy'])->middleware('permission:user.delete')->name('users.destroy');
    });
});
