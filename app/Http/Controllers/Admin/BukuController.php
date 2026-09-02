<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;

/**
 * BukuController (Controller Admin untuk Manajemen Data Buku)
 * 
 * Menangani seluruh operasi CRUD (Create, Read, Update, Delete) data buku perpustakaan.
 */
class BukuController extends Controller
{
    /**
     * Penjaga Keamanan (Security Guard):
     * Memeriksa apakah pengguna saat ini sudah login dan memiliki role 'admin'.
     * Jika bukan admin, hentikan proses seketika dan tampilkan respon 403 (Akses Ditolak).
     */
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * [READ] Menampilkan Halaman Daftar / Katalog Buku
     * 
     * Mengambil semua data buku dari tabel 'bukus' menggunakan ORM Eloquent Buku::all(),
     * lalu mengirimkan data tersebut ke view 'admin.buku.index'.
     */
    public function index()
    {
        $this->checkAdmin();
        $bukus = Buku::all(); // Mengambil seluruh koleksi data buku
        return view('admin.buku.index', compact('bukus')); // compact('bukus') sama dengan ['bukus' => $bukus]
    }

    /**
     * [CREATE - Step 1] Menampilkan Form Tambah Buku Baru
     */
    public function create()
    {
        $this->checkAdmin();
        return view('admin.buku.create');
    }

    /**
     * [CREATE - Step 2] Menyimpan Data Buku Baru ke Database
     * 
     * Memvalidasi form input (tidak boleh kosong, kode_buku harus unik, stok angka)
     * lalu mengeksekusi query INSERT ke database.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        // 1. Validasi Input Pengguna
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku', // Wajib diisi & belum pernah dipakai di tabel bukus
            'judul' => 'required',                            // Wajib diisi
            'pengarang' => 'required',                        // Wajib diisi
            'penerbit' => 'required',                         // Wajib diisi
            'stok' => 'required|integer',                     // Wajib diisi dan harus berupa angka bulat
        ]);

        // 2. Simpan Data ke Database menggunakan Mass Assignment
        Buku::create($request->all());

        // 3. Redirect kembali ke halaman index dengan pesan notifikasi sukses (Flash Session)
        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * [UPDATE - Step 1] Menampilkan Form Edit Buku
     * 
     * Menggunakan Route Model Binding (Buku $buku): Laravel otomatis mencari data buku 
     * di database berdasarkan parameter {id} pada URL.
     */
    public function edit(Buku $buku)
    {
        $this->checkAdmin();
        return view('admin.buku.edit', compact('buku'));
    }

    /**
     * [UPDATE - Step 2] Menyimpan Perubahan Data Buku ke Database
     * 
     * Memvalidasi data input dan mengecualikan ID buku saat ini dari aturan unique kode_buku.
     */
    public function update(Request $request, Buku $buku)
    {
        $this->checkAdmin();

        // 1. Validasi Input (mengabaikan kode_buku miliknya sendiri saat validasi unique)
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku,' . $buku->id,
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'stok' => 'required|integer',
        ]);

        // 2. Eksekusi Perubahan Data (Query UPDATE)
        $buku->update($request->all());

        // 3. Redirect kembali ke tabel buku dengan pesan sukses
        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * [DELETE] Menghapus Data Buku dari Database
     * 
     * Menjalankan query DELETE FROM bukus WHERE id = :id.
     */
    public function destroy(Buku $buku)
    {
        $this->checkAdmin();

        // Eksekusi Hapus Data
        $buku->delete();

        // Redirect kembali ke tabel buku dengan pesan sukses
        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil dihapus.');
    }
}
