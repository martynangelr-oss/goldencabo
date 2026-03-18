<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Vehicle;
use App\Models\Tour;
use App\Models\CarouselSlide;
use App\Models\GalleryImage;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $zonesCollection = Zone::with('activeHotels')->active()->orderBy('sort_order')->get();

            if ($zonesCollection->isNotEmpty()) {
                $zones  = $zonesCollection->keyBy('number')->map(fn($z) => [
                    'name'     => $z->name,
                    'round'    => '$' . number_format($z->round_trip_price, 0) . ' USD',
                    'oneway'   => '$' . number_format($z->one_way_price, 0) . ' USD',
                    'time'     => $z->travel_time ?? '',
                    'img_main' => $z->image_url,
                    'img_sec'  => $z->image_secondary_url,
                ])->toArray();

                $hotels = $zonesCollection->keyBy('number')->map(fn($z) => [
                    'zone'   => 'Zona ' . $z->number,
                    'area'   => $z->area ?? $z->name,
                    'hotels' => $z->activeHotels->sortBy('sort_order')->pluck('name')->toArray(),
                ])->toArray();
            } else {
                $zones  = self::getZones();
                $hotels = self::getHotels();
            }

            $vehiclesCollection = Vehicle::available()->orderBy('sort_order')->get();
            $fleet = $vehiclesCollection->isNotEmpty()
                ? $vehiclesCollection->map(fn($v) => [
                    'brand'    => explode(' ', $v->name, 2)[0],
                    'model'    => implode(' ', array_slice(explode(' ', $v->name), 1)) ?: $v->name,
                    'pax'      => $v->passengers,
                    'badge'    => $v->is_available ? 'Disponible' : 'No disponible',
                    'badge_color' => 'teal',
                    'image'    => $v->image_url,
                    'features' => $v->services ?? [],
                ])->toArray()
                : self::getFleet();

            $toursCollection = Tour::active()->orderBy('sort_order')->get();
            $tours = $toursCollection->isNotEmpty()
                ? $toursCollection->map(fn($t) => [
                    'name'        => $t->name,
                    'duration'    => $t->duration ?? '',
                    'price'       => $t->price_formatted,
                    'image'       => $t->image_url,
                    'description' => $t->route_description ?? '',
                    'tags'        => $t->destinations ?? [],
                ])->toArray()
                : self::getTours();

            // Carousel slides
            $slidesCollection = CarouselSlide::active()->orderBy('sort_order')->get();
            $slides = $slidesCollection->map(fn($s) => [
                'title'       => $s->title,
                'subtitle'    => $s->subtitle,
                'image'       => $s->image_url,
                'button_text' => $s->button_text,
                'button_url'  => $s->button_url,
            ])->values()->toArray();

            // Gallery images
            $galleryCollection = GalleryImage::active()->orderBy('sort_order')->get();
            $galleryImages = $galleryCollection->map(fn($g) => [
                'url'     => $g->image_url,
                'caption' => $g->caption ?: 'Los Cabos',
            ])->values()->toArray();

            // Zone images – inject into zones map
            foreach ($zonesCollection as $z) {
                if (isset($zones[$z->number])) {
                    $zones[$z->number]['img_main'] = $z->image_url;
                    $zones[$z->number]['img_sec']  = $z->image_secondary_url;
                }
            }

        } catch (\Exception $e) {
            $zones  = self::getZones();
            $hotels = self::getHotels();
            $fleet  = self::getFleet();
            $tours  = self::getTours();
        }

        // Ensure defaults
        $slides        = $slides        ?? [];
        $galleryImages = $galleryImages ?? [];

        // Section images (Acerca de Nosotros & Servicio Aeropuerto)
        $sectionImages = [
            'about_main'      => SiteSetting::fileUrl('about_img_main')
                                 ?? 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=85',
            'about_secondary' => SiteSetting::fileUrl('about_img_secondary')
                                 ?? 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80',
            'airport_main'    => SiteSetting::fileUrl('airport_img_main')
                                 ?? 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=900&q=85',
        ];

        // Global settings
        $siteLogo    = SiteSetting::fileUrl('logo');
        $siteSettings = [
            'name'            => SiteSetting::get('site_name', 'Golden Cabo Transportation'),
            'tagline'         => SiteSetting::get('site_tagline', 'Traslados privados de lujo'),
            'phone_primary'   => SiteSetting::get('phone_primary', '(+52) 333 303 4455'),
            'phone_secondary' => SiteSetting::get('phone_secondary', '(+52) 624 121 6527'),
            'email'           => SiteSetting::get('email_contact', 'goldencabotransportation@gmail.com'),
            'address'         => SiteSetting::get('address', 'Calle Huanacastle Esq. Eucalipto Mza 70 lte 1, Col. Las Veredas, CP 23436, San José del Cabo, BCS'),
            'whatsapp'        => SiteSetting::get('whatsapp', '+523333034455'),
            'messenger_url'   => SiteSetting::get('messenger_url', ''),
            'logo'            => $siteLogo,
        ];

        return view('home', compact('zones', 'hotels', 'fleet', 'tours', 'slides', 'galleryImages', 'siteSettings', 'sectionImages'));
    }

    // ── Fallback hardcoded data ──────────────────────────────────────

    public static function getZones(): array
    {
        return [
            1 => ['name' => 'San José del Cabo',    'round' => '$100 USD', 'oneway' => '$60 USD',  'time' => '15 min'],
            2 => ['name' => 'Corredor Turístico',   'round' => '$120 USD', 'oneway' => '$65 USD',  'time' => '30 min'],
            3 => ['name' => 'Cabo San Lucas',        'round' => '$140 USD', 'oneway' => '$75 USD',  'time' => '45 min'],
            4 => ['name' => 'Lado del Pacífico',     'round' => '$180 USD', 'oneway' => '$100 USD', 'time' => '60 min'],
        ];
    }

    public static function getHotels(): array
    {
        return [
            1 => ['zone' => 'Zona 1', 'area' => 'San José del Cabo', 'hotels' => [
                'Hyatt Ziva Los Cabos','One&Only Palmilla','Casa Dorada Los Cabos',
                'El Ganzo Hotel','Hilton Los Cabos','Barceló Gran Faro',
                'Hacienda del Mar','Westin Los Cabos Resort','Las Ventanas al Paraíso','Marquis Los Cabos',
            ]],
            2 => ['zone' => 'Zona 2', 'area' => 'Corredor Turístico', 'hotels' => [
                'Grand Velas Los Cabos','Esperanza Auberge Resort','Pueblo Bonito Sunset Beach',
                'RIU Palace Los Cabos','Hard Rock Hotel Cabo','ME Cabo',
                'Paradisus Los Cabos','Vidanta Los Cabos','The Cape Thompson','Fiesta Americana Grand',
            ]],
            3 => ['zone' => 'Zona 3', 'area' => 'Cabo San Lucas', 'hotels' => [
                'Breathless Cabo San Lucas','Cabo Villas Beach Resort','Dreams Los Cabos',
                'Holiday Inn Resort Cabo','Hotel Finisterra','Meliá Cabo Real',
                'Playa Grande Resort & Spa','Sandos Finisterra','Sirena del Mar','Villa del Palmar',
            ]],
            4 => ['zone' => 'Zona 4', 'area' => 'Lado del Pacífico', 'hotels' => [
                'Todos Santos Inn','Hotel California','Guaycura Boutique Hotel',
                'Posada La Poza','The Hotelito','Casa Bentley Hotel',
                'Rancho Pescadero','Cerritos Beach Inn','Misión Catavina','Hacienda Todos Santos',
            ]],
        ];
    }

    public static function getFleet(): array
    {
        return [
            ['brand' => 'Chevrolet', 'model' => 'Suburban LTZ', 'pax' => 5, 'badge' => 'Disponible', 'badge_color' => 'teal',
             'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&q=85',
             'features' => ['❄️ A/C Premium', '📶 WiFi a bordo', '🧳 Maletero amplio', '⚡ USB / 12V']],
            ['brand' => 'Toyota', 'model' => 'Hiace Van', 'pax' => 9, 'badge' => 'Disponible', 'badge_color' => 'teal',
             'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=85',
             'features' => ['❄️ A/C Doble zona', '📶 WiFi a bordo', '🎵 Audio premium', '🧳 Gran maletero']],
            ['brand' => 'Mercedes-Benz', 'model' => 'Sprinter VIP', 'pax' => 12, 'badge' => 'VIP', 'badge_color' => 'gold',
             'image' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=85',
             'features' => ['🌟 Asientos ejecutivos', '❄️ Clima VIP', '📶 WiFi alta velocidad', '🍾 Minibar']],
        ];
    }

    public static function getTours(): array
    {
        return [
            ['name' => 'Recorrido a La Paz', 'duration' => '10 horas', 'price' => '$420 USD',
             'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=85',
             'description' => 'Transporte privado con visita al Trópico de Cáncer, Buena Vista, Los Barriles, San Antonio, El Triunfo y La Paz. Incluye Playa Balandra, el Malecón, demostración de perlas y tiempo libre.',
             'tags' => ['Playa Balandra', 'Trópico de Cáncer', 'El Triunfo', 'El Malecón', 'Todos Santos']],
            ['name' => 'Recorrido Todos Santos', 'duration' => '5 horas', 'price' => '$300 USD',
             'image' => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800&q=85',
             'description' => 'Viaje redondo con vista panorámica al Océano Pacífico. Galerías de arte, Misión Jesuita, Teatro del Pueblo, Hotel California y Playa Cerritos.',
             'tags' => ['Hotel California', 'Vista Pacífico', 'Misión Jesuita', 'Arte & Galerías', 'Cerritos']],
        ];
    }
}
