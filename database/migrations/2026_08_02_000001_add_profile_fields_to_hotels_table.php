<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add hotel profile fields to the hotels table.
     *
     * Reuses: image (cover image), name, description, address, status (existing columns).
     * Does NOT add: cover_image (reuses 'image'), google_maps_url (deferred).
     */
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Contact Information
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');

            // Classification
            $table->tinyInteger('stars')->unsigned()->nullable()->after('website'); // 1-5
            $table->string('category')->nullable()->after('stars');                 // e.g. Boutique, Resort

            // Media
            $table->string('logo')->nullable()->after('image');
            $table->json('gallery_images')->nullable()->after('logo');

            // Facilities
            $table->json('amenities')->nullable()->after('gallery_images');

            // Policies
            $table->string('check_in_time')->nullable()->after('amenities');
            $table->string('check_out_time')->nullable()->after('check_in_time');
            $table->text('cancellation_policy')->nullable()->after('check_out_time');
            $table->string('children_policy')->nullable()->after('cancellation_policy');
            $table->string('pets_policy')->nullable()->after('children_policy');
            $table->string('smoking_policy')->nullable()->after('pets_policy');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'email', 'website',
                'stars', 'category',
                'logo', 'gallery_images',
                'amenities',
                'check_in_time', 'check_out_time',
                'cancellation_policy', 'children_policy',
                'pets_policy', 'smoking_policy',
            ]);
        });
    }
};
