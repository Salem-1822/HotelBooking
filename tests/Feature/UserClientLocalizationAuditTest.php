<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserClientLocalizationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Room $room;
    protected User $client;
    protected Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Atlas Luxury Resort',
            'address'         => 'Hivernage, Marrakech',
            'phone'           => '+212 5 24 00 00 00',
            'price_per_night' => 1200,
            'status'          => 'active',
        ]);

        $this->room = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '101',
            'type'            => 'deluxe',
            'price_per_night' => 1200,
            'capacity'        => 2,
            'status'          => 'available',
        ]);

        $this->client = User::create([
            'name'     => 'Yassine Client',
            'email'    => 'client@hotelbooking.com',
            'phone'    => '+212600112233',
            'password' => bcrypt('ClientPass123!'),
            'role'     => 'client',
        ]);

        $this->reservation = Reservation::create([
            'hotel_id'     => $this->hotel->id,
            'room_id'      => $this->room->id,
            'user_id'      => $this->client->id,
            'guest_name'   => 'Yassine Client',
            'check_in'     => Carbon::tomorrow()->toDateString(),
            'check_out'    => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price'  => 2400,
            'status'       => 'pending',
        ]);
    }

    /** Test RTL assets, html attributes, and no raw keys on Arabic client pages */
    public function test_user_and_client_pages_render_cleanly_in_all_locales(): void
    {
        $locales = ['en', 'fr', 'ar'];

        $routesToTest = [
            '/',
            '/about',
            '/contact',
            '/hotels',
            '/login',
            route('client.register'),
            route('client.password.request'),
        ];

        foreach ($locales as $locale) {
            foreach ($routesToTest as $route) {
                $response = $this->withSession(['locale' => $locale])->get($route);
                $response->assertOk();

                $content = $response->getContent();

                if ($locale === 'ar') {
                    $response->assertSee('dir="rtl"', false);
                    $response->assertSee('bootstrap.rtl.min.css', false);
                } else {
                    $response->assertSee('dir="ltr"', false);
                }

                // Ensure no raw translation keys leaked
                $this->assertDoesNotMatchRegularExpression('/\b(client|booking|hotel|nav|common)\.[a-z_0-9]+/', $content, "Found raw translation key in {$route} for locale {$locale}");
            }
        }
    }

    /** Test authenticated client routes render cleanly in all locales */
    public function test_authenticated_client_pages_render_cleanly(): void
    {
        $locales = ['en', 'fr', 'ar'];

        $routesWithPrices = [
            route('client.dashboard'),
            route('client.reservations.index'),
            route('client.reservations.show', $this->reservation),
            route('client.reserve.create', [$this->hotel, $this->room]),
        ];

        foreach ($locales as $locale) {
            foreach ($routesWithPrices as $route) {
                $response = $this->actingAs($this->client, 'web')
                    ->withSession(['locale' => $locale])
                    ->get($route);

                $response->assertOk();
                $content = $response->getContent();

                if ($locale === 'ar') {
                    $response->assertSee('dir="rtl"', false);
                    $response->assertSee('درهم');
                } else {
                    $response->assertSee('dir="ltr"', false);
                    $response->assertSee('MAD');
                }

                $this->assertDoesNotMatchRegularExpression('/\b(client|booking|hotel|nav|common)\.[a-z_0-9]+/', $content, "Found raw translation key in {$route} for locale {$locale}");
            }

            // Profile edit (no prices)
            $profRes = $this->actingAs($this->client, 'web')
                ->withSession(['locale' => $locale])
                ->get(route('client.profile.edit'));
            $profRes->assertOk();
            $this->assertDoesNotMatchRegularExpression('/\b(client|booking|hotel|nav|common)\.[a-z_0-9]+/', $profRes->getContent(), "Found raw translation key in profile edit for locale {$locale}");
        }
    }

    /** Test bidi isolation for phone numbers and reservation codes */
    public function test_bidi_isolation_in_arabic_mode(): void
    {
        $showRes = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.reservations.show', $this->reservation));

        $showRes->assertOk();
        // Reservation reference code must have dir="ltr"
        $showRes->assertSee('dir="ltr">#MOR-RSV-' . $this->reservation->id . '<', false);

        // Hotel show page contact phone must have dir="ltr"
        $hotelShow = $this->withSession(['locale' => 'ar'])
            ->get(route('hotels.show', $this->hotel));
        $hotelShow->assertOk();
        $hotelShow->assertSee('dir="ltr"', false);
    }

    /** Test client controller flash messages localize properly */
    public function test_client_controller_flash_messages_localize(): void
    {
        // Profile update with Arabic
        $profileResponse = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->put(route('client.profile.update'), [
                'name'  => 'Yassine Updated',
                'email' => 'client@hotelbooking.com',
                'phone' => '+212600112233',
            ]);

        $profileResponse->assertRedirect();
        $profileResponse->assertSessionHas('success', __('client.profile_updated', [], 'ar'));

        // Profile update with French
        $profileFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->put(route('client.profile.update'), [
                'name'  => 'Yassine FR',
                'email' => 'client@hotelbooking.com',
                'phone' => '+212600112233',
            ]);

        $profileFr->assertRedirect();
        $profileFr->assertSessionHas('success', __('client.profile_updated', [], 'fr'));
    }
}
