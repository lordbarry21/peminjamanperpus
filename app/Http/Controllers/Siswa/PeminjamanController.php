<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

/**
 * PeminjamanController (Controller Siswa untuk Transaksi Peminjaman & Pengembalian)
 * 
 * Menangani transaksi peminjaman buku oleh siswa, pengelolaan riwayat peminjaman,
 * validasi ketersediaan stok, dan pemrosesan pengembalian buku.
 */
class PeminjamanController extends Controller
{
    /**
     * [READ] Menampilkan Riwayat Peminjaman Buku Siswa
     * 
     * Mengambil seluruh data peminjaman buku milik siswa yang sedang login,
     * diurutkan dari transaksi terbaru, lalu dikirimkan ke view 'siswa.peminjaman.index'.
     */
    public function index()
    {
        $userId = auth()->id();

        // 1. Ambil Semua Riwayat Peminjaman Milik Siswa yang Login
        $riwayatPeminjaman = Peminjaman::with('buku')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        // 2. Render Halaman Riwayat Peminjaman
        return view('siswa.peminjaman.index', compact('riwayatPeminjaman'));
    }

    /**
     * [CREATE] Memproses Transaksi Peminjaman Buku Baru
     * 
     * Melakukan validasi input buku, memastikan stok tersedia, mencegah peminjaman
     * ganda untuk buku yang sama, menyimpan transaksi ke database, dan mengurangi stok buku.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Pengguna
        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
        ], [
            'buku_id.required' => 'Buku harus dipilih.',
            'buku_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $userId = auth()->id();

        // 2. Cek Ketersediaan Stok Buku
        if ($buku->stok <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku "' . $buku->judul . '" sedang habis.');
        }

        // 3. Pencegahan Pinjam Ganda: Cek apakah siswa masih meminjam buku ini
        $sudahDipinjam = Peminjaman::where('user_id', $userId)
            ->where('buku_id', $buku->id)
            ->where('status', 'dipinjam')
            ->exists();

        if ($sudahDipinjam) {
            return redirect()->back()->with('error', 'Anda masih meminjam buku "' . $buku->judul . '". Silakan kembalikan terlebih dahulu sebelum meminjam kembali.');
        }

        // 4. Simpan Transaksi Peminjaman ke Database
        Peminjaman::create([
            'user_id' => $userId,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => null,
            'status' => 'dipinjam',
        ]);

        // 5. Kurangi Stok Buku (Stok berkurang 1)
        $buku->decrement('stok');

        // 6. Redirect dengan Pesan Sukses
        return redirect()->route('siswa.peminjaman.index')->with('success', 'Buku "' . $buku->judul . '" berhasil dipinjam! Selamat membaca.');
    }

    /**
     * [UPDATE] Memproses Pengembalian Buku oleh Siswa
     * 
     * Memverifikasi hak kepemilikan transaksi, memperbarui status menjadi 'dikembalikan',
     * mencatat tanggal pengembalian, dan menambahkan kembali stok buku ke perpustakaan.
     */
    public function kembalikan(Peminjaman $peminjaman)
    {
        // 1. Keamanan Otorisasi: Pastikan transaksi peminjaman adalah milik siswa yang sedang login
        if ($peminjaman->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak untuk mengembalikan buku ini.');
        }

        // 2. Validasi Status: Pastikan buku memang sedang berstatus 'dipinjam'
        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->back()->with('error', 'Buku ini sudah pernah dikembalikan sebelumnya.');
        }

        // 3. Perbarui Status Peminjaman dan Catat Tanggal Kembali
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now()->toDateString(),
        ]);

        // 4. Kembalikan Stok Buku (Stok bertambah 1)
        $peminjaman->buku->increment('stok');

        // 5. Redirect dengan Pesan Sukses
        return redirect()->back()->with('success', 'Buku "' . $peminjaman->buku->judul . '" berhasil dikembalikan. Terima kasih!');
    }
}
