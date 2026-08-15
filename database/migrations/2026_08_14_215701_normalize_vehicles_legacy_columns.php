<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasBrand = Schema::hasColumn('vehicles', 'brand');
        $hasModel = Schema::hasColumn('vehicles', 'model');

        /*
         * Verifica se existe algum dado antigo antes
         * de remover as colunas legadas.
         */
        $hasLegacyData = false;

        if ($hasBrand) {
            $hasLegacyData = DB::table('vehicles')
                ->whereNotNull('brand')
                ->exists();
        }

        if (! $hasLegacyData && $hasModel) {
            $hasLegacyData = DB::table('vehicles')
                ->whereNotNull('model')
                ->exists();
        }

        if (! $hasLegacyData) {
            $columns = [];

            if ($hasBrand) {
                $columns[] = 'brand';
            }

            if ($hasModel) {
                $columns[] = 'model';
            }

            if (! empty($columns)) {
                Schema::table('vehicles', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        } else {
            /*
             * Se houver registros antigos, preserva os dados,
             * mas deixa as colunas opcionais.
             */
            if ($hasBrand) {
                DB::statement(
                    "ALTER TABLE `vehicles`
                     MODIFY `brand` VARCHAR(80) NULL"
                );
            }

            if ($hasModel) {
                DB::statement(
                    "ALTER TABLE `vehicles`
                     MODIFY `model` VARCHAR(120) NULL"
                );
            }
        }

        /*
         * Placa antiga com hífen possui 8 caracteres:
         * ABC-1234.
         */
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE `vehicles`
                 MODIFY `plate` VARCHAR(8) NOT NULL"
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('vehicles', 'brand')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('brand', 80)->nullable();
            });
        }

        if (! Schema::hasColumn('vehicles', 'model')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('model', 120)->nullable();
            });
        }
    }
};
