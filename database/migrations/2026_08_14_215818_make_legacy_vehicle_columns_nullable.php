<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vehicles', 'brand')) {
            DB::statement("
                ALTER TABLE `vehicles`
                MODIFY `brand` VARCHAR(80) NULL
            ");
        }

        if (Schema::hasColumn('vehicles', 'model')) {
            DB::statement("
                ALTER TABLE `vehicles`
                MODIFY `model` VARCHAR(120) NULL
            ");
        }
    }

    public function down(): void
    {
        // Colunas legadas não serão recriadas.
    }
};
