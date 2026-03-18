<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('plan', 32)->default('free')->after('slug');
            $table->json('features_override')->nullable()->after('plan');
            $table->json('plan_metadata')->nullable()->after('features_override');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['plan', 'features_override', 'plan_metadata']);
        });
    }
};
