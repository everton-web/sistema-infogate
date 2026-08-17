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
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['product', 'service']);
            $table->string('name');
            $table->string('sku', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('unit', 10)->default('UN');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('stock_quantity', 12, 2)->default(0);
            $table->decimal('stock_minimum', 12, 2)->default(0);
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('ncm', 10)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'status']);
            $table->unique(['company_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
