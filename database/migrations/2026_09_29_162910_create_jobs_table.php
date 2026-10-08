<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Job Identification
            |--------------------------------------------------------------------------
            */

            $table->string('job_code', 50)->unique();

            $table->string('work_order_number', 50)
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | CRM Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('contact_id')
                ->nullable()
                ->constrained('contacts')
                ->nullOnDelete();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->foreignId('property_location_id')
                ->nullable()
                ->constrained('property_locations')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Job Information
            |--------------------------------------------------------------------------
            */

            $table->string('title', 255);

            $table->enum('job_type', [
                'service',
                'repair',
                'maintenance',
                'installation',
                'inspection',
                'replacement',
                'construction',
                'renovation',
                'emergency',
                'other',
            ])->default('service');

            $table->text('description')->nullable();

            $table->text('scope_of_work')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Scheduling
            |--------------------------------------------------------------------------
            */

            $table->dateTime('scheduled_start_at')
                ->nullable();

            $table->dateTime('scheduled_end_at')
                ->nullable();

            $table->dateTime('started_at')
                ->nullable();

            $table->dateTime('completed_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Job Management
            |--------------------------------------------------------------------------
            */

            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'urgent',
            ])->default('normal');

            $table->enum('status', [
                'draft',
                'scheduled',
                'in_progress',
                'on_hold',
                'completed',
                'cancelled',
            ])->default('draft');

            /*
            |--------------------------------------------------------------------------
            | Financial Preparation
            |--------------------------------------------------------------------------
            |
            | These are intentionally only reference/estimate values.
            | Detailed costs and billing will be handled by later phases.
            |
            */

            $table->decimal(
                'estimated_amount',
                15,
                2
            )->nullable();

            $table->decimal(
                'approved_amount',
                15,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('customer_notes')->nullable();

            $table->text('internal_notes')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('customer_id');
            $table->index('contact_id');
            $table->index('property_id');
            $table->index('property_location_id');

            $table->index('job_type');
            $table->index('priority');
            $table->index('status');

            $table->index('scheduled_start_at');
            $table->index('scheduled_end_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};