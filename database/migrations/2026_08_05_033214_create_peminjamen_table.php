<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat Tabel 'peminjamans' (Transaksi Peminjaman)
 * 
 * Menghubungkan siswa (users) dan buku (bukus) melalui Foreign Key (Kunci Tamu).
 */
return new class extends Migration
{
    /**
     * Method up():
     * Membuat struktur tabel peminjaman di database
     */
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id(); // Primary Key
            
            // Foreign Key ke tabel 'users': Jika data user dihapus, transaksi peminjamannya ikut terhapus otomatis (cascade)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Foreign Key ke tabel 'bukus': Jika data buku dihapus, transaksi peminjamannya ikut terhapus otomatis (cascade)
            $table->foreignId('buku_id')->constrained('bukus')->onDelete('cascade');
            
            $table->date('tanggal_pinjam');                                         // Tanggal saat buku mulai dipinjam
            $table->date('tanggal_kembali')->nullable();                            // Tanggal saat buku dikembalikan (bisa kosong/null jika belum kembali)
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam'); // Status peminjaman (default: dipinjam)
            $table->timestamps();                                                   // created_at & updated_at
        });
    }

    /**
     * Method down():
     * Menghapus tabel jika dilakukan rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
