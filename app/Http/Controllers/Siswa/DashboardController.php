<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

/**
 * DashboardController (Controller Siswa untuk Halaman Beranda Siswa)
 * 
 * Menangani tampilan dashboard siswa, menyajikan ringkasan statistik peminjaman,
 * dan menampilkan daftar buku yang sedang aktif dipinjam oleh siswa saat ini.
 */
class DashboardController extends Controller
{
    /**
     * [READ] Menampilkan Halaman Dashboard Siswa
     * 
     * 1. Memeriksa peran pengguna: jika admin, diarahkan ke dashboard admin.
     * 2. Mengambil data statistik peminjaman milik siswa yang sedang login.
     * 3. Mengambil daftar peminjaman buku yang berstatus 'dipinjam' (aktif).
     * 4. Mengirimkan seluruh data tersebut ke view 'dashboard'.
     */
    public function index()
    {
        // 1. Keamanan: Jika yang login adalah Administrator, alihkan ke dashboard admin
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $userId = auth()->id();

        // 2. Menghitung Statistik Peminjaman Siswa
        $bukuDipinjamCount = Peminjaman::where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->count();

        $bukuDikembalikanCount = Peminjaman::where('user_id', $userId)
            ->where('status', 'dikembalikan')
            ->count();

        $totalKoleksiBuku = Buku::count();

        // 3. Mengambil Daftar Buku yang Sedang Dipinjam (Peminjaman Aktif)
        // Menggunakan eager loading with('buku') untuk efisiensi query database
        $peminjamanAktif = Peminjaman::with('buku')
            ->where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        // 4. Render View Dashboard Siswa dengan Data Terkait
        return view('dashboard', compact(
            'bukuDipinjamCount',
            'bukuDikembalikanCount',
            'totalKoleksiBuku',
            'peminjamanAktif'
        ));
    }
}
