# 📚 Sistem Peminjaman Perpustakaan Digital

Aplikasi web manajemen peminjaman buku perpustakaan digital yang dibangun menggunakan framework **Laravel 11**, **Tailwind CSS**, dan **Vite**.

> 📖 **[Baca Pembahasan Lengkap & Panduan Teknis (PEMBAHASAN_FULL.md)](./PEMBAHASAN_FULL.md)** — Dokumentasi detail mencakup konsep MVC, analisis kode file-per-file, relasi database, dan lifecycle request.

---

## 🛠️ Tech Stack & Kebutuhan Lingkungan (Environment)

Project ini dibangun dengan spesifikasi teknologi berikut:

| Tool / Stack | Keterangan / Versi |
| :--- | :--- |
| **Framework** | Laravel 11.x |
| **PHP** | PHP >= 8.2 |
| **Package Manager** | Composer 2.x |
| **Frontend & Assets** | Vite & Tailwind CSS |
| **Runtime JS** | Node.js (>= 18.x) & npm |
| **Database** | MySQL / MariaDB (via XAMPP / Laragon) |

---

## 📋 Persyaratan Sistem Minimum

Sebelum menjalankan project, pastikan di komputer Anda sudah terpasang:
- **PHP** >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **MySQL / MariaDB Server** (misal via XAMPP)
- Web Browser modern (Google Chrome, Microsoft Edge, Firefox, dll)

---

## 🚀 Panduan Penggunaan & Instalasi (How to Use / Run)

Ikuti langkah-langkah berikut untuk menjalankan project di perangkat lokal:

### 1. Clone Repository
```bash
git clone https://github.com/lordbarry21/peminjamanperpus.git
cd peminjamanperpus
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
# Windows (PowerShell / CMD)
copy .env.example .env

# Atau Linux / macOS / Git Bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan pengaturan database sesuai konfigurasi MySQL di komputer Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307          # Ubah ke 3306 jika menggunakan port default MySQL/XAMPP
DB_DATABASE=db_perpus_digital
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

### 5. Buat Database & Nyalakan MySQL
1. Buka **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **MySQL**.
3. Buat database baru bernama `db_perpus_digital` melalui **phpMyAdmin** (`http://localhost/phpmyadmin`) atau MySQL CLI.

### 6. Generate Application Key & Jalankan Migrasi
```bash
# Generate App Key
php artisan key:generate

# Jalankan migrasi database beserta data awal (seeder)
php artisan migrate --seed
```

### 7. Jalankan Server Pengembangan
Jalankan backend Laravel dan frontend Vite di dua terminal terpisah:

**Terminal 1 (Laravel Server):**
```bash
php artisan serve
```
> Server akan berjalan di: `http://127.0.0.1:8000`

**Terminal 2 (Vite Asset Bundler):**
```bash
npm run dev
```

---

## 🌐 Akses Aplikasi

Setelah kedua server berjalan, buka browser dan akses tautan berikut:
- **URL Aplikasi**: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## ✨ Fitur Utama
- 🔐 **Autentikasi Pengguna**: Registrasi akun, Login, dan Logout.
- 📖 **Manajemen Buku (CRUD)**: Tambah data buku, edit, hapus, dan lihat katalog.
- 📑 **Peminjaman & Pengembalian**: Transaksi peminjaman buku perpustakaan.
- 👤 **Profil Pengguna**: Manajemen data profil dan ubah kata sandi.
- 📱 **Responsif & Modern**: Tampilan antarmuka bersih menggunakan Tailwind CSS.

---

## 📄 Lisensi
Project ini bersifat open-source di bawah lisensi [MIT License](LICENSE).
