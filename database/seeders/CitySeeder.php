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
    'Casablanca' => 'https://www.visitmorocco.com/sites/default/files/styles/thumbnail_events_slider/public/thumbnails/image/city-panorama.-casablanca-morocco.-africa-marianna-ianovska.jpg?itok=h4FjZSIp', // Hassan II Mosque
    'Rabat' => 'https://images.unsplash.com/photo-1596489397635-43ea560934e8?auto=format&fit=crop&q=80&w=800&h=600', // Hassan Tower
    'Marrakech' => 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&q=80&w=800&h=600', // Koutoubia
    'Fes' => 'https://images.unsplash.com/photo-1563294315-08e89578297b?auto=format&fit=crop&q=80&w=800&h=600', // Tanneries
    'Tangier' => 'https://images.unsplash.com/photo-1561571900-34812fcc4d5b?auto=format&fit=crop&q=80&w=800&h=600', // Night Port
    'Agadir' => 'https://images.unsplash.com/photo-1550972353-8d0fb053426e?auto=format&fit=crop&q=80&w=800&h=600', // Beach
    'Meknes' => 'https://images.unsplash.com/photo-1563459733-5b878239e248?auto=format&fit=crop&q=80&w=800&h=600', // Bab Mansour
    'Oujda' => 'https://images.unsplash.com/photo-1627998632616-5634db84c010?auto=format&fit=crop&q=80&w=800&h=600', // Clock Tower
    'Essaouira' => 'https://images.unsplash.com/photo-1594140024419-f53e34bca821?auto=format&fit=crop&q=80&w=800&h=600', // Blue Boats
    'Chefchaouen' => 'https://images.unsplash.com/photo-1536043941541-f7032179929f?auto=format&fit=crop&q=80&w=800&h=600', // Blue Streets
    'Tetouan' => 'https://images.unsplash.com/photo-1594140024213-f53e34bca82e?auto=format&fit=crop&q=80&w=800&h=600', // White Dove
    'El Jadida' => 'https://images.unsplash.com/photo-1610484724210-249591465e90?auto=format&fit=crop&q=80&w=800&h=600', // Citadel
    'Nador' => 'https://images.unsplash.com/photo-1634563819851-bc000490b6a1?auto=format&fit=crop&q=80&w=800&h=600', // Grand Mosque
    'Dakhla' => 'https://images.unsplash.com/photo-1594140024103-f53e34bca82f?auto=format&fit=crop&q=80&w=800&h=600', // Lagoon
    'Laayoune' => 'https://images.unsplash.com/photo-1582260641199-ab192a54b38d?auto=format&fit=crop&q=80&w=800&h=600', // Shipwreck
    'Taroudant' => 'https://images.unsplash.com/photo-1594140024225-f53e34bca830?auto=format&fit=crop&q=80&w=800&h=600' // Red Walls
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
