<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemPerpusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_redirect_admin(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@perpus.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_login_redirect_siswa(): void
    {
        $response = $this->post('/login', [
            'email' => 'siswa@perpus.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_siswa_katalog_dan_pinjam(): void
    {
        $siswa = User::where('role', 'user')->first();
        $buku = Buku::first();
        $initialStok = $buku->stok;

        $response = $this->actingAs($siswa)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($buku->judul);

        $pinjamResponse = $this->actingAs($siswa)->post('/dashboard/pinjam', [
            'buku_id' => $buku->id,
            'tanggal_kembali' => date('Y-m-d', strtotime('+7 days')),
        ]);

        $pinjamResponse->assertRedirect(route('user.katalog'));
        $this->assertEquals($initialStok - 1, $buku->fresh()->stok);

        $peminjaman = Peminjaman::where('user_id', $siswa->id)->first();
        $this->assertNotNull($peminjaman);
        $this->assertEquals('dipinjam', $peminjaman->status);

        // Test pengembalian
        $kembaliResponse = $this->actingAs($siswa)->patch('/dashboard/kembali/' . $peminjaman->id);
        $kembaliResponse->assertStatus(302);
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
        $this->assertEquals($initialStok, $buku->fresh()->stok);
    }

    public function test_admin_crud_buku(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Admin dashboard
        $dashboardResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);

        // Admin tambah buku
        $storeResponse = $this->actingAs($admin)->post('/admin/buku', [
            'kode_buku' => 'BK-TEST-99',
            'judul' => 'Buku Uji Coba Unit Test',
            'pengarang' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'stok' => 10,
        ]);

        $storeResponse->assertRedirect(route('admin.buku.index'));
        $this->assertDatabaseHas('bukus', ['kode_buku' => 'BK-TEST-99']);

        $bukuBaru = Buku::where('kode_buku', 'BK-TEST-99')->first();

        // Admin edit buku
        $updateResponse = $this->actingAs($admin)->put('/admin/buku/' . $bukuBaru->id, [
            'kode_buku' => 'BK-TEST-99',
            'judul' => 'Buku Uji Coba Diperbarui',
            'pengarang' => 'Penulis Uji Baru',
            'penerbit' => 'Penerbit Uji Baru',
            'stok' => 12,
        ]);
        $updateResponse->assertRedirect(route('admin.buku.index'));
        $this->assertEquals('Buku Uji Coba Diperbarui', $bukuBaru->fresh()->judul);

        // Admin hapus buku
        $deleteResponse = $this->actingAs($admin)->delete('/admin/buku/' . $bukuBaru->id);
        $deleteResponse->assertRedirect(route('admin.buku.index'));
        $this->assertDatabaseMissing('bukus', ['id' => $bukuBaru->id]);
    }
}
