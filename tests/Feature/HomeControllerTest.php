<?php

namespace Tests\Feature;

use App\Models\CarouselSlide;
use App\Models\Tour;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_with_empty_database(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertViewHas('zones');
        $response->assertViewHas('fleet');
        $response->assertViewHas('tours');
    }

    public function test_homepage_renders_with_live_data(): void
    {
        Zone::create([
            'number' => 1, 'name' => 'San José del Cabo', 'area' => 'SJD',
            'round_trip_price' => 100, 'one_way_price' => 60, 'travel_time' => '15 min',
            'is_active' => true, 'sort_order' => 1,
        ]);
        Vehicle::create([
            'name' => 'Toyota Hiace', 'passengers' => 9, 'is_available' => true, 'sort_order' => 1,
        ]);
        Tour::create([
            'name' => 'Recorrido a La Paz', 'duration' => '10 horas', 'price_usd' => 420,
            'price_label' => 'group', 'is_active' => true, 'sort_order' => 1,
        ]);
        CarouselSlide::create(['title' => 'Bienvenido', 'image_path' => 'cms/carousel/test.jpg', 'is_active' => true, 'sort_order' => 1]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('zones', function ($zones) {
            return isset($zones[1]) && $zones[1]['name'] === 'San José del Cabo';
        });
    }
}
