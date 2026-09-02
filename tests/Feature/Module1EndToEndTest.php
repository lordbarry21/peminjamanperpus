<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Module1EndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_seeded_users_and_books_exist()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'admin@perpus.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'siswa@perpus.com',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('bukus', [
            'kode_buku' => 'BK-001',
            'judul' => 'Pemrograman Web Laravel Dasar',
        ]);

        $this->assertDatabaseHas('bukus', [
            'kode_buku' => 'BK-002',
            'judul' => 'Belajar Basis Data MySQL untuk Pemula',
        ]);
    }

    public function test_admin_login_redirects_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@perpus.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticated();
    }

    public function test_siswa_login_redirects_to_user_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'siswa@perpus.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    public function test_new_user_registration_defaults_role_to_user()
    {
        $response = $this->post('/register', [
            'name' => 'Siswa Baru',
            'email' => 'siswabaru@perpus.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'siswabaru@perpus.com',
            'role' => 'user',
        ]);
    }
}
