<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A tabela vehicles já existia antes do catálogo
        // de marcas e modelos. Adicionamos as colunas
        // somente se ainda não existirem.

        if (! Schema::hasColumn('vehicles', 'vehicle_brand_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedBigInteger('vehicle_brand_id')
                    ->nullable()
                    ->after('customer_id');
            });
        }

        if (! Schema::hasColumn('vehicles', 'vehicle_model_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedBigInteger('vehicle_model_id')
                    ->nullable()
                    ->after('vehicle_brand_id');
            });
        }

        // Agora as colunas existem e podemos criar
        // os relacionamentos com o catálogo automotivo.

        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign('vehicle_brand_id')
                ->references('id')
                ->on('vehicle_brands')
                ->onDelete('restrict');

            $table->foreign('vehicle_model_id')
                ->references('id')
                ->on('vehicle_models')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['vehicle_brand_id']);
            $table->dropForeign(['vehicle_model_id']);
        });

        if (Schema::hasColumn('vehicles', 'vehicle_model_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('vehicle_model_id');
            });
        }

        if (Schema::hasColumn('vehicles', 'vehicle_brand_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('vehicle_brand_id');
            });
        }
    }
};
