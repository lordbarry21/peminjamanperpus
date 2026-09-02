<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Peminjaman
 * 
 * Merepresentasikan tabel 'peminjamans' di database MySQL yang mencatat
 * riwayat transaksi peminjaman buku oleh anggota/siswa.
 */
class Peminjaman extends Model
{
    use HasFactory;

    // Nama tabel eksplisit di database
    protected $table = 'peminjamans';

    // Kolom 'id' dilindungi dari pengisian langsung
    protected $guarded = ['id'];

    /**
     * Relasi Many-to-One (Banyak ke Satu):
     * Setiap baris transaksi peminjaman dimiliki oleh 1 Pengguna (User / Siswa).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Many-to-One (Banyak ke Satu):
     * Setiap baris transaksi peminjaman terhubung ke 1 Buku tertentu.
     */
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
