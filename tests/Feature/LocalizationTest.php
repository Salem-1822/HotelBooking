<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $admin;
    protected Admin $superAdmin;
    protected User $client;
    protected Room $room;

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
            'price_per_night' => 1200,
            'status'          => 'active',
        ]);

        $this->admin = Admin::create([
            'name'       => 'Karim Admin',
            'email'      => 'karim@hotelbooking.com',
            'password'   => Hash::make('AdminPassword123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->hotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->superAdmin = Admin::create([
            'name'       => 'Salem SuperAdmin',
            'email'      => 'superadmin@hotelbooking.com',
            'password'   => Hash::make('SuperPassword123!'),
            'role'       => 'super_admin',
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->client = User::create([
            'name'     => 'Yassine Client',
            'email'    => 'client@hotelbooking.com',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
        ]);

        $this->room = Room::create([
            'hotel_id'        => $this->hotel->id,
            'room_number'     => '101',
            'type'            => 'deluxe',
            'price_per_night' => 1200,
            'capacity'        => 2,
            'status'          => 'available',
        ]);
    }

    /** 1. English is default locale */
    public function test_default_locale_is_english(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $this->assertEquals('en', App::getLocale());
        $response->assertSee('lang="en"', false);
        $response->assertSee('dir="ltr"', false);
    }

    /** 2. Switching to French sets session and cookie */
    public function test_switching_to_french_persists(): void
    {
        $response = $this->get(route('locale.switch', 'fr'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'fr');
        $response->assertCookie('locale', 'fr');

        $followUp = $this->withSession(['locale' => 'fr'])->get('/');
        $followUp->assertOk();
        $followUp->assertSee('lang="fr"', false);
        $followUp->assertSee('dir="ltr"', false);
        $followUp->assertSee('Accueil');
    }

    /** 3. Switching to Arabic sets session, cookie, and RTL */
    public function test_switching_to_arabic_sets_rtl(): void
    {
        $response = $this->get(route('locale.switch', 'ar'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ar');
        $response->assertCookie('locale', 'ar');

        $followUp = $this->withSession(['locale' => 'ar'])->get('/');
        $followUp->assertOk();
        $followUp->assertSee('lang="ar"', false);
        $followUp->assertSee('dir="rtl"', false);
        $followUp->assertSee('الرئيسية');
        $followUp->assertSee('bootstrap.rtl.min.css', false);
    }

    /** 4. Unsupported locale is rejected safely and falls back to en */
    public function test_unsupported_locale_is_rejected_safely(): void
    {
        $response = $this->get(route('locale.switch', 'es'));
        $response->assertRedirect();
        $response->assertSessionMissing('locale');

        $followUp = $this->withSession(['locale' => 'invalid_lang'])->get('/');
        $followUp->assertOk();
        $this->assertEquals('en', App::getLocale());
        $followUp->assertSee('lang="en"', false);
    }

    /** 5. Admin layout respects selected locale */
    public function test_admin_layout_respects_locale(): void
    {
        $responseEn = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $responseEn->assertOk();
        $responseEn->assertSee('Dashboard');

        $responseFr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'fr'])
            ->get(route('admin.dashboard'));
        $responseFr->assertOk();
        $responseFr->assertSee('Tableau de bord');

        $responseAr = $this->actingAs($this->admin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.dashboard'));
        $responseAr->assertOk();
        $responseAr->assertSee('dir="rtl"', false);
        $responseAr->assertSee('لوحة التحكم');
    }

    /** 6. Super Admin layout respects selected locale */
    public function test_super_admin_layout_respects_locale(): void
    {
        $responseEn = $this->actingAs($this->superAdmin, 'admin')->get(route('super_admin.dashboard'));
        $responseEn->assertOk();
        $responseEn->assertSee('Dashboard');

        $responseFr = $this->actingAs($this->superAdmin, 'admin')
            ->withSession(['locale' => 'fr'])
            ->get(route('super_admin.dashboard'));
        $responseFr->assertOk();
        $responseFr->assertSee('Tableau de bord');

        $responseAr = $this->actingAs($this->superAdmin, 'admin')
            ->withSession(['locale' => 'ar'])
            ->get(route('super_admin.dashboard'));
        $responseAr->assertOk();
        $responseAr->assertSee('dir="rtl"', false);
        $responseAr->assertSee('لوحة التحكم');
    }

    /** 7. Homepage and About pages render in all languages */
    public function test_homepage_and_about_render_in_all_languages(): void
    {
        // Homepage FR
        $homeFr = $this->withSession(['locale' => 'fr'])->get('/');
        $homeFr->assertOk();
        $homeFr->assertSee('Découvrez Votre Séjour Idéal');
        $homeFr->assertSee('Destinations Populaires');

        // Homepage AR
        $homeAr = $this->withSession(['locale' => 'ar'])->get('/');
        $homeAr->assertOk();
        $homeAr->assertSee('اكتشف إقامتك المثالية');
        $homeAr->assertSee('وجهات مميزة');

        // About FR
        $aboutFr = $this->withSession(['locale' => 'fr'])->get('/about');
        $aboutFr->assertOk();
        $aboutFr->assertSee('À propos de nous');
        $aboutFr->assertSee('Notre Mission');

        // About AR
        $aboutAr = $this->withSession(['locale' => 'ar'])->get('/about');
        $aboutAr->assertOk();
        $aboutAr->assertSee('من نحن');
        $aboutAr->assertSee('رسالتنا');
    }

    /** 8. Client auth pages render in all languages */
    public function test_client_auth_pages_render_in_all_languages(): void
    {
        // Login
        $loginEn = $this->withSession(['locale' => 'en'])->get('/login');
        $loginEn->assertOk()->assertSee('Welcome back')->assertSee('Keep me signed in');

        $loginFr = $this->withSession(['locale' => 'fr'])->get('/login');
        $loginFr->assertOk()->assertSee('Bon retour')->assertSee('Rester connecté');

        $loginAr = $this->withSession(['locale' => 'ar'])->get('/login');
        $loginAr->assertOk()->assertSee('مرحبًا بعودتك')->assertSee('تذكرني على هذا الجهاز');

        // Register
        $regEn = $this->withSession(['locale' => 'en'])->get(route('client.register'));
        $regEn->assertOk()->assertSee('Create Account')->assertSee('Join HotelBooking');

        $regFr = $this->withSession(['locale' => 'fr'])->get(route('client.register'));
        $regFr->assertOk()->assertSee('Créer un compte')->assertSee('Rejoignez HotelBooking');

        $regAr = $this->withSession(['locale' => 'ar'])->get(route('client.register'));
        $regAr->assertOk()->assertSee('إنشاء حساب')->assertSee('انضم إلى HotelBooking');

        // Forgot password
        $forgotEn = $this->withSession(['locale' => 'en'])->get(route('client.password.request'));
        $forgotEn->assertOk()->assertSee('Forgot Password?')->assertSee('Send Reset Link');

        $forgotFr = $this->withSession(['locale' => 'fr'])->get(route('client.password.request'));
        $forgotFr->assertOk()->assertSee('Mot de passe oublié ?')->assertSee('Envoyer le lien de réinitialisation');

        $forgotAr = $this->withSession(['locale' => 'ar'])->get(route('client.password.request'));
        $forgotAr->assertOk()->assertSee('نسيت كلمة المرور؟')->assertSee('إرسال رابط إعادة التعيين');
    }

    /** 9. Client dashboard and profile pages render in all languages */
    public function test_client_dashboard_and_profile_render_in_all_languages(): void
    {
        // Dashboard
        $dashEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.dashboard'));
        $dashEn->assertOk()->assertSee('Client Portal')->assertSee('My Reservations');

        $dashFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.dashboard'));
        $dashFr->assertOk()->assertSee('Portail Client')->assertSee('Mes réservations');

        $dashAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.dashboard'));
        $dashAr->assertOk()->assertSee('بوابة العميل')->assertSee('حجوزاتي');

        // Profile
        $profEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.profile.edit'));
        $profEn->assertOk()->assertSee('My Profile')->assertSee('Profile Information');

        $profFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.profile.edit'));
        $profFr->assertOk()->assertSee('Mon profil')->assertSee('Informations du profil');

        $profAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.profile.edit'));
        $profAr->assertOk()->assertSee('ملفي الشخصي')->assertSee('المعلومات الشخصية');
    }

    /** 10. Hotel listings and details render in all languages */
    public function test_hotel_listings_and_details_render_in_all_languages(): void
    {
        // Listings EN
        $hotelsEn = $this->withSession(['locale' => 'en'])->get(route('hotels.index'));
        $hotelsEn->assertOk()->assertSee('Browse Our Hotels')->assertSee('Filter by City');

        // Listings FR
        $hotelsFr = $this->withSession(['locale' => 'fr'])->get(route('hotels.index'));
        $hotelsFr->assertOk()->assertSee('Découvrez nos hôtels')->assertSee('Filtrer par ville');

        // Listings AR
        $hotelsAr = $this->withSession(['locale' => 'ar'])->get(route('hotels.index'));
        $hotelsAr->assertOk()->assertSee('استعرض فنادقنا المميزة')->assertSee('تصفية حسب المدينة');

        // Hotel Show EN
        $showEn = $this->withSession(['locale' => 'en'])->get(route('hotels.show', $this->hotel));
        $showEn->assertOk()->assertSee('Available Rooms')->assertSee('Guest Reviews');

        // Hotel Show FR
        $showFr = $this->withSession(['locale' => 'fr'])->get(route('hotels.show', $this->hotel));
        $showFr->assertOk()->assertSee('Chambres disponibles')->assertSee('Avis des clients');

        // Hotel Show AR
        $showAr = $this->withSession(['locale' => 'ar'])->get(route('hotels.show', $this->hotel));
        $showAr->assertOk()->assertSee('الغرف المتاحة')->assertSee('تقييمات النزلاء');
    }

    /** 11. Client reservation pages render in all languages */
    public function test_client_reservation_pages_render_in_all_languages(): void
    {
        $res = Reservation::create([
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

        // Index
        $indexEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.reservations.index'));
        $indexEn->assertOk()->assertSee('My Reservations')->assertSee('Book New Room');

        $indexFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.reservations.index'));
        $indexFr->assertOk()->assertSee('Mes réservations')->assertSee('Réserver une chambre');

        $indexAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.reservations.index'));
        $indexAr->assertOk()->assertSee('حجوزاتي')->assertSee('حجز غرفة جديدة');

        // Create / Booking
        $createEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.reserve.create', [$this->hotel, $this->room]));
        $createEn->assertOk()->assertSee('Complete Your Reservation')->assertSee('Booking Summary');

        $createFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.reserve.create', [$this->hotel, $this->room]));
        $createFr->assertOk()->assertSee('Finalisez votre réservation')->assertSee('Récapitulatif de la réservation');

        $createAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.reserve.create', [$this->hotel, $this->room]));
        $createAr->assertOk()->assertSee('إتمام تفاصيل الحجز')->assertSee('ملخص الحجز');

        // Show
        $showResEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.reservations.show', $res));
        $showResEn->assertOk()->assertSee('Reservation Details')->assertSee('Stay Summary');

        $showResFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.reservations.show', $res));
        $showResFr->assertOk()->assertSee('Détails de la réservation')->assertSee('Résumé du séjour');

        $showResAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.reservations.show', $res));
        $showResAr->assertOk()->assertSee('تفاصيل الحجز')->assertSee('ملخص الإقامة');

        // Confirmation
        $confEn = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'en'])
            ->get(route('client.reservations.confirmation', $res));
        $confEn->assertOk()->assertSee('Reservation Submitted!')->assertSee('Go to Dashboard');

        $confFr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'fr'])
            ->get(route('client.reservations.confirmation', $res));
        $confFr->assertOk()->assertSee('Demande de réservation envoyée !')->assertSee('Aller au tableau de bord');

        $confAr = $this->actingAs($this->client, 'web')
            ->withSession(['locale' => 'ar'])
            ->get(route('client.reservations.confirmation', $res));
        $confAr->assertOk()->assertSee('تم إرسال طلب الحجز بنجاح!')->assertSee('الانتقال إلى لوحة التحكم');
    }
}
