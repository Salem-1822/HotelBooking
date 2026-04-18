<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Casablanca' => 'https://images.unsplash.com/photo-1541416395460-a548325a809b?q=80&w=800',
            'Rabat' => 'https://images.unsplash.com/photo-1596489397635-43ea560934e8?q=80&w=800',
            'Marrakech' => 'https://images.unsplash.com/photo-1548013146-72479768bada?q=80&w=800',
            'Fes' => 'https://images.unsplash.com/photo-1563294315-08e89578297b?q=80&w=800',
            'Tangier' => 'https://images.unsplash.com/photo-1561571900-34812fcc4d5b?q=80&w=800',
            'Agadir' => 'https://images.unsplash.com/photo-1550972353-8d0fb053426e?q=80&w=800',
        ];

        foreach ($cities as $cityName => $image) {
            City::updateOrCreate(
                ['slug' => Str::slug($cityName)],
                [
                    'name' => $cityName,
                    'image' => $image,
                ]
            );
        }
    }
}
