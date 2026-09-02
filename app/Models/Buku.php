<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Buku
 * 
 * Merepresentasikan tabel 'bukus' di database MySQL dan menyediakan
 * antarmuka ORM Eloquent untuk memanipulasi data buku.
 */
class Buku extends Model
{
    use HasFactory;

    /**
     * $guarded = ['id']:
     * Melindungi kolom 'id' agar tidak bisa diubah langsung via mass assignment.
     * Semua kolom lain (kode_buku, judul, pengarang, penerbit, stok) otomatis diizinkan.
     */
    protected $guarded = ['id'];

    /**
     * Relasi One-to-Many (Satu ke Banyak):
     * 1 Buku dapat tercatat dalam banyak transaksi peminjaman (Peminjaman).
     */
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
