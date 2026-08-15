<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type', 10)->default('product');
            $table->string('name');
            $table->string('sku', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('unit', 10)->default('un');
            $table->text('description')->nullable();

            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);

            $table->decimal('stock_quantity', 12, 2)->default(0);
            $table->decimal('stock_minimum', 12, 2)->default(0);

            $table->string('status', 20)->default('active')->index();

            $table->timestamps();

            $table->index(['company_id', 'name']);
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'sku']);
            $table->unique(['company_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
