<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRoomViewDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Hotel $hotel;
    protected City $city;
    protected Room $roomWithRes;
    protected Room $roomEmpty;
    protected Room $roomMultipleRes;
    protected User $client;
    protected Customer $customer;
    protected Reservation $activeReservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create(['name' => 'Tangier', 'slug' => 'tangier']);

        $this->hotel = Hotel::create([
            'name'            => 'Grand Hotel Tanger',
            'city_id'         => $this->city->id,
            'address'         => '10 Boulevard Pasteur',
            'phone'           => '+212 5 39 11 22 33',
            'email'           => 'contact@grandhoteltanger.ma',
            'price_per_night' => 1200,
            'star_rating'     => 4,
            'status'          => 'active',
        ]);

        $this->admin = Admin::create([
            'name'       => 'Samir Admin',
            'email'      => 'samir.admin@hotelbooking.com',
            'phone'      => '+212 6 10 20 30 40',
            'password'   => Hash::make('AdminSecret123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->hotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->client = User::create([
            'name'     => 'Yassine Alami',
            'email'    => 'yassine.alami@example.com',
            'phone'    => '+212 6 99 88 77 66',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
        ]);

        $this->customer = Customer::create([
            'hotel_id' => $this->hotel->id,
            'name'     => 'Yassine Alami',
            'phone'    => '+212 6 99 88 77 66',
            'email'    => 'yassine.alami@example.com',
        ]);

        // Room 1: Has an active reservation
        $this->roomWithRes = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '101',
            'type'            => 'Deluxe',
            'price_per_night' => 1200,
            'capacity'        => 2,
            'status'          => 'occupied',
        ]);

        $this->activeReservation = Reservation::create([
            'hotel_id'     => $this->hotel->id,
            'room_id'      => $this->roomWithRes->id,
            'user_id'      => $this->client->id,
            'customer_id'  => $this->customer->id,
            'guest_name'   => 'Yassine Alami',
            'guest_phone'  => '+212 6 99 88 77 66',
            'check_in'     => Carbon::yesterday()->toDateString(),
            'check_out'    => Carbon::tomorrow()->toDateString(),
            'guests_count' => 2,
            'total_price'  => 2400,
            'status'       => 'checked_in',
        ]);

        // Room 2: Empty room without any reservations
        $this->roomEmpty = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '102',
            'type'            => 'Single',
            'price_per_night' => 800,
            'capacity'        => 1,
            'status'          => 'available',
        ]);

        // Room 3: Room with multiple reservations (past + active)
        $this->roomMultipleRes = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '103',
            'type'            => 'Suite',
            'price_per_night' => 2500,
            'capacity'        => 4,
            'status'          => 'occupied',
        ]);

        // Past reservation for room 3
        Reservation::create([
            'hotel_id'     => $this->hotel->id,
            'room_id'      => $this->roomMultipleRes->id,
            'user_id'      => $this->client->id,
            'customer_id'  => $this->customer->id,
            'guest_name'   => 'Yassine Alami',
            'guest_phone'  => '+212 6 99 88 77 66',
            'check_in'     => Carbon::now()->subDays(10)->toDateString(),
            'check_out'    => Carbon::now()->subDays(7)->toDateString(),
            'guests_count' => 2,
            'total_price'  => 7500,
            'status'       => 'checked_out',
        ]);

        // Current reservation for room 3
        $customer2 = Customer::create([
            'hotel_id' => $this->hotel->id,
            'name'     => 'Fatima Zahra',
            'phone'    => '+212 6 11 22 33 44',
            'email'    => 'fatima@example.com',
        ]);

        Reservation::create([
            'hotel_id'     => $this->hotel->id,
            'room_id'      => $this->roomMultipleRes->id,
            'customer_id'  => $customer2->id,
            'guest_name'   => 'Fatima Zahra',
            'guest_phone'  => '+212 6 11 22 33 44',
            'check_in'     => Carbon::today()->toDateString(),
            'check_out'    => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 3,
            'total_price'  => 7500,
            'status'       => 'confirmed',
        ]);
    }

    /** 1. Test Room with Active Reservation & Client Details via AJAX endpoint */
    public function test_room_with_active_reservation_returns_client_details_via_ajax(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomWithRes));

        $response->assertOk();
        $response->assertJson([
            'id'          => $this->roomWithRes->id,
            'room_number' => '101',
            'type'        => 'Deluxe',
            'status'      => 'occupied',
        ]);

        $data = $response->json();
        $this->assertNotNull($data['active_reservation']);
        $this->assertEquals('Yassine Alami', $data['active_reservation']['client_name']);
        $this->assertEquals('+212 6 99 88 77 66', $data['active_reservation']['client_phone']);
        $this->assertEquals('yassine.alami@example.com', $data['active_reservation']['client_email']);
        $this->assertEquals(2, $data['active_reservation']['guests_count']);
        $this->assertEquals('checked_in', $data['active_reservation']['status']);
        $this->assertNotEmpty($data['active_reservation']['ref']);
    }

    /** 2. Test Room without reservation returns null active_reservation */
    public function test_room_without_reservation_returns_null_active_reservation(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomEmpty));

        $response->assertOk();
        $data = $response->json();
        $this->assertNull($data['active_reservation']);
        $this->assertEmpty($data['reservations']);
    }

    /** 3. Test Room with multiple reservations returns all reservations and prioritizes active */
    public function test_room_with_multiple_reservations_returns_history_and_prioritizes_active(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomMultipleRes));

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(2, $data['reservations']);
        $this->assertNotNull($data['active_reservation']);
        $this->assertEquals('Fatima Zahra', $data['active_reservation']['client_name']);
        $this->assertEquals('confirmed', $data['active_reservation']['status']);
    }

    /** 4. Test unauthorized hotel cannot view other hotel's room details */
    public function test_admin_cannot_view_other_hotels_room(): void
    {
        $otherHotel = Hotel::create([
            'name'            => 'Atlas Resort',
            'city_id'         => $this->city->id,
            'address'         => 'Route de l\'Ourika',
            'phone'           => '+212 5 24 00 11 22',
            'email'           => 'contact@atlasresort.ma',
            'price_per_night' => 2000,
            'star_rating'     => 5,
            'status'          => 'active',
        ]);

        $otherRoom = Room::create([
            'hotel_id'        => $otherHotel->id,
            'room_number'     => '999',
            'type'            => 'Suite',
            'price_per_night' => 3000,
            'capacity'        => 2,
            'status'          => 'available',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $otherRoom));

        $response->assertForbidden();
    }

    /** 5. Test Rooms Page HTML renders correctly across all three locales (EN, FR, AR) */
    public function test_rooms_page_renders_with_client_details_support_in_all_locales(): void
    {
        $locales = ['en', 'fr', 'ar'];

        foreach ($locales as $locale) {
            $response = $this->actingAs($this->admin, 'admin')
                ->withSession(['locale' => $locale])
                ->get(route('admin.rooms.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Modal elements exist
            $response->assertSee('id="viewRoomModal"', false);
            $response->assertSee('id="viewReservationSection"', false);
            $response->assertSee('id="viewClientName"', false);
            $response->assertSee('id="viewClientPhone"', false);
            $response->assertSee('id="viewClientEmail"', false);
            $response->assertSee('id="viewNoReservation"', false);
            $response->assertSee('id="viewMultipleReservationsSection"', false);

            // Script contains global status translations and reservation status translations
            $response->assertSee('const statusTranslations =', false);
            $response->assertSee('const resStatusTranslations =', false);
            $response->assertSee('function renderRoomReservationDetails', false);

            // Client data is embedded in window.rooms for instant rendering
            $response->assertSee('Yassine Alami');

            // No raw translation keys
            $this->assertDoesNotMatchRegularExpression(
                '/(?<!@)\b(admin|admin_nav|booking|common)\.[a-z_0-9]+/',
                $content,
                "Found raw translation key on rooms index in locale {$locale}"
            );

            // Locale specific dir
            if ($locale === 'ar') {
                $response->assertSee('dir="rtl"', false);
            } else {
                $response->assertSee('dir="ltr"', false);
            }
        }
    }
}
