<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_estimate_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estimate_id')
                ->constrained('crm_estimates')
                ->cascadeOnDelete();

            $table->string('item_type')
                ->default('service');

            $table->string('sku')->nullable();

            $table->string('description');

            $table->decimal('quantity', 15, 3)
                ->default(1);

            $table->string('unit')
                ->default('unit');

            $table->decimal('unit_cost', 15, 2)
                ->default(0);

            $table->decimal('markup_percent', 8, 2)
                ->default(0);

            $table->decimal('unit_price', 15, 2)
                ->default(0);

            $table->decimal('tax_percent', 8, 2)
                ->default(0);

            $table->decimal('tax_amount', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2)
                ->default(0);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index('estimate_id');
            $table->index('item_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_estimate_items');
    }
};