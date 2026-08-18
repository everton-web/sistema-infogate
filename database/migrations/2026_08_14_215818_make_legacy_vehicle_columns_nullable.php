<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vehicles', 'brand')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('brand', 80)->nullable()->change();
            });
        }

        if (Schema::hasColumn('vehicles', 'model')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('model', 120)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Colunas legadas não serão recriadas.
    }
};
