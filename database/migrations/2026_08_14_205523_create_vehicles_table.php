<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('vehicle_brand_id');
            $table->unsignedBigInteger('vehicle_model_id');

            $table->string('plate', 7);
            $table->string('version', 120)->nullable();

            $table->unsignedSmallInteger('year_manufacture')->nullable();
            $table->unsignedSmallInteger('year_model')->nullable();

            $table->string('color', 50)->nullable();
            $table->string('chassis', 30)->nullable();
            $table->unsignedInteger('odometer')->nullable();

            $table->text('notes')->nullable();

            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->timestamps();

            $table->index(['company_id', 'customer_id']);
            $table->index('vehicle_brand_id');
            $table->index('vehicle_model_id');

            $table->unique(['company_id', 'plate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
