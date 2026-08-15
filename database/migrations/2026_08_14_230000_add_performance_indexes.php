<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
        });

        Schema::table('branch_user', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
        });

        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->index(['vehicle_brand_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
        });

        Schema::table('branch_user', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
        });

        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->dropIndex(['vehicle_brand_id', 'is_active']);
        });
    }
};
