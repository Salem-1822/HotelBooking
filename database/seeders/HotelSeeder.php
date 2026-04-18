<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $casablanca = City::where('name', 'Casablanca')->first();
        $marrakech = City::where('name', 'Marrakech')->first();
        $rabat = City::where('name', 'Rabat')->first();
        $fes = City::where('name', 'Fes')->first();
        $tangier = City::where('name', 'Tangier')->first();
        $agadir = City::where('name', 'Agadir')->first();

        $hotels = [
            // Marrakech
            [
                'city_id' => $marrakech->id,
                'name' => 'Royal Mansour Marrakech',
                'description' => 'Un rve de palais au cur de la ville rouge.',
                'address' => 'Abou Abbas El Sebti, Marrakech',
                'price_per_night' => 1500.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1541416395460-a548325a809b?q=80&w=800'
            ],
            [
                'city_id' => $marrakech->id,
                'name' => 'Riad El Fenn',
                'description' => 'Boutique rad color au cur de la mdina.',
                'address' => 'Derb Moulay Abdallah Ben Hezzian, Marrakech',
                'price_per_night' => 850.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?q=80&w=800'
            ],
            // Casablanca
            [
                'city_id' => $casablanca->id,
                'name' => 'Four Seasons Casablanca',
                'description' => 'Luxe moderne face  l\'ocan Atlantique.',
                'address' => 'Boulevard de la Corniche, Casablanca',
                'price_per_night' => 1200.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1563294315-08e89578297b?q=80&w=800'
            ],
            [
                'city_id' => $casablanca->id,
                'name' => 'Kenzi Tower Hotel',
                'description' => 'Vue panoramique sur toute la capitale conomique.',
                'address' => 'Twin Center, Casablanca',
                'price_per_night' => 700.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1561571900-34812fcc4d5b?q=80&w=800'
            ],
            // Fes
            [
                'city_id' => $fes->id,
                'name' => 'Palais Faraj Suites & Spa',
                'description' => 'Une vue imprenable sur la plus vieille mdina du monde.',
                'address' => 'Bab Ziat, Fes',
                'price_per_night' => 550.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1563294315-08e89578297b?q=80&w=800'
            ],
            [
                'city_id' => $fes->id,
                'name' => 'Riad Medina Fes',
                'description' => 'Authenticit et tradition fassie.',
                'address' => 'Derb el Mernissi, Fes',
                'price_per_night' => 300.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1536043941541-f7032179929f?q=80&w=800'
            ],
            // Agadir
            [
                'city_id' => $agadir->id,
                'name' => 'Sofitel Agadir Thalassa sea & spa',
                'description' => 'Dtente et bien-tre au bord de la plage.',
                'address' => 'Baie des palmiers, Agadir',
                'price_per_night' => 900.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1550972353-8d0fb053426e?q=80&w=800'
            ],
            [
                'city_id' => $agadir->id,
                'name' => 'Ocean View Agadir',
                'description' => 'Htel familial avec vue sur mer.',
                'address' => 'Secteur Touristique, Agadir',
                'price_per_night' => 450.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1563459733-5b878239e248?q=80&w=800'
            ]
        ];

        foreach ($hotels as $hotelData) {
            Hotel::create($hotelData);
        }
    }
}
