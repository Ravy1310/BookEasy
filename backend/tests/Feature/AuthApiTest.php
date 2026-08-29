<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// helper: buat admin, return user
function createAdmin(): User
{
    return User::factory()->create(['password' => bcrypt('password')]);
}

test('login berhasil dengan kredensial valid', function () {
    $user = createAdmin();

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonStructure([
                 'data' => ['token', 'user'],
             ]);
});

test('login gagal jika password salah', function () {
    $user = createAdmin();

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'salah',
    ]);

    $response->assertStatus(401)
             ->assertJsonPath('success', false);
});

test('login gagal jika email tidak terdaftar', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'tidak@ada.com',
        'password' => 'password',
    ]);

    $response->assertStatus(401)
             ->assertJsonPath('success', false);
});

test('login return 422 jika field wajib kosong', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'password']);
});

test('logout berhasil jika terautentikasi', function () {
    $user = createAdmin();

    $login = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $token = $login->json('data.token');

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->postJson('/api/auth/logout');

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonPath('message', 'Logged out');

    // pastikan token sudah dihapus dari database
    expect($user->tokens()->count())->toBe(0);
});

test('logout gagal jika tidak ada token', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(401);
});
