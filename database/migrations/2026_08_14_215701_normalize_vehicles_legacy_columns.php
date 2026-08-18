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
            if ($hasBrand) {
                Schema::table('vehicles', function (Blueprint $table) {
                    $table->string('brand', 80)->nullable()->change();
                });
            }

            if ($hasModel) {
                Schema::table('vehicles', function (Blueprint $table) {
                    $table->string('model', 120)->nullable()->change();
                });
            }
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('plate', 8)->change();
        });
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
