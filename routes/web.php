<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * Web Routes (Jalur URL Web Aplikasi)
 * --------------------------------------------------------------------------
 * File ini mendefinisikan rute URL yang dapat diakses pengguna di browser
 * beserta controller atau view yang akan meresponsnya.
 */

// 1. Halaman Utama / Landing Page (Dapat diakses oleh siapa saja / Publik)
Route::get('/', function () {
    return view('welcome');
});

// 2. Dashboard Siswa / Anggota
// Middleware 'auth': Hanya pengguna yang sudah login yang diizinkan masuk
// Middleware 'verified': Pengguna harus sudah verifikasi email jika diaktifkan
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Grup Rute Khusus Administrator
// Mengelompokkan route yang membutuhkan login dan hak akses admin
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Dashboard Admin
    Route::get('/admin/dashboard', function () {
        // Pengecekan Keamanan: Jika role bukan 'admin', hentikan dengan error 403 (Akses Ditolak)
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Route Resource CRUD Buku:
    // Otomatis membuat 7 rute RESTful (index, create, store, show, edit, update, destroy)
    // dengan prefix nama 'admin.buku.*'
    Route::resource('/admin/buku', BukuController::class, ['as' => 'admin']);
});

// 4. Pengaturan Profil Pengguna (Bawaan Laravel Breeze)
// Memungkinkan pengguna mengedit nama, email, ganti kata sandi, dan menghapus akun
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');     // Tampilkan form edit profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Simpan perubahan profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Hapus akun
});

// 5. Muat Rute Autentikasi Tambahan (Login, Register, Logout, Reset Password)
require __DIR__.'/auth.php';
