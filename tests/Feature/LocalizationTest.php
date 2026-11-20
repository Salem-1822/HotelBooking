<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use App\Models\User;
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
}
