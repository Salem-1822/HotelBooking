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
            'Casablanca' => 'https://source.unsplash.com/800x600/?casablanca,morocco',
            'Rabat' => 'https://source.unsplash.com/800x600/?rabat,morocco',
            'Marrakech' => 'https://source.unsplash.com/800x600/?marrakech,morocco',
            'Fes' => 'https://source.unsplash.com/800x600/?fes,morocco',
            'Tangier' => 'https://source.unsplash.com/800x600/?tangier,morocco',
            'Agadir' => 'https://source.unsplash.com/800x600/?agadir,morocco',
            'Meknes' => 'https://source.unsplash.com/800x600/?meknes,morocco',
            'Oujda' => 'https://source.unsplash.com/800x600/?oujda,morocco',
            'Essaouira' => 'https://source.unsplash.com/800x600/?essaouira,morocco',
            'Chefchaouen' => 'https://source.unsplash.com/800x600/?chefchaouen,morocco',
            'Tetouan' => 'https://source.unsplash.com/800x600/?tetouan,morocco',
            'El Jadida' => 'https://source.unsplash.com/800x600/?eljadida,morocco',
            'Nador' => 'https://source.unsplash.com/800x600/?nador,morocco',
            'Dakhla' => 'https://source.unsplash.com/800x600/?dakhla,morocco',
            'Laayoune' => 'https://source.unsplash.com/800x600/?laayoune,morocco',
            'Taroudant' => 'https://source.unsplash.com/800x600/?taroudant,morocco'
        ];

        foreach ($cities as $cityName => $image) {
            City::create([
                'name' => $cityName,
                'slug' => Str::slug($cityName),
                'image' => $image,
            ]);
        }
    }
}
