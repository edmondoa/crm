<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('invoice_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('item_type', 30)->default('service');

            $table->string('description');

            $table->string('sku', 100)->nullable();

            $table->decimal('quantity', 12, 3)->default(1);

            $table->string('unit', 50)->default('unit');

            $table->decimal('unit_price', 12, 2)->default(0);

            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->decimal('tax_percent', 5, 2)->default(0);

            $table->decimal('line_subtotal', 12, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->decimal('line_total', 12, 2)->default(0);

            $table->unsignedInteger('sort_order')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
