<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SiswaPeminjamanTest
 * 
 * Pengujian fitur alur siswa: Dashboard, Katalog Buku, Peminjaman, dan Pengembalian Buku.
 */
class SiswaPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $siswa;
    protected $buku;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Inisialisasi Akun Pengujian Admin
        $this->admin = User::factory()->create([
            'name' => 'Admin Perpus',
            'email' => 'admin@perpus.com',
            'role' => 'admin',
        ]);

        // 2. Inisialisasi Akun Pengujian Siswa
        $this->siswa = User::factory()->create([
            'name' => 'Siswa Penguji',
            'email' => 'siswa@perpus.com',
            'role' => 'user',
        ]);

        // 3. Inisialisasi Data Buku Pengujian
        $this->buku = Buku::create([
            'kode_buku' => 'BK-101',
            'judul' => 'Pemrograman PHP Modern',
            'pengarang' => 'Taylor Otwell',
            'penerbit' => 'Laravel Press',
            'stok' => 3,
        ]);
    }

    public function test_guest_cannot_access_siswa_pages()
    {
        $this->get(route('dashboard'))->assertRedirect('/login');
        $this->get(route('siswa.buku.index'))->assertRedirect('/login');
        $this->get(route('siswa.peminjaman.index'))->assertRedirect('/login');
    }

    public function test_admin_accessing_dashboard_is_redirected_to_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_siswa_can_view_dashboard_with_stats()
    {
        $response = $this->actingAs($this->siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Siswa');
        $response->assertSee('Halo, ' . $this->siswa->name);
        $response->assertSee('Buka Katalog Buku');
    }

    public function test_siswa_can_view_katalog_buku_and_search()
    {
        $response = $this->actingAs($this->siswa)->get(route('siswa.buku.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Koleksi Buku Perpustakaan');
        $response->assertSee('BK-101');
        $response->assertSee('Pemrograman PHP Modern');

        // Uji Filter Pencarian
        $searchResponse = $this->actingAs($this->siswa)->get(route('siswa.buku.index', ['search' => 'Taylor']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Taylor Otwell');
    }

    public function test_siswa_can_borrow_available_book()
    {
        $stokAwal = $this->buku->stok;

        $response = $this->actingAs($this->siswa)->post(route('siswa.peminjaman.store'), [
            'buku_id' => $this->buku->id,
        ]);

        $response->assertRedirect(route('siswa.peminjaman.index'));
        $response->assertSessionHas('success');

        // Pastikan data tersimpan di tabel peminjamans
        $this->assertDatabaseHas('peminjamans', [
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'status' => 'dipinjam',
        ]);

        // Pastikan stok buku berkurang 1
        $this->assertEquals($stokAwal - 1, $this->buku->fresh()->stok);
    }

    public function test_siswa_cannot_borrow_book_with_zero_stock()
    {
        $bukuHabis = Buku::create([
            'kode_buku' => 'BK-999',
            'judul' => 'Buku Langka Habis Stok',
            'pengarang' => 'Anonim',
            'penerbit' => 'Arsip',
            'stok' => 0,
        ]);

        $response = $this->actingAs($this->siswa)->post(route('siswa.peminjaman.store'), [
            'buku_id' => $bukuHabis->id,
        ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('peminjamans', [
            'user_id' => $this->siswa->id,
            'buku_id' => $bukuHabis->id,
        ]);
    }

    public function test_siswa_cannot_borrow_same_book_twice_without_returning()
    {
        // Peminjaman pertama
        $this->actingAs($this->siswa)->post(route('siswa.peminjaman.store'), [
            'buku_id' => $this->buku->id,
        ]);

        // Percobaan peminjaman kedua untuk buku yang sama
        $response = $this->actingAs($this->siswa)->post(route('siswa.peminjaman.store'), [
            'buku_id' => $this->buku->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, Peminjaman::where('user_id', $this->siswa->id)->where('buku_id', $this->buku->id)->count());
    }

    public function test_siswa_can_return_borrowed_book()
    {
        // 1. Siswa meminjam buku terlebih dahulu
        $peminjaman = Peminjaman::create([
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->buku->decrement('stok');

        $stokSebelumKembali = $this->buku->fresh()->stok;

        // 2. Siswa mengembalikan buku
        $response = $this->actingAs($this->siswa)->patch(route('siswa.peminjaman.kembalikan', $peminjaman->id));

        $response->assertSessionHas('success');

        // 3. Verifikasi status peminjaman di database
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'status' => 'dikembalikan',
        ]);
        $this->assertNotNull($peminjaman->fresh()->tanggal_kembali);

        // 4. Verifikasi stok bertambah kembali (+1)
        $this->assertEquals($stokSebelumKembali + 1, $this->buku->fresh()->stok);
    }

    public function test_other_siswa_cannot_return_different_siswa_book()
    {
        $siswaLain = User::factory()->create(['role' => 'user']);

        $peminjaman = Peminjaman::create([
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Siswa lain mencoba mengembalikan
        $response = $this->actingAs($siswaLain)->patch(route('siswa.peminjaman.kembalikan', $peminjaman->id));

        $response->assertStatus(403);
    }

    public function test_siswa_can_view_riwayat_peminjaman()
    {
        Peminjaman::create([
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.peminjaman.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Peminjaman Saya');
        $response->assertSee('BK-101');
        $response->assertSee('Dipinjam');
    }
}
