<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            // When false, email login collects the address only — no OTP is sent or verified.
            // Defaults to true (current behavior: send and verify a 4-digit code).
            $table->boolean('email_require_otp')->default(true)->after('auth_methods');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('email_require_otp');
        });
    }
};
