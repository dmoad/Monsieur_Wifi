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
        Schema::create('online_network_users', function (Blueprint $table) {
            $table->id();
            $table->string('mac');
            $table->string('type');
            $table->string('ip');
            $table->string('interface');
            $table->string('hostname')->nullable();
            $table->string('network');
            $table->foreignId('location_id')->constrained();
            $table->timestamps();
            
            // Add unique constraint on mac + location_id to prevent duplicates
            $table->unique(['mac', 'type', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_network_users');
    }
};
