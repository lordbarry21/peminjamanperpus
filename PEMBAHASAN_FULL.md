# 📘 Pembahasan Lengkap & Panduan Teknis: Sistem Peminjaman Perpustakaan Digital

Dokumen ini berisi dokumentasi mendalam, arsitektur sistem, struktur database, analisis kode baris per baris, dan alur kerja aplikasi **Sistem Peminjaman Perpustakaan Digital** berbasis **Laravel 11**.

---

## 📑 Daftar Isi
1. [Gambaran Umum Sistem](#1-gambaran-umum-sistem)
2. [Konsep Arsitektur MVC (Model-View-Controller)](#2-konsep-arsitektur-mvc-model-view-controller)
3. [Struktur Direktori Proyek](#3-struktur-direktori-proyek)
4. [Struktur Database & Relasi Antar Tabel](#4-struktur-database--relasi-antar-tabel)
5. [Sistem Autentikasi & Hak Akses (Multi-Role)](#5-sistem-autentikasi--hak-akses-multi-role)
6. [Analisis File & Kode Inti (Code Walkthrough)](#6-analisis-file--kode-inti-code-walkthrough)
   - [A. Model](#a-model-appmodels)
   - [B. Controller](#b-controller-apphttpcontrollers)
   - [C. Routing](#c-routing-routeswebphp)
   - [D. View / Blade Template](#d-view--blade-template-resourcesviews)
7. [Siklus Hidup Permintaan (Request Lifecycle)](#7-siklus-hidup-permintaan-request-lifecycle)
8. [Panduan Troubleshooting Masalah Umum](#8-panduan-troubleshooting-masalah-umum)
9. [Kredensial Akun Pengujian](#9-kredensial-akun-pengujian)

---

## 1. Gambaran Umum Sistem

Aplikasi ini adalah sistem berbasis web untuk mengotomatisasi proses pengelolaan data buku dan transaksi peminjaman buku pada perpustakaan sekolah/institusi.

### Peran Pengguna (User Roles):
* **Admin (Petugas Perpustakaan)**: Memiliki hak penuh untuk mengelola master data buku (Tambah, Lihat, Ubah, Hapus) dan memantau operasional perpustakaan.
* **User (Siswa / Anggota)**: Dapat mendaftar, login, melihat katalog buku yang tersedia, melakukan peminjaman buku, serta mengelola profil pribadi.

---

## 2. Konsep Arsitektur MVC (Model-View-Controller)

Laravel menggunakan pola desain arsitektur **MVC** yang memisahkan tanggung jawab kode menjadi 3 lapisan utama:

```
                  ┌───────────────────────────────┐
                  │    Browser Pengguna (Client)  │
                  └───────────────┬───────────────┘
                                  │ 1. Request HTTP (URL)
                                  ▼
                  ┌───────────────────────────────┐
                  │    Routing (routes/web.php)   │
                  └───────────────┬───────────────┘
                                  │ 2. Mengarahkan ke Controller
                                  ▼
                  ┌───────────────────────────────┐
                  │          CONTROLLER           │
                  │ (app/Http/Controllers/...)    │
                  └──────┬─────────────────▲──────┘
                         │ 3. Panggil data │ 4. Mengembalikan
                         ▼                 │    data/objek
         ┌───────────────────────────┐     │
         │           MODEL           ├─────┘
         │    (app/Models/...)       │
         └─────────────┬─────────────┘
                       │ Query SQL (ORM Eloquent)
                       ▼
         ┌───────────────────────────┐
         │      Database MySQL       │
         └───────────────────────────┘
                         │
                         │ 5. Mengirim data ke View
                         ▼
                  ┌───────────────────────────────┐
                  │             VIEW              │
                  │    (resources/views/...)      │
                  └───────────────┬───────────────┘
                                  │ 6. Render HTML + CSS
                                  ▼
                  ┌───────────────────────────────┐
                  │        Layar Pengguna         │
                  └───────────────────────────────┘
```

1. **Model**: Mengelola interaksi dengan tabel database, relasi, dan aturan data.
2. **View**: Mengatur tampilan antarmuka (UI) menggunakan template engine Blade dan Tailwind CSS.
3. **Controller**: Otak logika aplikasi yang menghubungkan input pengguna, memanggil Model, dan menampilkan View.

---

## 3. Struktur Direktori Proyek

Berikut fungsi folder-folder utama dalam proyek ini:

```
peminjamanperpus/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Berisi controller logika (BukuController, Auth, Profile)
│   │   └── Requests/          # Validasi form request
│   └── Models/                # Model Eloquent (User.php, Buku.php, Peminjaman.php)
├── config/                    # File konfigurasi sistem (database, session, auth, dll)
├── database/
│   ├── migrations/            # Struktur skema tabel database
│   └── seeders/               # Pengisi data awal database otomatis
├── public/                    # Titik masuk web (index.php, aset terkompilasi)
├── resources/
│   ├── css/                   # Styling aplikasi (Tailwind CSS)
│   ├── js/                    # Skrip JavaScript / Vite
│   └── views/                 # File antarmuka (.blade.php)
├── routes/
│   ├── web.php                # Jalur URL halaman web utama
│   └── auth.php               # Jalur URL autentikasi (login, register, logout)
└── .env                       # Variabel lingkungan & konfigurasi database
```

---

## 4. Struktur Database & Relasi Antar Tabel

Database proyek ini bernama **`db_perpus_digital`**. Terdiri dari 3 tabel utama yang saling berelasi:

### Diagram Relasi Tabel (Entity Relationship):
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

### Relasi Eloquent:
* **User -> Peminjaman**: 1 User dapat memiliki banyak transaksi peminjaman (`hasMany`).
* **Buku -> Peminjaman**: 1 Buku dapat dipinjam dalam banyak transaksi (`hasMany`).
* **Peminjaman -> User & Buku**: Setiap baris peminjaman dimiliki oleh 1 user dan 1 buku (`belongsTo`).

---

## 5. Sistem Autentikasi & Hak Akses (Multi-Role)

Aplikasi membedakan hak akses berdasarkan kolom `role` pada tabel `users`.

1. **Saat Pengguna Mengakses URL Admin** (`/admin/*`):
   * Sistem memeriksa status login (`auth` middleware).
   * Sistem memeriksa nilai `auth()->user()->role`.
   * Jika bukan `'admin'`, sistem otomatis mengembalikan response `403 Forbidden` (Akses ditolak).

---

## 6. Analisis File & Kode Inti (Code Walkthrough)

### A. Model (`app/Models/`)

#### 1. [Buku.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/app/Models/Buku.php)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    
    // guarded ['id'] mengizinkan semua kolom selain 'id' untuk diisi (Mass Assignment)
    protected $guarded = ['id'];

    // Relasi One-to-Many ke tabel Peminjaman
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
```

#### 2. [Peminjaman.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/app/Models/Peminjaman.php)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $guarded = ['id'];

    // Relasi Many-to-One: Peminjaman milik User tertentu
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Many-to-One: Peminjaman terkait Buku tertentu
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
```

---

### B. Controller (`app/Http/Controllers/`)

#### [BukuController.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/app/Http/Controllers/Admin/BukuController.php)
Controller ini menangani seluruh fungsi CRUD (*Create, Read, Update, Delete*) data buku:

* **`checkAdmin()`**: Method privat untuk memastikan hanya akun role admin yang dapat mengeksekusi aksi.
* **`index()`**: Mengambil semua koleksi buku dengan `Buku::all()` dan mengirimkannya ke view `admin.buku.index`.
* **`create()`**: Menampilkan form penambahan buku baru.
* **`store(Request $request)`**: Memvalidasi data input (judul, kode buku unik, stok angka), lalu menyimpannya dengan `Buku::create($request->all())`.
* **`edit(Buku $buku)`**: Menggunakan *Route Model Binding* untuk otomatis mencari buku berdasarkan ID dan menampilkan form edit.
* **`update(Request $request, Buku $buku)`**: Memvalidasi input dan memperbarui data buku dengan `$buku->update($request->all())`.
* **`destroy(Buku $buku)`**: Menghapus data buku dari database melalui `$buku->delete()`.

---

### C. Routing (`routes/web.php`)

```php
// Halaman Selamat Datang
Route::get('/', function () {
    return view('welcome');
});

// Dashboard User Biasa / Siswa
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup Halaman Admin (Dilindungi Auth & Pengecekan Role)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Route Resource CRUD Buku Otomatis (index, create, store, edit, update, destroy)
    Route::resource('/admin/buku', BukuController::class, ['as' => 'admin']);
});
```

---

### D. View / Blade Template (`resources/views/`)

* **[resources/views/admin/buku/index.blade.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/resources/views/admin/buku/index.blade.php)**: Menampilkan tabel daftar buku, tombol tambah, edit, dan form hapus dengan konfirmasi javascript.
* **[resources/views/admin/buku/create.blade.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/resources/views/admin/buku/create.blade.php)**: Form input penambahan buku baru lengkap dengan pesan error validasi `@error`.
* **[resources/views/admin/buku/edit.blade.php](file:///d:/Bari/buDinda%20belajar/peminjamanperpus/resources/views/admin/buku/edit.blade.php)**: Form modifikasi data buku dengan metode `@method('PUT')`.

---

## 7. Siklus Hidup Permintaan (Request Lifecycle)

Contoh: **Admin Menyimpan Buku Baru**
1. Admin mengisi form di `/admin/buku/create` lalu klik **Simpan**.
2. Browser mengirim data form via HTTP `POST` ke `/admin/buku`.
3. File `routes/web.php` mencocokkan route dan memanggil `BukuController@store`.
4. `BukuController` memvalidasi bahwa `kode_buku` belum pernah dipakai dan `stok` berupa angka.
5. Model `Buku` mengeksekusi query SQL `INSERT INTO bukus (...) VALUES (...)` ke MySQL.
6. Controller mengembalikan response `redirect()` ke `/admin/buku` disertai pesan `session('success')`.
7. Browser memuat kembali tabel buku dan menampilkan banner hijau tanda sukses.

---

## 8. Panduan Troubleshooting Masalah Umum

| Gejala Error | Penyebab | Solusi |
| :--- | :--- | :--- |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL XAMPP belum aktif atau port di `.env` salah | Buka XAMPP Control Panel, klik **Start** pada MySQL, dan pastikan `DB_PORT=3307` (atau `3306`) sesuai port MySQL aktif. |
| `403 This action is unauthorized / Akses ditolak` | Login menggunakan akun role `user` dan mencoba membuka `/admin/*` | Login menggunakan akun role `admin` (`admin@perpus.com`). |
| `Vite manifest not found` | Aset CSS / JS belum dibundel | Jalankan perintah `npm run build` atau aktifkan `npm run dev`. |

---

## 9. Kredensial Akun Pengujian

Akun berikut tersedia secara bawaan dari Database Seeder:

| Akun | Email | Password | Role | Akses Halaman |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin@perpus.com` | `password123` | `admin` | Dashboard Admin & CRUD Buku (`/admin/buku`) |
| **Siswa / Anggota** | `siswa@perpus.com` | `password123` | `user` | Dashboard Siswa (`/dashboard`) |
