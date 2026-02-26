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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 4)->default(0.13)->after('smtp_password');
            $table->integer('cart_abandonment_hours')->default(24)->after('tax_rate');
            $table->enum('payment_mode', ['mock', 'stripe'])->default('mock')->after('cart_abandonment_hours');
            $table->boolean('stripe_enabled')->default(false)->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'cart_abandonment_hours', 'payment_mode', 'stripe_enabled']);
        });
    }
};
