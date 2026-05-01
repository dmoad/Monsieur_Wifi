<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();

            // AP-reported identifiers
            $table->string('ap_id', 255);
            $table->string('ap_mac', 17);

            // Firmware / config context — opaque labels, not numeric
            $table->string('config_version', 255);
            $table->string('firmware_version', 255);

            // AP-reported timestamp; may be bogus if clock was unset
            $table->datetime('ap_ts');
            $table->boolean('ap_ts_flagged')->default(false);

            // Physical radio metrics (radio0 + radio1) stored as JSON;
            // 1–2 small objects, never queried individually
            $table->json('radios');

            // Server receive time for gap detection
            $table->timestamp('received_at');

            $table->timestamps();

            // Idempotency: AP sends each (ap_id, ts) at most once
            $table->unique(['device_id', 'ap_ts']);
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_stats');
    }
};
