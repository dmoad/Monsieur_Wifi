<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_network_users', function (Blueprint $table) {
            $table->unsignedInteger('login_successful_count')->default(0)->after('blocked');
            $table->unsignedInteger('login_failure_count')->default(0)->after('login_successful_count');
        });
    }

    public function down(): void
    {
        Schema::table('guest_network_users', function (Blueprint $table) {
            $table->dropColumn(['login_successful_count', 'login_failure_count']);
        });
    }
};
