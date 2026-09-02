<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudBukuTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->user = User::where('role', 'user')->first();
    }

    public function test_admin_can_view_buku_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.buku.index'));

        $response->assertStatus(200);
        $response->assertSee('BK-001');
        $response->assertSee('Pemrograman Web Laravel Dasar');
    }

    public function test_non_admin_cannot_view_buku_index()
    {
        $response = $this->actingAs($this->user)->get(route('admin.buku.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_create_buku_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.buku.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Buku Baru');
    }

    public function test_admin_can_store_new_buku()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.buku.store'), [
            'kode_buku' => 'BK-003',
            'judul' => 'Mastering Vue 3',
            'pengarang' => 'Evan You',
            'penerbit' => 'Tech Pub',
            'stok' => 10,
        ]);

        $response->assertRedirect(route('admin.buku.index'));
        $response->assertSessionHas('success', 'Data buku berhasil ditambahkan.');

        $this->assertDatabaseHas('bukus', [
            'kode_buku' => 'BK-003',
            'judul' => 'Mastering Vue 3',
        ]);
    }

    public function test_admin_can_view_edit_buku_form()
    {
        $buku = Buku::first();

        $response = $this->actingAs($this->admin)->get(route('admin.buku.edit', $buku->id));

        $response->assertStatus(200);
        $response->assertSee($buku->kode_buku);
    }

    public function test_admin_can_update_buku()
    {
        $buku = Buku::first();

        $response = $this->actingAs($this->admin)->put(route('admin.buku.update', $buku->id), [
            'kode_buku' => $buku->kode_buku,
            'judul' => 'Judul Buku Updated',
            'pengarang' => 'Pengarang Updated',
            'penerbit' => 'Penerbit Updated',
            'stok' => 20,
        ]);

        $response->assertRedirect(route('admin.buku.index'));
        $response->assertSessionHas('success', 'Data buku berhasil diperbarui.');

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'judul' => 'Judul Buku Updated',
            'stok' => 20,
        ]);
    }

    public function test_admin_can_delete_buku()
    {
        $buku = Buku::first();

        $response = $this->actingAs($this->admin)->delete(route('admin.buku.destroy', $buku->id));

        $response->assertRedirect(route('admin.buku.index'));
        $response->assertSessionHas('success', 'Data buku berhasil dihapus.');

        $this->assertDatabaseMissing('bukus', [
            'id' => $buku->id,
        ]);
    }
}
