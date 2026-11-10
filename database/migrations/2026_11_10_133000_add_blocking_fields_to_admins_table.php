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
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('status');
            $table->text('blocked_reason')->nullable()->after('is_blocked');
            $table->timestamp('blocked_at')->nullable()->after('blocked_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'blocked_reason', 'blocked_at']);
        });
    }
};

