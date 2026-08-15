<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();

            $table->string('number', 20);
            $table->string('status', 30)->default('open');
            $table->string('priority', 20)->default('normal');

            $table->text('complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('internal_notes')->nullable();

            $table->decimal('total_products', 12, 2)->default(0);
            $table->decimal('total_services', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'customer_id']);
            $table->index('vehicle_id');
        });

        Schema::create('service_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('type', 10)->default('product');
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();

            $table->index('service_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_items');
        Schema::dropIfExists('service_orders');
    }
};
