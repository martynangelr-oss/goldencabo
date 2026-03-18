<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Zones & Hotels ──────────────────────────────────────────
        $zonesData = [
            [
                'number'           => 1,
                'name'             => 'San José del Cabo',
                'area'             => 'San José del Cabo',
                'round_trip_price' => 100,
                'one_way_price'    => 60,
                'travel_time'      => '15 min',
                'is_active'        => true,
                'sort_order'       => 1,
                'hotels' => [
                    'Hyatt Ziva Los Cabos', 'One&Only Palmilla', 'Casa Dorada Los Cabos',
                    'El Ganzo Hotel', 'Hilton Los Cabos', 'Barceló Gran Faro',
                    'Hacienda del Mar', 'Westin Los Cabos Resort', 'Las Ventanas al Paraíso', 'Marquis Los Cabos',
                ],
            ],
            [
                'number'           => 2,
                'name'             => 'Corredor Turístico',
                'area'             => 'Corredor Turístico',
                'round_trip_price' => 120,
                'one_way_price'    => 65,
                'travel_time'      => '30 min',
                'is_active'        => true,
                'sort_order'       => 2,
                'hotels' => [
                    'Grand Velas Los Cabos', 'Esperanza Auberge Resort', 'Pueblo Bonito Sunset Beach',
                    'RIU Palace Los Cabos', 'Hard Rock Hotel Cabo', 'ME Cabo',
                    'Paradisus Los Cabos', 'Vidanta Los Cabos', 'The Cape Thompson', 'Fiesta Americana Grand',
                ],
            ],
            [
                'number'           => 3,
                'name'             => 'Cabo San Lucas',
                'area'             => 'Cabo San Lucas',
                'round_trip_price' => 140,
                'one_way_price'    => 75,
                'travel_time'      => '45 min',
                'is_active'        => true,
                'sort_order'       => 3,
                'hotels' => [
                    'Breathless Cabo San Lucas', 'Cabo Villas Beach Resort', 'Dreams Los Cabos',
                    'Holiday Inn Resort Cabo', 'Hotel Finisterra', 'Meliá Cabo Real',
                    'Playa Grande Resort & Spa', 'Sandos Finisterra', 'Sirena del Mar', 'Villa del Palmar',
                ],
            ],
            [
                'number'           => 4,
                'name'             => 'Lado del Pacífico',
                'area'             => 'Lado del Pacífico',
                'round_trip_price' => 180,
                'one_way_price'    => 100,
                'travel_time'      => '60 min',
                'is_active'        => true,
                'sort_order'       => 4,
                'hotels' => [
                    'Todos Santos Inn', 'Hotel California', 'Guaycura Boutique Hotel',
                    'Posada La Poza', 'The Hotelito', 'Casa Bentley Hotel',
                    'Rancho Pescadero', 'Cerritos Beach Inn', 'Misión Catavina', 'Hacienda Todos Santos',
                ],
            ],
        ];

        foreach ($zonesData as $zData) {
            $hotels = $zData['hotels'];
            unset($zData['hotels']);

            $zone = Zone::firstOrCreate(['number' => $zData['number']], $zData);

            foreach ($hotels as $i => $hotelName) {
                Hotel::firstOrCreate(
                    ['zone_id' => $zone->id, 'name' => $hotelName],
                    ['is_active' => true, 'sort_order' => $i]
                );
            }
        }

        // ── Vehicles ─────────────────────────────────────────────────
        $vehicles = [
            [
                'name'         => 'Chevrolet Suburban LTZ',
                'description'  => 'SUV de lujo con amplio espacio para pasajeros y equipaje.',
                'services'     => ['A/C Premium', 'WiFi a bordo', 'Maletero amplio', 'USB / 12V'],
                'passengers'   => 5,
                'is_available' => true,
                'image_path'   => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&q=85',
                'sort_order'   => 1,
            ],
            [
                'name'         => 'Toyota Hiace Van',
                'description'  => 'Van espaciosa ideal para grupos medianos.',
                'services'     => ['A/C Doble zona', 'WiFi a bordo', 'Audio premium', 'Gran maletero'],
                'passengers'   => 9,
                'is_available' => true,
                'image_path'   => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=85',
                'sort_order'   => 2,
            ],
            [
                'name'         => 'Mercedes-Benz Sprinter VIP',
                'description'  => 'Experiencia ejecutiva para grupos con servicio premium.',
                'services'     => ['Asientos ejecutivos', 'Clima VIP', 'WiFi alta velocidad', 'Minibar'],
                'passengers'   => 12,
                'is_available' => true,
                'image_path'   => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=85',
                'sort_order'   => 3,
            ],
        ];

        foreach ($vehicles as $v) {
            Vehicle::firstOrCreate(['name' => $v['name']], $v);
        }

        // ── Tours ─────────────────────────────────────────────────────
        $tours = [
            [
                'name'              => 'Recorrido a La Paz',
                'duration'          => '10 horas',
                'route_description' => 'Transporte privado con visita al Trópico de Cáncer, Buena Vista, Los Barriles, San Antonio, El Triunfo y La Paz. Incluye Playa Balandra, el Malecón, demostración de perlas y tiempo libre.',
                'destinations'      => ['Playa Balandra', 'Trópico de Cáncer', 'El Triunfo', 'El Malecón', 'Todos Santos'],
                'price_usd'         => 420,
                'is_active'         => true,
                'image_path'        => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=85',
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Recorrido Todos Santos',
                'duration'          => '5 horas',
                'route_description' => 'Viaje redondo con vista panorámica al Océano Pacífico. Galerías de arte, Misión Jesuita, Teatro del Pueblo, Hotel California y Playa Cerritos.',
                'destinations'      => ['Hotel California', 'Vista Pacífico', 'Misión Jesuita', 'Arte & Galerías', 'Cerritos'],
                'price_usd'         => 300,
                'is_active'         => true,
                'image_path'        => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800&q=85',
                'sort_order'        => 2,
            ],
        ];

        foreach ($tours as $t) {
            Tour::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}
