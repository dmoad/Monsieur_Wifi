<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: tables may already exist (manual SQL / partial run) while `migrations` row was missing.
        if (! Schema::hasTable('qos_classes')) {
            Schema::create('qos_classes', function (Blueprint $table) {
                $table->string('id', 10)->primary();   // EF, AF41, BE, CS1
                $table->string('label');               // Real-time, Streaming, Default, Background
                $table->unsignedTinyInteger('dscp_value');
                $table->string('nft_mark', 10);        // 0x2e, 0x22, 0x00, 0x08
                $table->unsignedTinyInteger('priority');
                $table->string('description');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('qos_class_domains')) {
            Schema::create('qos_class_domains', function (Blueprint $table) {
                $table->id();
                $table->string('class_id', 10);
                $table->string('domain');              // e.g. *.zoom.us
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('class_id')->references('id')->on('qos_classes')->cascadeOnDelete();
                $table->unique(['class_id', 'domain']);
            });
        }

        $now = now();

        if (DB::table('qos_classes')->count() === 0) {
            DB::table('qos_classes')->insert([
                ['id' => 'EF',   'label' => 'Real-time',  'dscp_value' => 46, 'nft_mark' => '0x2e', 'priority' => 0, 'description' => 'Video calls, VoIP — highest priority',          'created_at' => $now, 'updated_at' => $now],
                ['id' => 'AF41', 'label' => 'Streaming',  'dscp_value' => 34, 'nft_mark' => '0x22', 'priority' => 1, 'description' => 'Video streaming',                                'created_at' => $now, 'updated_at' => $now],
                ['id' => 'BE',   'label' => 'Default',    'dscp_value' => 0,  'nft_mark' => '0x00', 'priority' => 2, 'description' => 'General browsing — unmatched traffic falls here', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 'CS1',  'label' => 'Background', 'dscp_value' => 8,  'nft_mark' => '0x08', 'priority' => 3, 'description' => 'Backups, cloud sync, IoT, Guest networks',       'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('qos_class_domains')->count() === 0) {
            $domains = [
                // EF — Real-time (VoIP / video calls)
                ['class_id' => 'EF', 'domain' => 'zoom.us'],
                ['class_id' => 'EF', 'domain' => '*.zoom.us'],
                ['class_id' => 'EF', 'domain' => 'teams.microsoft.com'],
                ['class_id' => 'EF', 'domain' => '*.teams.microsoft.com'],
                ['class_id' => 'EF', 'domain' => '*.skype.com'],
                ['class_id' => 'EF', 'domain' => 'stun.l.google.com'],
                // AF41 — Streaming
                ['class_id' => 'AF41', 'domain' => 'api.netflix.com'],
                ['class_id' => 'AF41', 'domain' => '*.nflxvideo.com'],
                ['class_id' => 'AF41', 'domain' => '*.nflximg.net'],
                ['class_id' => 'AF41', 'domain' => 'nflxso.net'],
                ['class_id' => 'AF41', 'domain' => 'youtube.com'],
                ['class_id' => 'AF41', 'domain' => '*.youtube.com'],
                ['class_id' => 'AF41', 'domain' => 'googlevideo.com'],
                ['class_id' => 'AF41', 'domain' => '*.googlevideo.com'],
            ];

            foreach ($domains as &$d) {
                $d['created_at'] = $now;
            }

            DB::table('qos_class_domains')->insert($domains);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qos_class_domains');
        Schema::dropIfExists('qos_classes');
    }
};
