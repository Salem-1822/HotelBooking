<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR'); // Using French locale for common Moroccan-style names/format
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            return;
        }

        $moroccanNames = [
            'Ahmed Alami', 'Fatima Zahra Mansouri', 'Youssef Bennani', 'Khadija El Idrissi',
            'Mohamed Chraibi', 'Zineb Lahlou', 'Omar Tazi', 'Meryem Belkhayat',
            'Hassan Berrada', 'Soukaina Filali', 'Anas Mezouar', 'Salma Guessous',
            'Simohamed Sijilmassi', 'Noura Skali', 'Adnane Benjelloun', 'Malika El Amrani'
        ];

        for ($i = 0; $i < 50; $i++) {
            $hotel = $hotels->random();
            $checkIn = $faker->dateTimeBetween('now', '+2 months');
            $nights = $faker->numberBetween(1, 7);
            
            // Clone checkIn to modify it correctly
            $checkOut = (clone $checkIn)->modify('+' . $nights . ' days');
            
            Reservation::create([
                'hotel_id' => $hotel->id,
                'guest_name' => $faker->randomElement($moroccanNames),
                'guest_phone' => '+212 ' . $faker->numberBetween(6, 7) . ' ' . $faker->numerify('## ## ## ##'),
                'guests_count' => $faker->numberBetween(1, 5),
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'total_price' => $hotel->price_per_night * $nights,
                'status' => $faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            ]);
        }
    }
}
