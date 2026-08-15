<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->string('slug', 120)->unique();

            $table->string('document', 20)->nullable()->index();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->string('plan', 30)->default('basic');
            $table->string('status', 20)->default('active')->index();

            $table->string('logo_path')->nullable();

            $table->string('timezone', 50)->default('America/Sao_Paulo');
            $table->string('locale', 10)->default('pt_BR');
            $table->string('currency', 3)->default('BRL');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
