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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_model_id')->constrained('product_models')->onDelete('cascade');
            $table->string('mac_address')->unique();
            $table->string('serial_number')->unique();
            $table->enum('status', ['available', 'reserved', 'sold', 'defective'])->default('available');
            $table->foreignId('cart_item_id')->nullable()->constrained('cart_items')->onDelete('set null');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index(['product_model_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
