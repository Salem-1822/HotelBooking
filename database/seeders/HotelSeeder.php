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
        $tangier = City::where('name', 'Tangier')->first();

        $hotels = [
            [
                'city_id' => $casablanca->id,
                'name' => 'Royal Mansour Casablanca',
                'address' => '27 Av. de l\'Armée Royale, Casablanca',
                'status' => 'active',
            ],
            [
                'city_id' => $marrakech->id,
                'name' => 'La Mamounia',
                'address' => 'Avenue Bab Jdid, Marrakech',
                'status' => 'active',
            ],
            [
                'city_id' => $marrakech->id,
                'name' => 'Amanjena',
                'address' => 'Route de Ouarzazate, Marrakech',
                'status' => 'active',
            ],
            [
                'city_id' => $rabat->id,
                'name' => 'Sofitel Rabat Jardin des Roses',
                'address' => 'Impasse Souissi, Rabat',
                'status' => 'active',
            ],
            [
                'city_id' => $tangier->id,
                'name' => 'Hilton Tangier Al Houara',
                'address' => 'Km 19.8 Route Nationale, Tangier',
                'status' => 'active',
            ]
        ];

        foreach ($hotels as $hotelData) {
            Hotel::create($hotelData);
        }
    }
}
