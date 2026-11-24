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

class AdminHotelProfilePhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $hotelAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->city = City::create([
            'name' => 'Casablanca',
            'slug' => 'casablanca',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Grand Mogador City Center',
            'address' => 'Pasteur Boulevard, Casablanca',
            'price_per_night' => 1500,
            'status' => 'active',
            'image' => 'hotels/covers/default.webp',
        ]);

        $this->hotelAdmin = Admin::create([
            'name' => 'Hotel Manager',
            'email' => 'manager@mogador.test',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'status' => 'active',
            'city_id' => $this->city->id,
            'hotel_id' => $this->hotel->id,
        ]);
    }

    /**
     * Test 1: Hotel admin sees profile view with resolved cover image URL
     * and single file input with no duplicate name="image" inputs.
     */
    public function test_hotel_admin_can_view_profile_with_proper_markup(): void
    {
        $response = $this->actingAs($this->hotelAdmin, 'admin')
            ->get(route('admin.hotel-profile'));

        $response->assertStatus(200);
        $response->assertSee($this->hotel->image_url, false);
        $response->assertSee('id="coverFileInput"', false);
        $response->assertSee('id="coverDropZone"', false);
        $response->assertDontSee('id="coverFileInput2"', false);
        $response->assertDontSee('onclick="document.getElementById(\'coverFileInput2\').click()"', false);
    }

    /**
     * Test 2: Uploading a new cover photo via 'image' saves file to hotels/covers and updates DB.
     */
    public function test_hotel_admin_can_upload_new_cover_photo(): void
    {
        $file = UploadedFile::fake()->image('hotel_cover.webp', 1200, 800);

        $response = $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => 'Grand Mogador Luxury Suites',
                'address' => 'Pasteur Boulevard, Casablanca',
                'image' => $file,
            ]);

        $response->assertRedirect(route('admin.hotel-profile'));
        $response->assertSessionHas('success');

        $this->hotel->refresh();

        $this->assertEquals('Grand Mogador Luxury Suites', $this->hotel->name);
        $this->assertNotNull($this->hotel->image);
        $this->assertStringStartsWith('hotels/covers/', $this->hotel->image);
        Storage::disk('public')->assertExists($this->hotel->image);
    }

    /**
     * Test 3: Uploading a replacement cover photo deletes the old photo from storage.
     */
    public function test_uploading_replacement_cover_photo_deletes_previous_file(): void
    {
        // First upload
        $firstFile = UploadedFile::fake()->image('first_cover.jpg', 800, 600);
        $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => $this->hotel->name,
                'address' => $this->hotel->address,
                'image' => $firstFile,
            ]);

        $this->hotel->refresh();
        $firstImagePath = $this->hotel->image;
        Storage::disk('public')->assertExists($firstImagePath);

        // Second upload
        $secondFile = UploadedFile::fake()->image('second_cover.png', 1000, 700);
        $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => $this->hotel->name,
                'address' => $this->hotel->address,
                'image' => $secondFile,
            ]);

        $this->hotel->refresh();
        $secondImagePath = $this->hotel->image;

        $this->assertNotEquals($firstImagePath, $secondImagePath);
        Storage::disk('public')->assertExists($secondImagePath);
        Storage::disk('public')->assertMissing($firstImagePath);
    }

    /**
     * Test 4: Uploading via 'cover_image' alias field is also accepted.
     */
    public function test_uploading_via_cover_image_field_alias(): void
    {
        $file = UploadedFile::fake()->image('cover_alias.webp', 900, 600);

        $response = $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => $this->hotel->name,
                'address' => $this->hotel->address,
                'cover_image' => $file,
            ]);

        $response->assertRedirect(route('admin.hotel-profile'));
        $this->hotel->refresh();

        $this->assertStringStartsWith('hotels/covers/', $this->hotel->image);
        Storage::disk('public')->assertExists($this->hotel->image);
    }

    /**
     * Test 5: Profile update without an image does not overwrite existing image.
     */
    public function test_update_without_image_preserves_existing_image(): void
    {
        $existingImage = $this->hotel->image;

        $response = $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => 'Updated Hotel Name Only',
                'address' => 'Updated Address',
            ]);

        $response->assertRedirect(route('admin.hotel-profile'));
        $this->hotel->refresh();

        $this->assertEquals('Updated Hotel Name Only', $this->hotel->name);
        $this->assertEquals($existingImage, $this->hotel->image);
    }

    /**
     * Test 6: Invalid file types or oversized files are rejected with validation errors.
     */
    public function test_invalid_cover_photo_is_rejected(): void
    {
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->hotelAdmin, 'admin')
            ->from(route('admin.hotel-profile'))
            ->put(route('admin.hotel-profile.update'), [
                'name' => $this->hotel->name,
                'address' => $this->hotel->address,
                'image' => $pdfFile,
            ]);

        $response->assertRedirect(route('admin.hotel-profile'));
        $response->assertSessionHasErrors('image');

        $this->hotel->refresh();
        $this->assertStringNotContainsString('.pdf', (string)$this->hotel->image);
    }

    /**
     * Test 7: Public hotel page displays the uploaded cover image.
     */
    public function test_public_hotel_page_displays_uploaded_cover(): void
    {
        $file = UploadedFile::fake()->image('public_cover.webp', 1200, 800);

        $this->actingAs($this->hotelAdmin, 'admin')
            ->put(route('admin.hotel-profile.update'), [
                'name' => $this->hotel->name,
                'address' => $this->hotel->address,
                'image' => $file,
            ]);

        $this->hotel->refresh();

        $clientResponse = $this->get(route('hotels.show', $this->hotel));
        $clientResponse->assertStatus(200);
        $clientResponse->assertSee($this->hotel->image_url, false);
    }
}
