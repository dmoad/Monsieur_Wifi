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
        Schema::create('temp_captive_portal_designs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('theme_color')->default('#7367f0');
            $table->string('welcome_message')->default('Welcome to our WiFi');
            $table->text('login_instructions')->nullable();
            $table->string('button_text')->default('Connect to WiFi');
            $table->boolean('show_terms')->default(true);
            $table->text('terms_content')->nullable();
            $table->text('privacy_content')->nullable();
            $table->string('location_logo_path')->nullable();
            $table->string('background_image_path')->nullable();
            $table->json('additional_settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('background_color_gradient_start')->nullable();
            $table->string('background_color_gradient_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_captive_portal_designs');
    }
};
