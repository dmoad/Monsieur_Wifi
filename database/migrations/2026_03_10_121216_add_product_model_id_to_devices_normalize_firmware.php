<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add product_model_id FK column to devices
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('product_model_id')->nullable()->after('name');
            $table->foreign('product_model_id')->references('id')->on('product_models')->nullOnDelete();
        });

        // Step 2: Migrate existing devices.model strings → product_model_id
        // Map all known model string variants to the correct ProductModel by device_type
        $modelMap = [
            '820'   => '820',
            '820AX' => '820',
            '1'     => '820',
            '835'   => '835',
            '835AX' => '835',
            '2'     => '835',
        ];

        foreach ($modelMap as $oldValue => $deviceType) {
            $productModel = DB::table('product_models')
                ->where('device_type', $deviceType)
                ->first();

            if ($productModel) {
                DB::table('devices')
                    ->where('model', $oldValue)
                    ->update(['product_model_id' => $productModel->id]);
            }
        }

        // Step 3: Drop the old model string column
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('model');
        });

        // Step 4: Normalise firmware.model strings to device_type format
        DB::table('firmware')->whereIn('model', ['820AX', '1'])->update(['model' => '820']);
        DB::table('firmware')->whereIn('model', ['835AX', '2'])->update(['model' => '835']);
    }

    public function down(): void
    {
        // Restore firmware.model to 820AX/835AX format
        DB::table('firmware')->where('model', '820')->update(['model' => '820AX']);
        DB::table('firmware')->where('model', '835')->update(['model' => '835AX']);

        // Re-add model string column
        Schema::table('devices', function (Blueprint $table) {
            $table->string('model')->nullable()->after('name');
        });

        // Restore model strings from product_model_id
        $productModels = DB::table('product_models')
            ->whereIn('device_type', ['820', '835'])
            ->get(['id', 'device_type']);

        foreach ($productModels as $pm) {
            DB::table('devices')
                ->where('product_model_id', $pm->id)
                ->update(['model' => $pm->device_type . 'AX']);
        }

        // Drop FK and product_model_id column
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['product_model_id']);
            $table->dropColumn('product_model_id');
        });
    }
};
