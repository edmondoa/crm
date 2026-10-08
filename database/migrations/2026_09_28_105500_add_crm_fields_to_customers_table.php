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

            $table->string('customer_code', 50)
                ->unique();

            $table->enum('customer_type', [
                'individual',
                'company',
            ])->default('individual');

            // Company
            $table->string('company_name')
                ->nullable();

            // Individual
            $table->string('first_name')
                ->nullable();

            $table->string('middle_name')
                ->nullable();

            $table->string('last_name')
                ->nullable();

            // Contact
            $table->string('email')
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->string('mobile', 50)
                ->nullable();

            // Tax
            $table->string('tax_id', 100)
                ->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('country', 100)->default('United States');

            // CRM
            $table->text('notes')
                ->nullable();

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();

            $table->index('customer_type');
            $table->index('status');
            $table->index('company_name');
            $table->index('last_name');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};