<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

/**
 * BukuController (Controller Siswa untuk Katalog Buku Perpustakaan)
 * 
 * Menangani penelusuran katalog buku oleh siswa, termasuk fitur pencarian,
 * pengecekan ketersediaan stok, dan status peminjaman oleh siswa aktif.
 */
class BukuController extends Controller
{
    /**
     * [READ] Menampilkan Halaman Katalog Buku untuk Siswa
     * 
     * Mengambil daftar buku dari database dengan opsi pencarian (search keyword).
     * Juga memeriksa daftar ID buku yang sedang dipinjam oleh siswa aktif
     * agar tampilan tombol pada katalog dapat disesuaikan secara dinamis.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Query Data Buku dengan Filter Pencarian (jika ada input search)
        $bukus = Buku::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%{$search}%")
                         ->orWhere('pengarang', 'like', "%{$search}%")
                         ->orWhere('kode_buku', 'like', "%{$search}%");
        })->latest()->get();

        // 2. Ambil Daftar ID Buku yang Sedang Dipinjam oleh Siswa Ini
        // Digunakan di View untuk menandai buku mana yang sudah dipinjam dan belum dikembalikan
        $bukuDipinjamIds = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->pluck('buku_id')
            ->toArray();

        // 3. Render View Katalog Buku Siswa
        return view('siswa.buku.index', compact('bukus', 'search', 'bukuDipinjamIds'));
    }
}
