<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_login_with_valid_credentials_redirects_to_admin(): void
    {
        $user = $this->makeAdmin();

        $response = $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password123']);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $this->makeAdmin();

        $response = $this->post('/login', ['email' => 'admin@example.com', 'password' => 'wrong']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_session_times_out_after_inactivity(): void
    {
        $user = $this->makeAdmin();
        SiteSetting::set('session_timeout_minutes', '5');

        $this->actingAs($user)
            ->withSession(['_last_activity' => now()->subMinutes(10)->timestamp])
            ->get('/admin')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
