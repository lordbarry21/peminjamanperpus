<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder
 * 
 * Mengisi data awal secara otomatis ke dalam database saat perintah
 * 'php artisan db:seed' atau 'php artisan migrate --seed' dijalankan.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Method run():
     * Titik eksekusi utama seeder untuk membuat data dummy/awal.
     */
    public function run(): void
    {
        // 1. Membuat Akun Pengujian Administrator
        User::create([
            'name' => 'Administrator Perpus',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password123'), // Mengenkripsi password menggunakan Hash Bcrypt
            'role' => 'admin',
        ]);

        // 2. Membuat Akun Pengujian Siswa / Anggota Biasa
        User::create([
            'name' => 'Siswa Teladan',
            'email' => 'siswa@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // 3. Mengisi Contoh Data Master Buku Awal
        Buku::create([
            'kode_buku' => 'BK-001',
            'judul' => 'Pemrograman Web Laravel Dasar',
            'pengarang' => 'Eko Kurniawan',
            'penerbit' => 'Media Ilmu',
            'stok' => 5,
        ]);

        Buku::create([
            'kode_buku' => 'BK-002',
            'judul' => 'Belajar Basis Data MySQL untuk Pemula',
            'pengarang' => 'Budi Raharjo',
            'penerbit' => 'Informatika',
            'stok' => 3,
        ]);
    }
}
