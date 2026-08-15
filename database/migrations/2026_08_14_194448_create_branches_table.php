<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 30)->nullable();

            $table->string('document', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->string('postal_code', 12)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();

            $table->boolean('is_main')->default(false);
            $table->string('status', 20)->default('active')->index();

            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
