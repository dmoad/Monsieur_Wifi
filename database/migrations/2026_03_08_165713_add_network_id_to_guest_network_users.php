<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_network_users', function (Blueprint $table) {
            $table->unsignedBigInteger('network_id')->nullable()->after('location_id');
            $table->foreign('network_id')->references('id')->on('location_networks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guest_network_users', function (Blueprint $table) {
            $table->dropForeign(['network_id']);
            $table->dropColumn('network_id');
        });
    }
};
