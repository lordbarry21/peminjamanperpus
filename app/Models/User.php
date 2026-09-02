<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User (Pengguna / Akun)
 * 
 * Merepresentasikan tabel 'users' di database dan bertindak sebagai
 * kelas otentikasi pengguna untuk login, registrasi, serta pengecekan role (admin/user).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * $guarded = ['id']:
     * Melindungi primary key 'id' agar tidak bisa diubah sembarangan via input form.
     */
    protected $guarded = ['id'];

    /**
     * $hidden:
     * Kolom yang disembunyikan saat data user dikonversi ke Array atau JSON
     * demi keamanan informasi kredensial.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * casts():
     * Konversi tipe data otomatis saat membaca kolom tertentu dari database.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Mengubah string tanggal menjadi objek Carbon DateTime
            'password' => 'hashed',             // Otomatis mengenkripsi password dengan hash Bcrypt
        ];
    }

    /**
     * Relasi One-to-Many (Satu ke Banyak):
     * 1 Pengguna (User) dapat memiliki banyak transaksi riwayat peminjaman buku.
     */
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
