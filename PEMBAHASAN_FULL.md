# 📘 PANDUAN LENGKAP END-TO-END SISTEM PEMINJAMAN PERPUSTAKAAN DIGITAL
### Berdasarkan 3 Modul Acuan:
1. `MODUL 1. PERSIAPAN, DATABASE, DAN LOGIN.pdf`
2. `CRUD Buku.pdf`
3. `Siswa katalog dan pinjam.docx`

---

## 📑 DAFTAR ISI
1. [Gambaran Arsitektur & Perancangan Basis Data](#1-gambaran-arsitektur--perancangan-basis-data)
2. [Prasyarat & Setup Lingkungan Kerja](#2-prasyarat--setup-lingkungan-kerja)
3. [Tahap 1: Inisiasi Proyek & Konfigurasi Lingkungan (.env)](#tahap-1-inisiasi-proyek--konfigurasi-lingkungan-env)
4. [Tahap 2: Skema Database, Migrasi, & Relasi Model (Modul 1)](#tahap-2-skema-database-migrasi--relasi-model-modul-1)
5. [Tahap 3: Database Seeder untuk Data Pengujian Awal (Modul 1)](#tahap-3-database-seeder-untuk-data-pengujian-awal-modul-1)
6. [Tahap 4: Sistem Autentikasi Laravel Breeze & Hak Akses Multi-Role (Modul 1)](#tahap-4-sistem-autentikasi-laravel-breeze--hak-akses-multi-role-modul-1)
7. [Tahap 5: Modul Admin - CRUD Data Buku (CRUD Buku.pdf)](#tahap-5-modul-admin---crud-data-buku-crud-bukupdf)
8. [Tahap 6: Modul Siswa - Katalog & Peminjaman Mandiri (Siswa katalog dan pinjam.docx)](#tahap-6-modul-siswa---katalog--peminjaman-mandiri-siswa-katalog-dan-pinjamdocx)
9. [Tahap 7: Navigasi Dinamis Berdasarkan Role Pengguna](#tahap-7-navigasi-dinamis-berdasarkan-role-pengguna)
10. [Rangkuman Kode Utuh Tiap File (Source Code Directory)](#10-rangkuman-kode-utuh-tiap-file-source-code-directory)
11. [Daftar Perintah Terminal dari Awal Sampai Selesai](#11-daftar-perintah-terminal-dari-awal-sampai-selesai)
12. [Panduan Pengujian Sistem & Troubleshooting Masalah](#12-panduan-pengujian-sistem--troubleshooting-masalah)

---

## 1. Gambaran Arsitektur & Perancangan Basis Data

### 1.1 Konsep MVC (Model-View-Controller)
Aplikasi dibangun di atas arsitektur MVC Laravel:
* **Model** (`app/Models/`): Berkomunikasi langsung dengan database MySQL melalui Eloquent ORM.
* **View** (`resources/views/`): Mengatur antarmuka pengguna berbasis template engine Blade dan styling Tailwind CSS via Vite.
* **Controller** (`app/Http/Controllers/`): Jembatan logika utama yang menerima request dari browser, memvalidasi input, mengolah data melalui Model, dan mengirimkan respon ke View.

### 1.2 Entity Relationship Diagram (ERD)
Sistem memiliki 3 tabel utama yang saling berelasi:
1. **`users`**: Menyimpan kredensial pengguna dan peran akses (`role`: `'admin'` atau `'user'`).
2. **`bukus`**: Menyimpan data koleksi buku (kode buku unik, judul, pengarang, penerbit, stok fisik).
3. **`peminjamans`**: Mencatat transaksi peminjaman buku oleh siswa beserta tanggal pinjam, batas pengembalian, dan status transaksi.

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

## 2. Prasyarat & Setup Lingkungan Kerja

Sebelum memulai pengerjaan, pastikan komponen berikut telah terpasang di komputer Anda:
1. **XAMPP / MySQL Server**: Pastikan modul MySQL dan Apache aktif.
2. **PHP**: Versi minimal PHP 8.2 (disarankan PHP 8.2 atau yang lebih baru). Cek via terminal:
   ```bash
   php -v
   ```
3. **Composer**: Dependency manager PHP. Cek via terminal:
   ```bash
   composer -V
   ```
4. **Node.js & NPM**: Dibutuhkan untuk instalasi dan build aset Vite/Tailwind CSS. Cek via terminal:
   ```bash
   node -v
   npm -v
   ```

> [!NOTE]
> **Catatan Konfigurasi Port MySQL:**
> Pada instalasi default XAMPP, port MySQL adalah **`3306`**. Namun jika di komputer Anda sudah terpasang instance database lain (misalnya Oracle MySQL Server) yang menempati port 3306, maka XAMPP MySQL biasanya berjalan pada port **`3307`**. Sesuaikan nilai `DB_PORT` pada file `.env`.

---

## Tahap 1: Inisiasi Proyek & Konfigurasi Lingkungan (.env)

### 1. Membuat Proyek Laravel Baru
Buka terminal (Command Prompt / PowerShell / Git Bash), arahkan ke folder workspace Anda, lalu jalankan:
```bash
composer create-project laravel/laravel peminjamanperpus
```

Setelah proses selesai, masuk ke dalam folder proyek:
```bash
cd peminjamanperpus
```

Buka proyek di editor (VS Code):
```bash
code .
```

### 2. Konfigurasi Database pada File `.env`
Buka file `.env` di root proyek Anda, lalu atur konfigurasi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=db_perpus_digital
DB_USERNAME=root
DB_PASSWORD=
```
*(Ubah `DB_PORT=3307` menjadi `DB_PORT=3306` jika MySQL XAMPP Anda menggunakan port default 3306).*

### 3. Pembuatan Database di MySQL
Buka MySQL melalui phpMyAdmin (`http://localhost/phpmyadmin` atau sesuai port) atau jalankan perintah SQL berikut di MySQL Console:
```sql
CREATE DATABASE db_perpus_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Tahap 2: Skema Database, Migrasi, & Relasi Model (Modul 1)

### 1. Modifikasi Migration Tabel `users` (Bawaan Laravel)
Buka file migration `database/migrations/0001_01_01_000000_create_users_table.php`. Tambahkan kolom `role` dengan tipe data enum (`admin`, `user`) berstatus default `'user'`:

```php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        // Tambahan kolom role untuk membedakan Admin dan Siswa/User
        $table->enum('role', ['admin', 'user'])->default('user');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    });

    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
}
```

### 2. Membuat Migration & Model untuk Buku
Jalankan perintah berikut di terminal:
```bash
php artisan make:model Buku -m
```
Buka file migration yang baru terbentuk di `database/migrations/..._create_bukus_table.php`, lalu lengkapi strukturnya:
```php
public function up(): void
{
    Schema::create('bukus', function (Blueprint $table) {
        $table->id();
        $table->string('kode_buku')->unique();
        $table->string('judul');
        $table->string('pengarang');
        $table->string('penerbit');
        $table->integer('stok');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('bukus');
}
```

### 3. Membuat Migration & Model untuk Peminjaman
Jalankan perintah berikut di terminal:
```bash
php artisan make:model Peminjaman -m
```
Buka file migration yang baru terbentuk di `database/migrations/..._create_peminjamans_table.php`, lalu sesuaikan strukturnya agar berelasi dengan tabel `users` dan `bukus`:
```php
public function up(): void
{
    Schema::create('peminjamans', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel users (siapa yang meminjam)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        // Relasi ke tabel bukus (buku apa yang dipinjam)
        $table->foreignId('buku_id')->constrained('bukus')->onDelete('cascade');
        $table->date('tanggal_pinjam');
        $table->date('tanggal_kembali')->nullable();
        // Status transaksi: dipinjam atau dikembalikan
        $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('peminjamans');
}
```

### 4. Menjalankan Migrate ke Database
Setelah ketiga struktur tabel selesai disiapkan, jalankan migrasi:
```bash
php artisan migrate
```

### 5. Mengisi Relasi pada Model Laravel

#### A. Model `app/Models/Buku.php`
Tambahkan properti `$guarded = ['id']` dan relasi `peminjamans()`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    // Kolom id dijaga, kolom lainnya diizinkan diisi massal
    protected $guarded = ['id'];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
```

#### B. Model `app/Models/Peminjaman.php`
Tambahkan properti `$table`, `$guarded`, dan relasi ke `User` serta `Buku`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
```

#### C. Model `app/Models/User.php`
Tambahkan relasi ke `Peminjaman` serta masukkan kolom `role` ke dalam array `$fillable`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## Tahap 3: Database Seeder untuk Data Pengujian Awal (Modul 1)

Untuk keperluan pengujian akun Admin dan Siswa serta contoh buku awal, buka file `database/seeders/DatabaseSeeder.php` lalu sesuaikan kodenya:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator Perpus',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Siswa / User
        User::create([
            'name' => 'Siswa Teladan',
            'email' => 'siswa@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // 3. Buat Contoh Data Buku Awal
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
```

Jalankan perintah seeder di terminal:
```bash
php artisan db:seed
```

---

## Tahap 4: Sistem Autentikasi Laravel Breeze & Hak Akses Multi-Role (Modul 1)

### 1. Instalasi Laravel Breeze
Jalankan perintah Composer di terminal:
```bash
composer require laravel/breeze --dev
```

Pasang scaffolding Breeze dengan opsi Blade:
```bash
php artisan breeze:install blade --no-interaction
```

### 2. Menjalankan Migrasi & Build Asset
```bash
php artisan migrate
npm install
npm run build
```

### 3. Menyesuaikan Kolom Role pada Registrasi Siswa
Buka file `app/Http/Controllers/Auth/RegisteredUserController.php`. Pada method `store`, tambahkan atribut `'role' => 'user'` agar siapapun yang mendaftar mandiri otomatis berstatus siswa:
```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => 'user', // Memastikan user yang mendaftar otomatis berstatus 'user'
]);
```

### 4. Menyesuaikan Redirect Login Berdasarkan Role
Buka file `app/Http/Controllers/Auth/AuthenticatedSessionController.php`. Pada method `store`, ubah bagian redirect-nya:
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    // Pengecekan Hak Akses (Role Redirect)
    if (auth()->user()->role === 'admin') {
        return redirect()->intended(route('admin.dashboard', absolute: false));
    } else {
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
```

### 5. Membuat Halaman Dashboard Admin
Buat file `resources/views/admin/dashboard.blade.php`:
```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Selamat Datang, Administrator! Anda login sebagai <strong>Admin</strong>.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## Tahap 5: Modul Admin - CRUD Data Buku (CRUD Buku.pdf)

### 1. Membuat Resource Controller Buku
Jalankan perintah artisan:
```bash
php artisan make:controller Admin/BukuController --resource
```

### 2. Mengisi Logika pada `app/Http/Controllers/Admin/BukuController.php`
Lengkapi seluruh aksi (Index, Create, Store, Edit, Update, Destroy) 100% sesuai modul:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::all();
        return view('admin.buku.index', compact('bukus'));
    }

    public function create()
    {
        return view('admin.buku.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku',
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'stok' => 'required|integer',
        ]);

        Buku::create($request->all());

        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku)
    {
        return view('admin.buku.edit', compact('buku'));
    }

    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku,' . $buku->id,
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'stok' => 'required|integer',
        ]);

        $buku->update($request->all());

        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil dihapus.');
    }
}
```

### 3. Membuat Views Admin untuk Kelola Buku

#### A. File `resources/views/admin/buku/index.blade.php`
*(Sesuai teks instruksi pada PDF halaman 4-6 yang menempatkan form tambah buku):*
```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Buku Baru') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.buku.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Kode Buku</label>
                        <input type="text" name="kode_buku" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Judul Buku</label>
                        <input type="text" name="judul" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Pengarang</label>
                        <input type="text" name="pengarang" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Penerbit</label>
                        <input type="text" name="penerbit" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Stok</label>
                        <input type="number" name="stok" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Simpan</button>
                    <a href="{{ route('admin.buku.index') }}" class="ml-2 text-gray-600">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
```

#### B. File `resources/views/admin/buku/create.blade.php`
*(Form tambah buku yang dipanggil oleh method `BukuController@create`):*
Isinya sama persis dengan form di atas agar pemanggilan route `route('admin.buku.create')` berjalan normal tanpa error *View Not Found*.

#### C. File `resources/views/admin/buku/edit.blade.php`
*(Sesuai PDF halaman 6-7 untuk edit data buku):*
```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Data Buku') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.buku.update', $buku->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Kode Buku</label>
                        <input type="text" name="kode_buku" value="{{ $buku->kode_buku }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Judul Buku</label>
                        <input type="text" name="judul" value="{{ $buku->judul }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Pengarang</label>
                        <input type="text" name="pengarang" value="{{ $buku->pengarang }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Penerbit</label>
                        <input type="text" name="penerbit" value="{{ $buku->penerbit }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Stok</label>
                        <input type="number" name="stok" value="{{ $buku->stok }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Update</button>
                    <a href="{{ route('admin.buku.index') }}" class="ml-2 text-gray-600">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## Tahap 6: Modul Siswa - Katalog & Peminjaman Mandiri (Siswa katalog dan pinjam.docx)

### 1. Membuat User Katalog Controller
Jalankan perintah berikut:
```bash
php artisan make:controller User/KatalogController
```

### 2. Mengisi Logika pada `app/Http/Controllers/User/KatalogController.php`
```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index()
    {
        $bukus = Buku::where('stok', '>', 0)->get();
        $riwayat = Peminjaman::with('buku')
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->get();

        return view('user.katalog', compact('bukus', 'riwayat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_kembali' => 'required|date|after:today',
        ]);

        $buku = Buku::findOrFail($request->buku_id);

        if ($buku->stok <= 0) {
            return back()->with('error', 'Maaf, stok buku ini sedang kosong.');
        }

        Peminjaman::create([
            'user_id' => auth()->id(),
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => date('Y-m-d'),
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dipinjam',
        ]);

        $buku->decrement('stok');

        return redirect()->route('user.katalog')->with('success', 'Berhasil meminjam buku. Silakan ambil buku di perpustakaan.');
    }
}
```

### 3. Tampilan View Katalog & Peminjaman Siswa (Versi 2)
Disimpan di **`resources/views/dashboard.blade.php`** dan **`resources/views/user/katalog.blade.php`**:

```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog & Peminjaman Buku Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Notifikasi Pesan -->
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-lg shadow">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-lg shadow">{{ session('error') }}</div>
            @endif

            <!-- Bagian 1: Daftar Katalog Buku Tersedia -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📚 Daftar Katalog Buku Tersedia</h3>
                
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border border-gray-300 p-2">Kode</th>
                            <th class="border border-gray-300 p-2">Judul Buku</th>
                            <th class="border border-gray-300 p-2">Pengarang</th>
                            <th class="border border-gray-300 p-2">Penerbit</th>
                            <th class="border border-gray-300 p-2">Stok</th>
                            <th class="border border-gray-300 p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bukus as $buku)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-2 text-center font-medium">{{ $buku->kode_buku }}</td>
                            <td class="border border-gray-300 p-2">{{ $buku->judul }}</td>
                            <td class="border border-gray-300 p-2">{{ $buku->pengarang }}</td>
                            <td class="border border-gray-300 p-2">{{ $buku->penerbit }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $buku->stok }}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                <form action="{{ route('user.pinjam') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                                    <input type="hidden" name="tanggal_kembali" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-3 py-1 rounded text-sm shadow">Pinjam</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 p-4 text-center text-gray-500">Semua stok buku sedang kosong atau habis dipinjam.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bagian 2: Riwayat Peminjaman & Pengembalian Mandiri -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📖 Riwayat Peminjaman Buku Saya</h3>
                
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border border-gray-300 p-2">No</th>
                            <th class="border border-gray-300 p-2">Judul Buku</th>
                            <th class="border border-gray-300 p-2">Tanggal Pinjam</th>
                            <th class="border border-gray-300 p-2">Batas Pengembalian</th>
                            <th class="border border-gray-300 p-2">Status</th>
                            <th class="border border-gray-300 p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $r)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2">{{ $r->buku->judul }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $r->tanggal_pinjam }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $r->tanggal_kembali }}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $r->status === 'dipinjam' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">
                                @if($r->status === 'dipinjam')
                                    <form action="{{ route('user.kembali', $r->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin mengembalikan buku ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1 rounded text-sm shadow">Kembalikan</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 p-4 text-center text-gray-500">Belum ada riwayat peminjaman buku.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 4. Pendaftaran Seluruh Rute pada `routes/web.php`
Gabungkan seluruh route dari Modul 1, CRUD Buku, dan Siswa Katalog ke dalam file `routes/web.php`:

```php
<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\KatalogController;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Sesuai Modul 1, CRUD Buku, dan Siswa Katalog & Pinjam
*/

// Halaman Awal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard / Katalog Buku untuk Siswa (User)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [KatalogController::class, 'index'])->name('dashboard');
    Route::get('/katalog', [KatalogController::class, 'index'])->name('user.katalog');
    Route::post('/dashboard/pinjam', [KatalogController::class, 'store'])->name('user.pinjam');

    // Route untuk tombol aksi pengembalian mandiri siswa
    Route::patch('/dashboard/kembali/{id}', function ($id) {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'dikembalikan']);
        $peminjaman->buku->increment('stok');
        return back()->with('success', 'Buku berhasil dikembalikan.');
    })->name('user.kembali');
});

// Grup Route Administrator
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', function () {
        // Pastikan hanya admin yang bisa akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Route CRUD Buku
    Route::resource('/admin/buku', BukuController::class, ['as' => 'admin']);
});

// Route Profile bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## Tahap 7: Navigasi Dinamis Berdasarkan Role Pengguna

Buka file `resources/views/layouts/navigation.blade.php`. Agar menu atas dan menu mobile menyesuaikan role pengguna (Admin melihat menu Admin, Siswa melihat menu Katalog):

### Navigasi Desktop (Baris 14-26):
```html
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    @if (Auth::user()->role === 'admin')
        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
            {{ __('Dashboard Admin') }}
        </x-nav-link>
        <x-nav-link :href="route('admin.buku.index')" :active="request()->routeIs('admin.buku.*')">
            {{ __('Kelola Buku') }}
        </x-nav-link>
    @else
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Katalog Buku') }}
        </x-nav-link>
    @endif
</div>
```

### Navigasi Responsive Mobile:
```html
<div class="pt-2 pb-3 space-y-1">
    @if (Auth::user()->role === 'admin')
        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
            {{ __('Dashboard Admin') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.buku.index')" :active="request()->routeIs('admin.buku.*')">
            {{ __('Kelola Buku') }}
        </x-responsive-nav-link>
    @else
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Katalog Buku') }}
        </x-responsive-nav-link>
    @endif
</div>
```

---

## 10. Rangkuman Kode Utuh Tiap File (Source Code Directory)

Berikut pohon file utama yang telah dibuat dan disesuaikan:

```
peminjamanperpus/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── BukuController.php          # [CRUD Buku Admin]
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php # [Redirect Role Login]
│   │   │   │   └── RegisteredUserController.php       # [Default Role User]
│   │   │   └── User/
│   │   │       └── KatalogController.php       # [Katalog & Pinjam Siswa]
│   └── Models/
│       ├── Buku.php                            # [Model Buku & Relasi]
│       ├── Peminjaman.php                      # [Model Peminjaman & Relasi]
│       └── User.php                            # [Model User & Relasi]
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php       # [Enum Role]
│   │   ├── 2026_09_10_005752_create_bukus_table.php       # [Skema Buku]
│   │   └── 2026_09_10_005758_create_peminjamans_table.php # [Skema Peminjaman]
│   └── seeders/
│       └── DatabaseSeeder.php                  # [Data Admin, User, Buku Awal]
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── dashboard.blade.php             # [Dashboard Admin]
│       │   └── buku/
│       │       ├── index.blade.php             # [Form Tambah Buku - Teks PDF]
│       │       ├── create.blade.php            # [Form Tambah Buku]
│       │       └── edit.blade.php              # [Form Edit Buku]
│       ├── user/
│       │   └── katalog.blade.php               # [Katalog Siswa Versi 2]
│       ├── dashboard.blade.php                 # [Katalog Siswa Versi 2]
│       └── layouts/
│           └── navigation.blade.php            # [Menu Dinamis Multi-Role]
└── routes/
    └── web.php                                 # [Pendaftaran Route Sistem]
```

---

## 11. Daftar Perintah Terminal dari Awal Sampai Selesai

```bash
# 1. Inisiasi Proyek Baru
composer create-project laravel/laravel peminjamanperpus
cd peminjamanperpus

# 2. Buat Model dan Migrasi
php artisan make:model Buku -m
php artisan make:model Peminjaman -m

# 3. Jalankan Migrasi Awal
php artisan migrate

# 4. Jalankan Seeder Akun & Buku Awal
php artisan db:seed

# 5. Instalasi Paket Autentikasi Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction
php artisan migrate
npm install
npm run build

# 6. Buat Controller Admin & Siswa
php artisan make:controller Admin/BukuController --resource
php artisan make:controller User/KatalogController

# 7. Memverifikasi Rute Aplikasi
php artisan route:list

# 8. Menjalankan Server Lokal
php artisan serve
```

---

## 12. Panduan Pengujian Sistem & Troubleshooting Masalah

### 12.1 Kredensial Akun Bawaan (Hasil Seeder)

| Peran (Role) | Email | Password | URL Tujuan Setelah Login |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@perpus.com` | `password123` | `http://127.0.0.1:8000/admin/dashboard` |
| **Siswa / User** | `siswa@perpus.com` | `password123` | `http://127.0.0.1:8000/dashboard` |

### 12.2 Skenario Pengujian

#### Skenario 1: Login & Fitur Admin
1. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
2. Buka browser pada alamat `http://127.0.0.1:8000/login`.
3. Masuk dengan akun admin (`admin@perpus.com` / `password123`).
4. Sistem otomatis mengalihkan admin ke `/admin/dashboard`.
5. Klik menu **Kelola Buku** pada navigasi atas (`/admin/buku`).
6. Isi form untuk menambahkan buku baru lalu klik **Simpan**. Data tersimpan ke tabel `bukus`.
7. Logout dari akun admin.

#### Skenario 2: Login & Fitur Siswa
1. Buka `http://127.0.0.1:8000/login`.
2. Masuk dengan akun siswa (`siswa@perpus.com` / `password123`).
3. Sistem otomatis mengalihkan siswa ke `/dashboard` (menampilkan katalog buku tersedia dan riwayat).
4. Pada tabel **Daftar Katalog Buku Tersedia**, klik tombol hijau **Pinjam**.
5. Stok buku berkurang 1 dan data peminjaman langsung masuk ke tabel **Riwayat Peminjaman Buku Saya** dengan status badge kuning `Dipinjam`.
6. Klik tombol biru **Kembalikan** pada baris buku yang dipinjam. Status berubah menjadi badge hijau `Dikembalikan` dan stok buku bertambah kembali 1.

---

### 12.3 Troubleshooting Kendala Umum

* **Kendala 1: `SQLSTATE[HY000] [2002] Connection refused`**
  * **Penyebab**: Service MySQL belum aktif di XAMPP Control Panel atau nomor port di `.env` keliru.
  * **Solusi**: Aktifkan modul MySQL di XAMPP. Jika port MySQL Anda adalah 3307, pastikan `DB_PORT=3307` pada file `.env`.

* **Kendala 2: `Route [user.katalog] not defined`**
  * **Penyebab**: Method `store` di `KatalogController` melakukan redirect ke route `user.katalog`, tetapi route belum memiliki alias nama tersebut.
  * **Solusi**: Pastikan pada `routes/web.php` terdapat baris `Route::get('/katalog', [KatalogController::class, 'index'])->name('user.katalog');`.

* **Kendala 3: Tampilan CSS Rusak / Berantakan**
  * **Penyebab**: Aset Tailwind belum dibuild oleh Vite.
  * **Solusi**: Jalankan perintah `npm run build` di terminal proyek.
