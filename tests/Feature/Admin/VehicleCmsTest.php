<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleCmsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret123'),
            'role' => 'admin', 'is_active' => true,
        ]);
    }

    private function editorWithout(string $permission): User
    {
        $perms = array_fill_keys(
            ['bookings', 'contacts', 'cms', 'vehicles', 'tours', 'zones', 'carousel', 'gallery', 'sections', 'settings'],
            true
        );
        $perms[$permission] = false;

        return User::create([
            'name' => 'Editor', 'email' => 'editor@example.com', 'password' => bcrypt('secret123'),
            'role' => 'editor', 'permissions' => $perms, 'is_active' => true,
        ]);
    }

    public function test_admin_can_list_and_create_vehicle(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.cms.vehicles.index'))->assertOk();

        $this->actingAs($admin)->post(route('admin.cms.vehicles.store'), [
            'name' => 'Toyota Hiace', 'passengers' => 9, 'is_available' => '1', 'services' => '',
        ])->assertRedirect(route('admin.cms.vehicles.index'));

        $this->assertDatabaseHas('vehicles', ['name' => 'Toyota Hiace']);
    }

    public function test_admin_can_toggle_and_delete_vehicle(): void
    {
        $admin = $this->admin();
        $vehicle = Vehicle::create(['name' => 'Sprinter', 'passengers' => 12, 'is_available' => true, 'sort_order' => 1]);

        $this->actingAs($admin)->post(route('admin.cms.vehicles.toggle', $vehicle))
            ->assertOk()->assertJson(['is_available' => false]);

        $this->actingAs($admin)->delete(route('admin.cms.vehicles.destroy', $vehicle))
            ->assertRedirect();

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_editor_without_vehicles_permission_gets_403(): void
    {
        $editor = $this->editorWithout('vehicles');

        $this->actingAs($editor)->get(route('admin.cms.vehicles.index'))->assertForbidden();
    }
}
