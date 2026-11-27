<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\KapasitasController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\LampiranController;
use App\Http\Controllers\LupaPasswordController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemantauanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\PenutupanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PesananMasukController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProyekSayaController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'beranda'])->name('website.beranda');
Route::get('/layanan', [WebsiteController::class, 'layanan'])->name('website.layanan');
Route::get('/layanan/{slug}', [WebsiteController::class, 'detailLayanan'])->name('website.layanan.show');
Route::get('/tentang-kami', [WebsiteController::class, 'tentangKami'])->name('website.tentang-kami');
Route::get('/kontak', [WebsiteController::class, 'kontak'])->name('website.kontak');

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
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::middleware('role:Customer')->group(function () {
        Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
        Route::get('/profil/ubah', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::post('/profil/ubah', [ProfilController::class, 'update'])->name('profil.update');
    });

    Route::middleware('permission:order.own')->group(function () {
        Route::get('/pesan/{slug}', [PesananController::class, 'create'])->name('pesanan.create');
        Route::post('/pesan/{slug}', [PesananController::class, 'store'])->name('pesanan.store');
        Route::get('/pesanan/{order}/terkirim', [PesananController::class, 'terkirim'])->name('pesanan.terkirim');
        Route::get('/pesanan-saya', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan-saya/{order}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::get('/pesanan-saya/{order}/tracking', [PesananController::class, 'tracking'])->name('pesanan.tracking');
        Route::post('/pesanan-saya/{order}/rating', [PesananController::class, 'rating'])->name('pesanan.rating');
    });

    Route::middleware('permission:order.view')->group(function () {
        Route::get('/pesanan-masuk', [PesananMasukController::class, 'index'])->name('pesanan-masuk.index');
        Route::get('/pesanan-masuk/{order}', [PesananMasukController::class, 'show'])->name('pesanan-masuk.show');
        Route::post('/pesanan-masuk/{order}/konsultasi', [KonsultasiController::class, 'simpan'])->middleware('permission:consult.note')->name('konsultasi.simpan');
        Route::post('/pesanan-masuk/{order}/harga', [HargaController::class, 'simpan'])->middleware('permission:price.set')->name('harga.simpan');
        Route::post('/pesanan-masuk/{order}/dp', [PembayaranController::class, 'simpanDp'])->middleware('permission:revenue.manage')->name('pembayaran.dp');
        Route::post('/pesanan-masuk/{order}/pelunasan', [PembayaranController::class, 'simpanPelunasan'])->middleware('permission:revenue.manage')->name('pembayaran.pelunasan');
        Route::post('/pesanan-masuk/{order}/batal', [PenutupanController::class, 'batal'])->middleware('permission:order.cancel')->name('pesanan-masuk.batal');
        Route::post('/pesanan-masuk/{order}/tolak', [PenutupanController::class, 'tolak'])->middleware('role:Owner')->name('pesanan-masuk.tolak');
    });

    Route::middleware('permission:revenue.view')->group(function () {
        Route::get('/riwayat-pendapatan', [PendapatanController::class, 'index'])->name('pendapatan.index');
        Route::get('/riwayat-pendapatan/proyek/{project}', [PendapatanController::class, 'proyek'])->name('pendapatan.proyek');
        Route::post('/riwayat-pendapatan/proyek/{project}/biaya', [BiayaController::class, 'simpan'])->middleware('permission:revenue.manage')->name('biaya.simpan');
        Route::post('/biaya-proyek/{biaya}/ubah', [BiayaController::class, 'ubah'])->middleware('permission:revenue.manage')->name('biaya.ubah');
        Route::post('/biaya-proyek/{biaya}/hapus', [BiayaController::class, 'hapus'])->middleware('permission:revenue.manage')->name('biaya.hapus');
    });

    Route::middleware('role:Owner')->group(function () {
        Route::get('/kapasitas-mingguan', [KapasitasController::class, 'index'])->name('kapasitas.index');
        Route::post('/kapasitas-mingguan', [KapasitasController::class, 'simpan'])->name('kapasitas.simpan');
    });

    Route::middleware('role:Owner|Kepala Staf Desainer')->group(function () {
        Route::get('/pemantauan-proyek', [PemantauanController::class, 'index'])->name('pemantauan.index');
        Route::get('/pemantauan-proyek/{project}', [PemantauanController::class, 'show'])->name('pemantauan.show');
    });

    Route::middleware('permission:project.assign')->group(function () {
        Route::get('/penugasan-proyek', [PenugasanController::class, 'index'])->name('penugasan.index');
        Route::post('/penugasan-proyek/{project}', [PenugasanController::class, 'simpan'])->name('penugasan.simpan');
        Route::post('/penugasan-proyek/{project}/pindah', [PenugasanController::class, 'pindah'])->name('penugasan.pindah');
    });

    Route::middleware('permission:order.assigned')->group(function () {
        Route::get('/proyek-saya', [ProyekSayaController::class, 'index'])->name('proyek-saya.index');
        Route::get('/proyek-saya/{project}', [ProyekSayaController::class, 'show'])->name('proyek-saya.show');
        Route::post('/proyek-saya/{project}/progres', [ProyekSayaController::class, 'progres'])->middleware('permission:progress.update')->name('proyek-saya.progres');
        Route::post('/proyek-saya/{project}/revisi', [ProyekSayaController::class, 'revisi'])->middleware('permission:progress.update')->name('proyek-saya.revisi');
        Route::post('/proyek-saya/{project}/selesai', [ProyekSayaController::class, 'selesai'])->middleware('permission:progress.update')->name('proyek-saya.selesai');
    });

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{chat}', [ChatController::class, 'kirim'])->name('chat.kirim');
    Route::get('/lampiran-chat/{lampiran}', [ChatController::class, 'unduh'])->name('chat.unduh');

    Route::get('/referensi/{referensi}', [ReferensiController::class, 'unduh'])->name('referensi.unduh');
    Route::get('/lampiran/{lampiran}', [LampiranController::class, 'unduh'])->name('lampiran.unduh');

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
