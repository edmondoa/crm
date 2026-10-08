<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {

            $table->id();

            /*
             * Customer ownership
             */
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            /*
             * Optional primary contact
             */
            $table->foreignId('primary_contact_id')
                ->nullable()
                ->constrained('contacts')
                ->nullOnDelete();

            /*
             * Property identification
             */
            $table->string('property_code', 50)->unique();

            $table->string('name', 255);

            /*
             * Property classification
             */
            $table->enum('property_type', [
                'residential',
                'commercial',
                'industrial',
                'office',
                'retail',
                'warehouse',
                'multi_family',
                'land',
                'other',
            ])->default('commercial');

            /*
             * General information
             */
            $table->string('description', 500)->nullable();

            /*
             * CRM status
             */
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            /*
             * Additional notes
             */
            $table->text('notes')->nullable();

            $table->timestamps();

            /*
             * Indexes
             */
            $table->index('customer_id');
            $table->index('primary_contact_id');
            $table->index('property_type');
            $table->index('status');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};