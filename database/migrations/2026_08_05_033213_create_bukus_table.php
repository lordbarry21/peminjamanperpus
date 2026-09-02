<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat Tabel 'bukus'
 * 
 * Bertanggung jawab membuat struktur skema tabel buku di MySQL secara terprogram.
 */
return new class extends Migration
{
    /**
     * Method up():
     * Dijalankan saat mengeksekusi perintah 'php artisan migrate'
     */
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();                                // Kolom Primary Key (Auto Increment: 1, 2, 3...)
            $table->string('kode_buku')->unique();       // Kode unik buku (contoh: BK-001), tidak boleh kembar
            $table->string('judul');                     // Judul buku (VARCHAR)
            $table->string('pengarang');                 // Nama penulis / pengarang buku
            $table->string('penerbit');                  // Nama penerbit buku
            $table->integer('stok');                     // Jumlah stok fisik buku yang tersedia (INT)
            $table->timestamps();                        // Otomatis membuat 2 kolom: created_at & updated_at
        });
    }

    /**
     * Method down():
     * Dijalankan saat melakukan rollback dengan 'php artisan migrate:rollback'
     */
    public function down(): void
    {
        Schema::dropIfExists('bukus'); // Hapus tabel 'bukus' jika migration dibatalkan
    }
};
