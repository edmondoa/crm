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
        Schema::create('crm_settings', function (Blueprint $table) {
            $table->id();

            $table->string('customer_code_prefix', 20)
                ->default('CUST-');

            $table->string('property_code_prefix', 20)
                ->default('PROP-');

            $table->string('task_code_prefix', 20)
                ->default('TASK-');

            $table->string('job_code_prefix', 20)
                ->default('JOB-');

            $table->string('quotation_code_prefix', 20)
                ->default('QUO-');

            $table->string('lead_code_prefix', 20)
                ->default('LEAD-');

            $table->string('opportunity_code_prefix', 20)
                ->default('OPP-');

            $table->boolean('enabled')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_settings');
    }
};
