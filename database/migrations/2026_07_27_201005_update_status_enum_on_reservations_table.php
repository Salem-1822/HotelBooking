<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires ALTER TABLE MODIFY COLUMN to change ENUM values.
        // The current ENUM is: pending, confirmed, cancelled, completed
        // Target ENUM is:      pending, confirmed, cancelled, checked_in, checked_out
        DB::statement("
            ALTER TABLE reservations
            MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'checked_in', 'checked_out')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert to original ENUM (replace checked_in/checked_out with completed)
        DB::statement("
            ALTER TABLE reservations
            MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
