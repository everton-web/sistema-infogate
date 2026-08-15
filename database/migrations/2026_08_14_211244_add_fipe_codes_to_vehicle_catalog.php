<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_brands', function (Blueprint $table) {
            $table->string('external_code', 30)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('source', 30)
                ->default('fipe')
                ->after('external_code');
        });

        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->string('external_code', 30)
                ->nullable()
                ->after('vehicle_brand_id');

            $table->string('source', 30)
                ->default('fipe')
                ->after('external_code');

            $table->unique([
                'vehicle_brand_id',
                'external_code'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->dropUnique([
                'vehicle_brand_id',
                'external_code'
            ]);

            $table->dropColumn([
                'external_code',
                'source'
            ]);
        });

        Schema::table('vehicle_brands', function (Blueprint $table) {
            $table->dropUnique(['external_code']);

            $table->dropColumn([
                'external_code',
                'source'
            ]);
        });
    }
};
