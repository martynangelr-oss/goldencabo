<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VehicleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_with_vehicles_ability_can_list_and_create(): void
    {
        $user = User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret123',
            'role' => 'admin', 'is_active' => true,
        ]);
        Sanctum::actingAs($user, ['vehicles']);

        Vehicle::create(['name' => 'Sprinter', 'passengers' => 12, 'is_available' => true, 'sort_order' => 1]);

        $this->getJson('/api/v1/vehicles')->assertOk()->assertJsonCount(1, 'data');

        $this->postJson('/api/v1/vehicles', [
            'name' => 'Toyota Hiace', 'passengers' => 9, 'is_available' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('vehicles', ['name' => 'Toyota Hiace']);
    }

    public function test_token_without_vehicles_ability_is_forbidden(): void
    {
        $user = User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret123',
            'role' => 'admin', 'is_active' => true,
        ]);
        Sanctum::actingAs($user, ['tours']);

        $this->getJson('/api/v1/vehicles')->assertForbidden();
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->getJson('/api/v1/vehicles')->assertUnauthorized();
    }
}
