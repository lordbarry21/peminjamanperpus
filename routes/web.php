<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\KatalogController;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Sesuai Modul 1, CRUD Buku, dan Siswa Katalog & Pinjam
*/

// Halaman Awal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard / Katalog Buku untuk Siswa (User)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [KatalogController::class, 'index'])->name('dashboard');
    Route::get('/katalog', [KatalogController::class, 'index'])->name('user.katalog');
    Route::post('/dashboard/pinjam', [KatalogController::class, 'store'])->name('user.pinjam');

    // Route untuk tombol aksi pengembalian mandiri siswa
    Route::patch('/dashboard/kembali/{id}', function ($id) {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'dikembalikan']);
        $peminjaman->buku->increment('stok');
        return back()->with('success', 'Buku berhasil dikembalikan.');
    })->name('user.kembali');
});

// Grup Route Administrator
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', function () {
        // Pastikan hanya admin yang bisa akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Route CRUD Buku
    Route::resource('/admin/buku', BukuController::class, ['as' => 'admin']);
});

// Route Profile bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
