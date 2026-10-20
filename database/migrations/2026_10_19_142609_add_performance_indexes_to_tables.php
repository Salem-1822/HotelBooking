<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hotels table indexes
        Schema::table('hotels', function (Blueprint $table) {
            $table->index(['status', 'city_id'], 'idx_hotels_status_city');
            $table->index(['status', 'price_per_night'], 'idx_hotels_status_price');
            $table->index(['status', 'stars'], 'idx_hotels_status_stars');
        });

        // 2. Rooms table indexes
        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['hotel_id', 'status', 'price_per_night'], 'idx_rooms_hotel_status_price');
            $table->index(['status', 'capacity'], 'idx_rooms_status_capacity');
        });

        // 3. Reservations table indexes (Crucial for date overlap, admin filters, customer history)
        Schema::table('reservations', function (Blueprint $table) {
            // High-frequency date overlap check: WHERE room_id = ? AND status != 'cancelled' AND check_in < ? AND check_out > ?
            $table->index(['room_id', 'status', 'check_in', 'check_out'], 'idx_reservations_room_status_dates');
            // Admin reservation listing, dashboard KPIs & date filters: WHERE hotel_id = ? AND status = ? AND check_in = ?
            $table->index(['hotel_id', 'status', 'created_at'], 'idx_reservations_hotel_status_created');
            $table->index(['hotel_id', 'check_in'], 'idx_reservations_hotel_checkin');
            // Client reservation listing: WHERE user_id = ? ORDER BY created_at DESC
            $table->index(['user_id', 'created_at'], 'idx_reservations_user_created');
            // Customer modal / history: WHERE customer_id = ? ORDER BY check_in DESC
            $table->index(['customer_id', 'check_in'], 'idx_reservations_customer_checkin');
        });

        // 4. Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['hotel_id', 'rating'], 'idx_reviews_hotel_rating');
            $table->index(['hotel_id', 'created_at'], 'idx_reviews_hotel_created');
        });

        // 5. Customers table index
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['hotel_id', 'name'], 'idx_customers_hotel_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_hotel_name');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_hotel_rating');
            $table->dropIndex('idx_reviews_hotel_created');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_reservations_room_status_dates');
            $table->dropIndex('idx_reservations_hotel_status_created');
            $table->dropIndex('idx_reservations_hotel_checkin');
            $table->dropIndex('idx_reservations_user_created');
            $table->dropIndex('idx_reservations_customer_checkin');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_hotel_status_price');
            $table->dropIndex('idx_rooms_status_capacity');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex('idx_hotels_status_city');
            $table->dropIndex('idx_hotels_status_price');
            $table->dropIndex('idx_hotels_status_stars');
        });
    }
};
