<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_estimates', function (Blueprint $table) {
            $table->id();

            $table->string('estimate_number')->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->foreignId('job_id')
                ->nullable()
                ->constrained('jobs')
                ->nullOnDelete();

            $table->date('estimate_date');

            $table->date('expiration_date')->nullable();

            $table->string('status')
                ->default('draft');

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('discount', 15, 2)
                ->default(0);

            $table->decimal('tax', 15, 2)
                ->default(0);

            $table->decimal('other_charges', 15, 2)
                ->default(0);

            $table->decimal('grand_total', 15, 2)
                ->default(0);

            $table->text('notes')->nullable();

            $table->text('terms')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('estimate_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_estimates');
    }
};