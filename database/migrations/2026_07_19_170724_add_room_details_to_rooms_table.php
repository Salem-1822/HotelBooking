<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (! Schema::hasColumn('rooms', 'name')) {
                    $table->string('name')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'bed_type')) {
                    $table->string('bed_type')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'floor')) {
                    $table->integer('floor')->default(1);
                }
                if (! Schema::hasColumn('rooms', 'size')) {
                    $table->decimal('size', 8, 2)->nullable();
                }
                if (! Schema::hasColumn('rooms', 'description')) {
                    $table->text('description')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'main_image')) {
                    $table->string('main_image')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'gallery_images')) {
                    $table->json('gallery_images')->nullable();
                }
            });

            // Expand status options and enforce unique room numbers per hotel.
            DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available','occupied','reserved','maintenance','inactive') NOT NULL DEFAULT 'available';");
            Schema::table('rooms', function (Blueprint $table) {
                if (! Schema::hasColumn('rooms', 'hotel_id_room_number_unique')) {
                    $table->unique(['hotel_id', 'room_number'], 'rooms_hotel_id_room_number_unique');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (Schema::hasColumn('rooms', 'gallery_images')) {
                    $table->dropColumn('gallery_images');
                }
                if (Schema::hasColumn('rooms', 'main_image')) {
                    $table->dropColumn('main_image');
                }
                if (Schema::hasColumn('rooms', 'description')) {
                    $table->dropColumn('description');
                }
                if (Schema::hasColumn('rooms', 'size')) {
                    $table->dropColumn('size');
                }
                if (Schema::hasColumn('rooms', 'floor')) {
                    $table->dropColumn('floor');
                }
                if (Schema::hasColumn('rooms', 'bed_type')) {
                    $table->dropColumn('bed_type');
                }
                if (Schema::hasColumn('rooms', 'name')) {
                    $table->dropColumn('name');
                }
                $table->dropUnique('rooms_hotel_id_room_number_unique');
            });

            DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available','occupied','maintenance') NOT NULL DEFAULT 'available';");
        }
    }
};
