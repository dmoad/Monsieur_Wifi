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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'payment_failed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('product_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->enum('shipping_method', ['normal', 'expedited']);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['mock', 'stripe'])->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->enum('payment_status', ['pending', 'succeeded', 'failed'])->nullable();
            $table->timestamp('payment_received_at')->nullable();
            $table->string('shipping_provider')->nullable();
            $table->string('tracking_id')->nullable();
            $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('restrict');
            $table->foreignId('billing_address_id')->constrained('addresses')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('order_number');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
