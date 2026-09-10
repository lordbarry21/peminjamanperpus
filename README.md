# 📚 Sistem Peminjaman Perpustakaan Digital

Aplikasi web manajemen perpustakaan modern berbasis **Laravel 11/12** dan **MySQL** yang dirancang untuk mengotomatisasi proses pengelolaan data master buku serta transaksi peminjaman dan pengembalian buku secara mandiri oleh siswa.

---

## ✨ Fitur Utama (Key Features)

### 🔐 1. Sistem Autentikasi & Multi-Role Access

* **Role-Based Redirection**: Pengalihan otomatis saat login; akun **Admin** langsung diarahkan ke `/admin/dashboard`, sedangkan akun **Siswa/User** diarahkan ke katalog `/dashboard`.
* **Registrasi Mandiri Siswa**: Form register publik otomatis memberikan role `user` bagi pendaftar baru tanpa celah eskalasi hak akses.
* **Proteksi Hak Akses (Middleware)**: Rute administratif (`/admin/*`) dilindungi dengan verifikasi status login dan pengecekan otorisasi role (`403 Akses Ditolak` bagi non-admin).

### 🛠️ 2. Modul Petugas / Administrator

* **Dashboard Admin**: Ringkasan status dan panel kendali operasional perpustakaan.
* **Manajemen Master Data Buku (CRUD)**:
  * **Tambah Buku Baru**: Form penambahan buku dengan validasi kode buku unik (`kode_buku`), judul, pengarang, penerbit, dan stok awal.
  * **Lihat Koleksi**: Pemantauan data buku perpustakaan.
  * **Edit Data Buku**: Pembaruan informasi buku dan penyesuaian jumlah stok fisik.
  * **Hapus Buku**: Penghapusan data buku dengan konfirmasi keamanan.

### 📖 3. Modul Siswa / Anggota

* **Katalog Buku Interaktif**: Menampilkan seluruh koleksi buku yang tersedia (hanya buku dengan `stok > 0` yang dapat dipinjam).
* **Peminjaman Buku Mandiri**:
  * Pengajuan peminjaman langsung dari katalog dalam 1 kali klik.
  * Perhitungan tanggal pengembalian otomatis (7 hari sejak tanggal peminjaman).
  * Pengurangan stok fisik buku secara *real-time* (`decrement`).
* **Riwayat Peminjaman Pribadi**:
  * Daftar riwayat transaksi lengkap dengan tanggal pinjam dan batas waktu pengembalian.
  * Label status transaksi dinamis (*badge* kuning `Dipinjam` dan hijau `Dikembalikan`).
* **Pengembalian Mandiri**: Siswa dapat memproses pengembalian buku yang sedang dipinjam secara langsung, dan sistem otomatis mengembalikan kuota stok buku (`increment`).

### 📱 4. Navigasi Cerdas & Responsif

* Header dan menu navigasi (desktop & mobile) otomatis menyesuaikan menu yang tampil sesuai role pengguna yang sedang login.

---

## 🛠️ Tech Stack & Ekosistem

* **Backend Framework**: [Laravel](https://laravel.com) (PHP 8.2+)
* **Database**: MySQL / MariaDB (Eloquent ORM, Migrations, Seeders)
* **Authentication**: Laravel Breeze (Blade Scaffolding)
* **Frontend & Styling**: Tailwind CSS, Blade Template Engine
* **Asset Bundler**: Vite
* **Testing**: PHPUnit / Pest Feature Testing

---

## 🗄️ Struktur Database & Relasi (ERD)

Database: **`db_perpus_digital`**

```
┌─────────────────────────┐             ┌─────────────────────────┐
│          users          │             │          bukus          │
├─────────────────────────┤             ├─────────────────────────┤
│ PK  id                  │             │ PK  id                  │
│     name                │             │     kode_buku (unique)  │
│     email (unique)      │             │     judul               │
│     password            │             │     pengarang           │
│     role (admin/user)   │             │     penerbit            │
│     timestamps          │             │     stok                │
└────────────┬────────────┘             │     timestamps          │
             │ 1                        └────────────┬────────────┘
             │                                       │ 1
             │ Memiliki                              │ Dimiliki
             │ banyak                                │ banyak
             │ N                                     │ N
             │         ┌───────────────────┐         │
             └────────►│    peminjamans    │◄────────┘
                       ├───────────────────┤
                       │ PK  id            │
                       │ FK  user_id       │
                       │ FK  buku_id       │
                       │     tanggal_pinjam│
                       │     tanggal_kembali
                       │     status        │
                       │     timestamps    │
                       └───────────────────┘
```

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem

Pastikan di komputer Anda sudah terpasang:

* **PHP >= 8.2**
* **Composer**
* **Node.js & NPM**
* **XAMPP / MySQL Server**

### 2. Clone atau Buka Direktori Proyek

```bash
cd peminjamanperpus
```

### 3. Instal Dependensi Backend & Frontend

```bash
composer install
npm install
```

### 4. Konfigurasi Lingkungan (`.env`)

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan pengaturan koneksi database MySQL pada `.env` sudah sesuai:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307        # Ubah ke 3306 jika menggunakan port default XAMPP
DB_DATABASE=db_perpus_digital
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeder Database

Buat database `db_perpus_digital` di phpMyAdmin / MySQL console, lalu jalankan migrasi beserta data awal:

```bash
php artisan migrate --seed
```

### 6. Kompilasi Aset Frontend

```bash
npm run build
```

### 7. Jalankan Server Lokal

```bash
php artisan serve
```

Akses aplikasi melalui browser di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Kredensial Akun Pengujian (Default Seed)

| Peran (Role)            | Email                | Password        | Halaman Tujuan Setelah Login              |
| :---------------------- | :------------------- | :-------------- | :---------------------------------------- |
| **Administrator** | `admin@perpus.com` | `password123` | `http://127.0.0.1:8000/admin/dashboard` |
| **Siswa / User**  | `siswa@perpus.com` | `password123` | `http://127.0.0.1:8000/dashboard`       |

---

## 🧪 Pengujian Otomatis (Automated Tests)

Aplikasi telah dilengkapi unit & feature test komprehensif untuk menguji seluruh alur autentikasi, hak akses, peminjaman siswa, dan CRUD buku admin:

```bash
php artisan test --filter SistemPerpusTest
```

---

## 📖 Dokumentasi Lengkap & Pembahasan

Untuk panduan teknis langkah demi langkah, rincian terminal command, dan pembahasan kode program secara menyeluruh:
👉 [**`PEMBAHASAN_FULL.md`**](PEMBAHASAN_FULL.md)

---
