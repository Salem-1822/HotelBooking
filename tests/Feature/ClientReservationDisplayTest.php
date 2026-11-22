<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class ClientReservationDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Room $room;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Atlas Luxury Palace',
            'description' => 'A luxury stay in the heart of Marrakech.',
            'address' => 'Boulevard Mohamed VI',
            'stars' => 5,
            'price_per_night' => 1000,
            'status' => 'active',
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'type' => 'Deluxe Suite',
            'capacity' => 6,
            'price_per_night' => 1000,
            'status' => 'available',
        ]);

        $this->client = User::create([
            'name' => 'John Doe',
            'email' => 'client@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'client',
            'status' => 'active',
        ]);
    }

    private function createReservation(int $guestsCount, int $nightCount): Reservation
    {
        $checkIn = Carbon::now()->addDays(5)->startOfDay();
        $checkOut = $checkIn->copy()->addDays($nightCount);

        return Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guest_email' => $this->client->email,
            'guest_phone' => '0612345678',
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'guests_count' => $guestsCount,
            'total_price' => $nightCount * $this->room->price_per_night,
            'status' => 'confirmed',
        ]);
    }

    #[Test]
    public function scenario_1_displays_1_guest_and_2_nights_correctly_without_duplication(): void
    {
        $reservation = $this->createReservation(guestsCount: 1, nightCount: 2);

        // 1. Client reservation details page (show)
        $showResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.show', $reservation));

        $showResponse->assertStatus(200);
        $showHtml = $showResponse->getContent();

        // Must display "2 nights" and "1 Guest"
        $this->assertStringContainsString('2 nights', $showHtml);
        $this->assertStringContainsString('1 Guest', $showHtml);

        // Must NOT contain duplicate/concatenated counts
        $this->assertStringNotContainsString('2 2 nights', $showHtml);
        $this->assertStringNotContainsString('22 nights', $showHtml);
        $this->assertStringNotContainsString('1 1 Guest', $showHtml);
        $this->assertStringNotContainsString('11 guests', $showHtml);
        $this->assertStringNotContainsString('1 1 guest', $showHtml);

        // 2. Client reservations listing page (index)
        $indexResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.index'));

        $indexResponse->assertStatus(200);
        $indexHtml = $indexResponse->getContent();

        $this->assertStringContainsString('2 nights', $indexHtml);
        $this->assertStringNotContainsString('2 2 nights', $indexHtml);
        $this->assertStringNotContainsString('22 nights', $indexHtml);

        // 3. Client reservation confirmation page
        $confirmResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.confirmation', $reservation));

        $confirmResponse->assertStatus(200);
        $confirmHtml = $confirmResponse->getContent();

        $this->assertStringContainsString('2 nights', $confirmHtml);
        $this->assertStringContainsString('1 Guest', $confirmHtml);
        $this->assertStringNotContainsString('2 2 nights', $confirmHtml);
        $this->assertStringNotContainsString('22 nights', $confirmHtml);
        $this->assertStringNotContainsString('1 1 Guest', $confirmHtml);
        $this->assertStringNotContainsString('11 guests', $confirmHtml);
    }

    #[Test]
    public function scenario_2_displays_2_guests_and_3_nights_correctly_without_duplication(): void
    {
        $reservation = $this->createReservation(guestsCount: 2, nightCount: 3);

        // 1. Client reservation details page (show)
        $showResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.show', $reservation));

        $showResponse->assertStatus(200);
        $showHtml = $showResponse->getContent();

        $this->assertStringContainsString('3 nights', $showHtml);
        $this->assertStringContainsString('2 Guests', $showHtml);

        $this->assertStringNotContainsString('3 3 nights', $showHtml);
        $this->assertStringNotContainsString('33 nights', $showHtml);
        $this->assertStringNotContainsString('2 2 Guests', $showHtml);
        $this->assertStringNotContainsString('22 guests', $showHtml);
        $this->assertStringNotContainsString('2 2 guests', $showHtml);

        // 2. Client reservations listing page (index)
        $indexResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.index'));

        $indexResponse->assertStatus(200);
        $indexHtml = $indexResponse->getContent();

        $this->assertStringContainsString('3 nights', $indexHtml);
        $this->assertStringNotContainsString('3 3 nights', $indexHtml);
        $this->assertStringNotContainsString('33 nights', $indexHtml);

        // 3. Client reservation confirmation page
        $confirmResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.confirmation', $reservation));

        $confirmResponse->assertStatus(200);
        $confirmHtml = $confirmResponse->getContent();

        $this->assertStringContainsString('3 nights', $confirmHtml);
        $this->assertStringContainsString('2 Guests', $confirmHtml);
        $this->assertStringNotContainsString('3 3 nights', $confirmHtml);
        $this->assertStringNotContainsString('33 nights', $confirmHtml);
        $this->assertStringNotContainsString('2 2 Guests', $confirmHtml);
        $this->assertStringNotContainsString('22 guests', $confirmHtml);
    }

    #[Test]
    public function scenario_3_displays_4_guests_and_5_nights_correctly_without_duplication(): void
    {
        $reservation = $this->createReservation(guestsCount: 4, nightCount: 5);

        // 1. Client reservation details page (show)
        $showResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.show', $reservation));

        $showResponse->assertStatus(200);
        $showHtml = $showResponse->getContent();

        $this->assertStringContainsString('5 nights', $showHtml);
        $this->assertStringContainsString('4 Guests', $showHtml);

        $this->assertStringNotContainsString('5 5 nights', $showHtml);
        $this->assertStringNotContainsString('55 nights', $showHtml);
        $this->assertStringNotContainsString('4 4 Guests', $showHtml);
        $this->assertStringNotContainsString('44 guests', $showHtml);
        $this->assertStringNotContainsString('4 4 guests', $showHtml);

        // 2. Client reservations listing page (index)
        $indexResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.index'));

        $indexResponse->assertStatus(200);
        $indexHtml = $indexResponse->getContent();

        $this->assertStringContainsString('5 nights', $indexHtml);
        $this->assertStringNotContainsString('5 5 nights', $indexHtml);
        $this->assertStringNotContainsString('55 nights', $indexHtml);

        // 3. Client reservation confirmation page
        $confirmResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.confirmation', $reservation));

        $confirmResponse->assertStatus(200);
        $confirmHtml = $confirmResponse->getContent();

        $this->assertStringContainsString('5 nights', $confirmHtml);
        $this->assertStringContainsString('4 Guests', $confirmHtml);
        $this->assertStringNotContainsString('5 5 nights', $confirmHtml);
        $this->assertStringNotContainsString('55 nights', $confirmHtml);
        $this->assertStringNotContainsString('4 4 Guests', $confirmHtml);
        $this->assertStringNotContainsString('44 guests', $confirmHtml);
    }

    #[Test]
    public function multilingual_renders_correct_guest_and_night_counts_in_french_and_arabic(): void
    {
        $reservation = $this->createReservation(guestsCount: 2, nightCount: 3);

        // French
        $showResponseFr = $this->withSession(['locale' => 'fr'])
            ->actingAs($this->client, 'web')
            ->get(route('client.reservations.show', $reservation));
        $showResponseFr->assertStatus(200);
        $htmlFr = $showResponseFr->getContent();

        $this->assertStringContainsString('3 nuits', $htmlFr);
        $this->assertStringContainsString('2 Voyageurs', $htmlFr);
        $this->assertStringNotContainsString('3 3 nuits', $htmlFr);
        $this->assertStringNotContainsString('2 2 Voyageurs', $htmlFr);

        // Arabic
        $showResponseAr = $this->withSession(['locale' => 'ar'])
            ->actingAs($this->client, 'web')
            ->get(route('client.reservations.show', $reservation));
        $showResponseAr->assertStatus(200);
        $htmlAr = $showResponseAr->getContent();

        $expectedNightsAr = trans_choice('client.nights', 3, [], 'ar');
        $expectedGuestsAr = trans_choice('hotel.guest_count', 2, [], 'ar');

        $this->assertStringContainsString($expectedNightsAr, $htmlAr);
        $this->assertStringContainsString($expectedGuestsAr, $htmlAr);
        $this->assertStringNotContainsString('3 ' . $expectedNightsAr, $htmlAr);
        $this->assertStringNotContainsString('2 ' . $expectedGuestsAr, $htmlAr);
    }

    #[Test]
    public function hotel_show_page_renders_guest_and_night_count_correctly_when_filtered(): void
    {
        $checkIn = Carbon::now()->addDays(2)->format('Y-m-d');
        $checkOut = Carbon::now()->addDays(5)->format('Y-m-d'); // 3 nights

        $response = $this->get(route('hotels.show', [
            $this->hotel,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]));

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('(3 nights)', $html);
        $this->assertStringContainsString('2 Guests', $html);
        $this->assertStringNotContainsString('(3 3 nights)', $html);
        $this->assertStringNotContainsString('(33 nights)', $html);
        $this->assertStringNotContainsString('2 2 Guests', $html);
        $this->assertStringNotContainsString('22 Guests', $html);
    }
}
