<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $superAdmin;
    protected Admin $regularAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Royal Mansour',
            'address' => 'Rue Abou Abbas El Sebti',
            'price_per_night' => 1200,
            'status' => 'active',
        ]);

        $this->superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->regularAdmin = Admin::create([
            'name' => 'Hotel Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'status' => 'active',
            'city_id' => $this->city->id,
            'hotel_id' => $this->hotel->id,
        ]);
    }

    /**
     * Test 1: Super Admin creates a regular Admin with a profile photo.
     */
    public function test_super_admin_can_create_admin_with_photo_and_both_parties_see_it(): void
    {
        $file = UploadedFile::fake()->image('new_admin.jpg', 200, 200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('super_admin.admins.store'), [
                'name' => 'New Created Admin',
                'email' => 'newadmin@example.com',
                'password' => 'Password123!',
                'city_id' => $this->city->id,
                'hotel_id' => $this->hotel->id,
                'image_file' => $file,
            ]);

        $response->assertRedirect(route('super_admin.admins.index'));

        $newAdmin = Admin::where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertNotNull($newAdmin->profile_image);
        $this->assertStringStartsWith('profiles/', $newAdmin->profile_image);

        // Verify the file was stored on the public disk
        Storage::disk('public')->assertExists($newAdmin->profile_image);

        // Verify Super Admin admin management page renders the photo URL
        $superAdminViewResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.admins.index'));
        $superAdminViewResponse->assertStatus(200);
        $superAdminViewResponse->assertSee(asset('storage/' . $newAdmin->profile_image), false);
        $superAdminViewResponse->assertDontSee('src="#"');

        // Verify the newly created Admin sees their photo in their profile and layout
        $adminProfileResponse = $this->actingAs($newAdmin, 'admin')
            ->get(route('admin.profile'));
        $adminProfileResponse->assertStatus(200);
        $adminProfileResponse->assertSee(asset('storage/' . $newAdmin->profile_image), false);
    }

    /**
     * Test 2: Regular Admin updates profile photo through settings.
     */
    public function test_admin_can_update_profile_photo_and_both_parties_see_it(): void
    {
        // First upload an initial photo
        $initialFile = UploadedFile::fake()->image('initial.jpg', 200, 200);
        $initialPath = $initialFile->store('profiles', 'public');
        $this->regularAdmin->update(['profile_image' => $initialPath]);
        Storage::disk('public')->assertExists($initialPath);

        // Now Admin updates their photo
        $newFile = UploadedFile::fake()->image('updated.png', 300, 300);

        $response = $this->actingAs($this->regularAdmin, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => $this->regularAdmin->name,
                'email' => $this->regularAdmin->email,
                'phone' => '1234567890',
                'profile_image' => $newFile,
            ]);

        $response->assertRedirect(route('admin.profile'));

        $this->regularAdmin->refresh();
        $this->assertNotNull($this->regularAdmin->profile_image);
        $this->assertStringStartsWith('profiles/', $this->regularAdmin->profile_image);
        $this->assertNotEquals($initialPath, $this->regularAdmin->profile_image);

        // New file exists, old file was deleted
        Storage::disk('public')->assertExists($this->regularAdmin->profile_image);
        Storage::disk('public')->assertMissing($initialPath);

        // Verify Admin profile page shows new photo
        $adminProfileResponse = $this->actingAs($this->regularAdmin, 'admin')
            ->get(route('admin.profile'));
        $adminProfileResponse->assertStatus(200);
        $adminProfileResponse->assertSee(asset('storage/' . $this->regularAdmin->profile_image), false);

        // Verify Super Admin admin list shows the updated photo
        $superAdminViewResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.admins.index'));
        $superAdminViewResponse->assertStatus(200);
        $superAdminViewResponse->assertSee(asset('storage/' . $this->regularAdmin->profile_image), false);
    }

    /**
     * Test 3: Admin without a photo receives a valid UI Avatar fallback and never src="#".
     */
    public function test_admin_without_photo_renders_valid_fallback(): void
    {
        $this->regularAdmin->update(['profile_image' => null]);

        $adminProfileResponse = $this->actingAs($this->regularAdmin, 'admin')
            ->get(route('admin.profile'));
        $adminProfileResponse->assertStatus(200);
        $adminProfileResponse->assertDontSee('src="#"');
        $adminProfileResponse->assertSee('ui-avatars.com');

        $superAdminViewResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.admins.index'));
        $superAdminViewResponse->assertStatus(200);
        $superAdminViewResponse->assertDontSee('src="#"');
        $superAdminViewResponse->assertSee('ui-avatars.com');

        // Edge case: profile_image stored as '#'
        $this->regularAdmin->update(['profile_image' => '#']);
        $this->assertFalse($this->regularAdmin->hasProfilePhoto());
        $this->assertStringContainsString('ui-avatars.com', $this->regularAdmin->profile_image_url);
    }

    /**
     * Test 4: Legacy filename-only record is properly resolved without duplicating directories.
     */
    public function test_legacy_filename_only_is_resolved_and_replaced_properly(): void
    {
        // Fake a file directly inside storage/app/public/profiles/
        $file = UploadedFile::fake()->image('legacy.jpg', 150, 150);
        $storedPath = $file->storeAs('profiles', 'legacy.jpg', 'public');

        // Legacy format in DB: only filename
        $this->regularAdmin->update(['profile_image' => 'legacy.jpg']);

        $this->assertTrue($this->regularAdmin->hasProfilePhoto());
        $resolvedUrl = $this->regularAdmin->profile_image_url;
        $this->assertStringContainsString('profiles/legacy.jpg', $resolvedUrl);
        $this->assertStringNotContainsString('profiles/profiles', $resolvedUrl);

        // Super Admin index resolves it correctly
        $superAdminViewResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.admins.index'));
        $superAdminViewResponse->assertStatus(200);
        $superAdminViewResponse->assertSee($resolvedUrl, false);

        // When updated, the legacy file is cleanly deleted
        $newFile = UploadedFile::fake()->image('fresh.jpg', 200, 200);
        $this->actingAs($this->regularAdmin, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => $this->regularAdmin->name,
                'email' => $this->regularAdmin->email,
                'profile_image' => $newFile,
            ]);

        Storage::disk('public')->assertMissing('profiles/legacy.jpg');
    }

    /**
     * Test 5: Invalid uploads are rejected and existing photo is preserved.
     */
    public function test_invalid_uploads_are_rejected_and_existing_photo_preserved(): void
    {
        $initialFile = UploadedFile::fake()->image('keeper.jpg', 200, 200);
        $initialPath = $initialFile->store('profiles', 'public');
        $this->regularAdmin->update(['profile_image' => $initialPath]);

        // Attempt to upload a non-image file (e.g. PDF/TXT)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->regularAdmin, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => $this->regularAdmin->name,
                'email' => $this->regularAdmin->email,
                'profile_image' => $invalidFile,
            ]);

        $response->assertSessionHasErrors('profile_image');

        // Existing file and path remain intact
        $this->regularAdmin->refresh();
        $this->assertEquals($initialPath, $this->regularAdmin->profile_image);
        Storage::disk('public')->assertExists($initialPath);

        // Attempt oversized file (> 5MB)
        $oversizedFile = UploadedFile::fake()->image('huge.jpg')->size(6000);

        $response2 = $this->actingAs($this->regularAdmin, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => $this->regularAdmin->name,
                'email' => $this->regularAdmin->email,
                'profile_image' => $oversizedFile,
            ]);

        $response2->assertSessionHasErrors('profile_image');

        $this->regularAdmin->refresh();
        $this->assertEquals($initialPath, $this->regularAdmin->profile_image);
        Storage::disk('public')->assertExists($initialPath);
    }

    /**
     * Test 6: Admin can remove photo and reverts to fallback avatar.
     */
    public function test_admin_can_remove_photo(): void
    {
        $file = UploadedFile::fake()->image('to_remove.jpg', 200, 200);
        $path = $file->store('profiles', 'public');
        $this->regularAdmin->update(['profile_image' => $path]);

        $response = $this->actingAs($this->regularAdmin, 'admin')
            ->delete(route('admin.profile.photo.destroy'));

        $response->assertRedirect(route('admin.profile'));

        $this->regularAdmin->refresh();
        $this->assertNull($this->regularAdmin->profile_image);
        Storage::disk('public')->assertMissing($path);

        $this->assertFalse($this->regularAdmin->hasProfilePhoto());
        $this->assertStringContainsString('ui-avatars.com', $this->regularAdmin->profile_image_url);
    }
}
