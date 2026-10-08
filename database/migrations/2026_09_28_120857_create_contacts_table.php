<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {

            $table->id();

            /*
             * Customer relationship
             */
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            /*
             * Contact classification
             */
            $table->enum('contact_type', [
                'primary',
                'billing',
                'project',
                'site',
                'technical',
                'emergency',
                'other',
            ])->default('primary');

            /*
             * Personal information
             */
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);

            /*
             * Business information
             */
            $table->string('job_title', 150)->nullable();
            $table->string('department', 150)->nullable();

            /*
             * Contact information
             */
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();

            /*
             * Address
             */
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('country', 100)->default('United States');

            /*
             * CRM preferences
             */
            $table->boolean('is_primary')->default(false);

            $table->enum('preferred_contact_method', [
                'email',
                'phone',
                'mobile',
            ])->nullable();

            $table->text('notes')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();

            /*
             * Indexes
             */
            $table->index('customer_id');
            $table->index('contact_type');
            $table->index('status');
            $table->index('email');
            $table->index('last_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};