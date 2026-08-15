<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type', 10)->default('pf');

            $table->string('name');
            $table->string('trade_name')->nullable();

            $table->string('document', 20)->nullable();
            $table->string('state_registration', 30)->nullable();

            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();

            $table->string('postal_code', 12)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();

            $table->text('notes')->nullable();

            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->timestamps();

            $table->index(['company_id', 'name']);
            $table->index(['company_id', 'document']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
