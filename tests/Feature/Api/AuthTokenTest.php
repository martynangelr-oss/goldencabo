<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_receives_a_token_with_all_abilities(): void
    {
        User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('secret123'),
            'role' => 'admin', 'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'admin@example.com', 'password' => 'secret123',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'abilities', 'expires_at']);
        $this->assertContains('vehicles', $response->json('abilities'));
        $this->assertContains('bookings', $response->json('abilities'));
    }

    public function test_editor_receives_a_token_scoped_to_their_permissions(): void
    {
        User::create([
            'name' => 'Editor', 'email' => 'editor@example.com', 'password' => Hash::make('secret123'),
            'role' => 'editor', 'is_active' => true,
            'permissions' => ['vehicles' => true, 'tours' => false, 'bookings' => true],
        ]);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'editor@example.com', 'password' => 'secret123',
        ]);

        $response->assertOk();
        $abilities = $response->json('abilities');
        $this->assertContains('vehicles', $abilities);
        $this->assertContains('bookings', $abilities);
        $this->assertNotContains('tours', $abilities);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('secret123'),
            'role' => 'admin', 'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'admin@example.com', 'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_inactive_user_cannot_get_a_token(): void
    {
        User::create([
            'name' => 'Disabled', 'email' => 'disabled@example.com', 'password' => Hash::make('secret123'),
            'role' => 'admin', 'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'disabled@example.com', 'password' => 'secret123',
        ]);

        $response->assertStatus(401);
    }
}
