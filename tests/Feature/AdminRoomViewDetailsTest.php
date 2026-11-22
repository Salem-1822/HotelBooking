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
            'description'     => 'Deluxe room with panoramic sea view.',
        ]);

        Reservation::create([
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
            'description'     => 'Cozy single room.',
        ]);

        // Room 3: Room with multiple past and current reservations
        $this->roomMultipleRes = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '103',
            'type'            => 'Suite',
            'price_per_night' => 2500,
            'capacity'        => 4,
            'status'          => 'occupied',
            'description'     => 'Executive suite with private lounge.',
        ]);

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

    /** 1. Test Room Details endpoint returns ONLY room information and NO reservation/client data */
    public function test_room_details_endpoint_returns_only_room_information(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomWithRes));

        $response->assertOk();
        $response->assertJson([
            'id'              => $this->roomWithRes->id,
            'room_number'     => '101',
            'type'            => 'Deluxe',
            'capacity'        => 2,
            'price_per_night' => '1,200.00',
            'status'          => 'occupied',
            'description'     => 'Deluxe room with panoramic sea view.',
        ]);

        $data = $response->json();

        // Must NOT contain any client or reservation data
        $this->assertArrayNotHasKey('active_reservation', $data);
        $this->assertArrayNotHasKey('reservations', $data);
        $this->assertArrayNotHasKey('client_name', $data);
        $this->assertArrayNotHasKey('client_email', $data);
        $this->assertArrayNotHasKey('client_phone', $data);
        $this->assertArrayNotHasKey('guest_name', $data);
        $this->assertArrayNotHasKey('guest_phone', $data);
    }

    /** 2. Test Room without reservations returns room-only data */
    public function test_room_without_reservations_returns_only_room_data(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomEmpty));

        $response->assertOk();
        $response->assertJson([
            'id'              => $this->roomEmpty->id,
            'room_number'     => '102',
            'type'            => 'Single',
            'capacity'        => 1,
            'status'          => 'available',
            'description'     => 'Cozy single room.',
        ]);

        $data = $response->json();
        $this->assertArrayNotHasKey('active_reservation', $data);
        $this->assertArrayNotHasKey('reservations', $data);
    }

    /** 3. Test Room with multiple reservations returns room-only data and NO client records */
    public function test_room_with_multiple_reservations_returns_only_room_data(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.rooms.show', $this->roomMultipleRes));

        $response->assertOk();
        $response->assertJson([
            'id'              => $this->roomMultipleRes->id,
            'room_number'     => '103',
            'type'            => 'Suite',
            'capacity'        => 4,
            'status'          => 'occupied',
        ]);

        $data = $response->json();
        $this->assertArrayNotHasKey('active_reservation', $data);
        $this->assertArrayNotHasKey('reservations', $data);
        $this->assertArrayNotHasKey('client_name', $data);
    }

    /** 4. Test unauthorized hotel cannot view other hotel's room */
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

    /** 5. Test Rooms Page HTML renders room-only details in all locales (EN, FR, AR) */
    public function test_rooms_page_renders_room_only_modal_in_all_locales(): void
    {
        $locales = ['en', 'fr', 'ar'];

        foreach ($locales as $locale) {
            $response = $this->actingAs($this->admin, 'admin')
                ->withSession(['locale' => $locale])
                ->get(route('admin.rooms.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Room-only modal elements exist
            $response->assertSee('id="viewRoomModal"', false);
            $response->assertSee('id="viewMainImage"', false);
            $response->assertSee('id="viewRoomTitle"', false);
            $response->assertSee('id="viewRoomSubtitle"', false);
            $response->assertSee('id="viewStatusBadge"', false);
            $response->assertSee('id="viewRoomNumber"', false);
            $response->assertSee('id="viewRoomType"', false);
            $response->assertSee('id="viewCapacity"', false);
            $response->assertSee('id="viewPrice"', false);
            $response->assertSee('id="viewDescription"', false);

            // Unwanted client & reservation sections are NOT present
            $response->assertDontSee('id="viewReservationSection"', false);
            $response->assertDontSee('id="viewReservationDetails"', false);
            $response->assertDontSee('id="viewClientName"', false);
            $response->assertDontSee('id="viewClientPhone"', false);
            $response->assertDontSee('id="viewClientEmail"', false);
            $response->assertDontSee('id="viewNoReservation"', false);
            $response->assertDontSee('id="viewMultipleReservationsSection"', false);

            // window.rooms must NOT contain client or reservation data
            $response->assertDontSee('active_reservation');
            $response->assertDontSee('Yassine Alami');
            $response->assertDontSee('yassine.alami@example.com');
            $response->assertDontSee('Fatima Zahra');

            // Global translations for statuses are present (no JS ReferenceError)
            $response->assertSee('const statusTranslations =', false);

            // No raw translation keys
            $this->assertDoesNotMatchRegularExpression(
                '/(?<!@)\b(admin|admin_nav|booking|common)\.[a-z_0-9]+/',
                $content,
                "Found raw translation key on rooms index in locale {$locale}"
            );

            // Locale specific direction
            if ($locale === 'ar') {
                $response->assertSee('dir="rtl"', false);
            } else {
                $response->assertSee('dir="ltr"', false);
            }
        }
    }
}
