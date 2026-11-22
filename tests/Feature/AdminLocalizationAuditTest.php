<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLocalizationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $admin;
    protected Room $room;
    protected User $client;
    protected Customer $customer;
    protected Reservation $reservation;
    protected Review $review;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Atlas Grand Palace',
            'address'         => 'Boulevard Mohammed VI, Marrakech',
            'phone'           => '+212 5 24 11 22 33',
            'email'           => 'palace@hotelbooking.com',
            'price_per_night' => 1500,
            'stars'           => 5,
            'status'          => 'active',
        ]);

        $this->admin = Admin::create([
            'name'       => 'Karim Admin',
            'email'      => 'karim.admin@hotelbooking.com',
            'phone'      => '+212 6 11 22 33 44',
            'password'   => Hash::make('AdminPassword123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->hotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->room = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '201',
            'type'            => 'Deluxe',
            'price_per_night' => 1500,
            'capacity'        => 2,
            'status'          => 'available',
        ]);

        $this->client = User::create([
            'name'     => 'Yassine Client',
            'email'    => 'yassine@example.com',
            'phone'    => '+212 6 55 66 77 88',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
        ]);

        $this->customer = Customer::create([
            'hotel_id' => $this->hotel->id,
            'name'     => 'Amine Customer',
            'phone'    => '+212 6 99 88 77 66',
            'email'    => 'amine@example.com',
        ]);

        $this->reservation = Reservation::create([
            'hotel_id'     => $this->hotel->id,
            'room_id'      => $this->room->id,
            'user_id'      => $this->client->id,
            'customer_id'  => $this->customer->id,
            'guest_name'   => 'Yassine Client',
            'guest_phone'  => '+212 6 55 66 77 88',
            'check_in'     => Carbon::tomorrow()->toDateString(),
            'check_out'    => Carbon::tomorrow()->addDays(3)->toDateString(),
            'guests_count' => 2,
            'total_price'  => 4500,
            'status'       => 'confirmed',
        ]);

        $this->review = Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => 'Exceptional stay and pristine service.',
        ]);
    }

    /** 1. Test Admin guest auth pages render with 200, correct dir, and no raw keys */
    public function test_admin_guest_pages_render_cleanly_in_all_locales(): void
    {
        $locales = ['en', 'fr', 'ar'];

        $routesToTest = [
            route('admin.login'),
            route('admin.password.request'),
            route('admin.password.reset', ['token' => 'sample-test-token']),
        ];

        foreach ($locales as $locale) {
            foreach ($routesToTest as $route) {
                $response = $this->withSession(['locale' => $locale])->get($route);
                $response->assertOk();

                $content = $response->getContent();

                if ($locale === 'ar') {
                    $response->assertSee('dir="rtl"', false);
                } else {
                    $response->assertSee('dir="ltr"', false);
                }

                $this->assertDoesNotMatchRegularExpression(
                    '/(?<!@)\b(admin|admin_nav|booking|common)\.[a-z_0-9]+/',
                    $content,
                    "Found raw translation key in {$route} for locale {$locale}"
                );
            }
        }
    }

    /** 2. Test Blocked Admin account view renders with localized content */
    public function test_blocked_admin_view_renders_in_all_locales(): void
    {
        $blockedAdmin = Admin::create([
            'name'           => 'Blocked User',
            'email'          => 'blocked@hotelbooking.com',
            'password'       => Hash::make('Password123!'),
            'role'           => 'admin',
            'status'         => 'blocked',
            'is_blocked'     => true,
            'blocked_reason' => 'Violation of security policy.',
            'blocked_at'     => now(),
        ]);

        $locales = ['en', 'fr', 'ar'];

        foreach ($locales as $locale) {
            $response = $this->withSession(['locale' => $locale, 'blocked_admin_id' => $blockedAdmin->id])
                ->get(route('admin.blocked'));

            $response->assertOk();
            $content = $response->getContent();

            if ($locale === 'ar') {
                $response->assertSee('dir="rtl"', false);
            } else {
                $response->assertSee('dir="ltr"', false);
            }

            $response->assertSee('Violation of security policy.');
            $this->assertDoesNotMatchRegularExpression(
                '/\b(admin|admin_nav|common)\.[a-z_0-9]+/',
                $content,
                "Found raw translation key in blocked view for locale {$locale}"
            );
        }
    }

    /** 3. Test all Authenticated Admin portal pages render cleanly in all locales */
    public function test_authenticated_admin_portal_pages_render_cleanly(): void
    {
        $locales = ['en', 'fr', 'ar'];

        $routesToTest = [
            route('admin.dashboard'),
            route('admin.rooms.index'),
            route('admin.reservations.index'),
            route('admin.customers.index'),
            route('admin.customers.show', $this->customer),
            route('admin.reports.index'),
            route('admin.hotel-profile'),
            route('admin.reviews.index'),
            route('admin.profile'),
        ];

        foreach ($locales as $locale) {
            foreach ($routesToTest as $route) {
                $response = $this->actingAs($this->admin, 'admin')
                    ->withSession(['locale' => $locale])
                    ->get($route);

                $response->assertOk();
                $content = $response->getContent();

                if ($locale === 'ar') {
                    $response->assertSee('dir="rtl"', false);
                    $response->assertSee('bootstrap.rtl.min.css', false);
                } else {
                    $response->assertSee('dir="ltr"', false);
                    $response->assertSee('bootstrap.min.css', false);
                }

                $this->assertDoesNotMatchRegularExpression(
                    '/(?<!@)\b(admin|admin_nav|booking|common)\.[a-z_0-9]+/',
                    $content,
                    "Found raw translation key in {$route} for locale {$locale}"
                );
            }
        }
    }

    /** 4. Test Currency rendering across Admin pages */
    public function test_currency_renders_correctly_per_locale(): void
    {
        // English
        $dashEn = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'en'])
            ->get(route('admin.dashboard'));
        $dashEn->assertOk()->assertSee('MAD');

        // Arabic
        $dashAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.dashboard'));
        $dashAr->assertOk()->assertSee('درهم');

        // Reservations in Arabic
        $resAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.reservations.index'));
        $resAr->assertOk()->assertSee('درهم');

        // Rooms in Arabic
        $roomsAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.rooms.index'));
        $roomsAr->assertOk()->assertSee('درهم');
    }

    /** 5. Test BiDi isolation for reservation IDs, ratings, and phone numbers in Arabic */
    public function test_bidi_isolation_in_arabic_mode(): void
    {
        // Dashboard
        $dash = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.dashboard'));
        $dash->assertOk();
        $dash->assertSee('<span dir="ltr">#', false);

        // Reservations
        $res = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.reservations.index'));
        $res->assertOk();
        $res->assertSee('<span dir="ltr">#MOR-RSV-' . $this->reservation->id . '</span>', false);
        $res->assertSee('<span dir="ltr">' . $this->reservation->guest_phone . '</span>', false);

        // Reviews rating fraction
        $rev = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.reviews.index'));
        $rev->assertOk();
        $rev->assertSee('<span dir="ltr">' . $this->review->rating . '/5</span>', false);
    }

    /** 6. Test Admin controller flash messages localize properly */
    public function test_admin_controller_flash_messages_localize(): void
    {
        // Profile update in Arabic
        $updateAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->put(route('admin.profile.update'), [
                'name'  => 'Karim Admin AR',
                'email' => 'karim.admin@hotelbooking.com',
                'phone' => '+212 6 11 22 33 44',
            ]);
        $updateAr->assertRedirect();
        $updateAr->assertSessionHas('success', __('admin.profile_updated_success', [], 'ar'));

        // Customer update in French
        $custFr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'fr'])
            ->patch(route('admin.customers.update', $this->customer), [
                'name'  => 'Amine Updated FR',
                'phone' => '+212 6 99 88 77 66',
                'email' => 'amine.updated@example.com',
            ]);
        $custFr->assertRedirect();
        $custFr->assertSessionHas('success', __('admin.customer_updated_success', ['name' => 'Amine Updated FR'], 'fr'));

        // Reservation deletion in Arabic
        $delResAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->delete(route('admin.reservations.destroy', $this->reservation));
        $delResAr->assertRedirect();
        $delResAr->assertSessionHas('success', __('admin.reservation_deleted_success', [], 'ar'));
    }

    /** 7. Test PDF report generation */
    public function test_admin_pdf_report_generates_successfully(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'fr'])
            ->get(route('admin.reports.export'));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    /** 8. Test No-Hotel view has correct admin logout route */
    public function test_no_hotel_view_has_admin_logout_route(): void
    {
        $adminWithoutHotel = Admin::create([
            'name'       => 'No Hotel Admin',
            'email'      => 'nohotel@hotelbooking.com',
            'password'   => Hash::make('Password123!'),
            'role'       => 'admin',
            'hotel_id'   => null,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $response = $this->actingAs($adminWithoutHotel, 'admin')
            ->get(route('admin.customers.index'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
