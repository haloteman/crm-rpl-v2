<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LupaPasswordController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/daftar', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/daftar', [CustomerAuthController::class, 'register']);
    Route::get('/masuk', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/masuk', [CustomerAuthController::class, 'login']);

    Route::get('/lupa-password', [LupaPasswordController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [LupaPasswordController::class, 'kirim'])->name('password.email');
    Route::get('/reset-password/{token}', [LupaPasswordController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [LupaPasswordController::class, 'update'])->name('password.store');
});

Route::middleware(['auth', 'wajib-ganti-password'])->group(function () {
    Route::view('/beranda', 'beranda')->name('beranda');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::middleware('role:Customer')->group(function () {
        Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
        Route::get('/profil/ubah', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::post('/profil/ubah', [ProfilController::class, 'update'])->name('profil.update');
    });

    Route::middleware('permission:service.manage')->group(function () {
        Route::get('/katalog-layanan', [KatalogController::class, 'index'])->name('katalog.index');
        Route::get('/katalog-layanan/tambah', [KatalogController::class, 'create'])->name('katalog.create');
        Route::post('/katalog-layanan', [KatalogController::class, 'store'])->name('katalog.store');
        Route::get('/katalog-layanan/{layanan}/ubah', [KatalogController::class, 'edit'])->name('katalog.edit');
        Route::post('/katalog-layanan/{layanan}/ubah', [KatalogController::class, 'update'])->name('katalog.update');
    });

    Route::middleware('permission:user.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:user.create')->name('users.store');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('permission:user.reset-password')->name('users.reset');
        Route::post('/users/{user}/hapus', [UserController::class, 'destroy'])->middleware('permission:user.delete')->name('users.destroy');
    });

    Route::middleware('permission:customer.view')->group(function () {
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
        Route::get('/pelanggan/{pelanggan}/ubah', [PelangganController::class, 'edit'])->middleware('permission:customer.manage')->name('pelanggan.edit');
        Route::post('/pelanggan/{pelanggan}/ubah', [PelangganController::class, 'update'])->middleware('permission:customer.manage')->name('pelanggan.update');
    });
});
