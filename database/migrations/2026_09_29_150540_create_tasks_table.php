<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('task_code', 50)->unique();

            /*
             * CRM relationships
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
             * Task information
             */
            $table->enum('task_type', [
                'follow_up',
                'call',
                'email',
                'meeting',
                'site_visit',
                'estimate',
                'proposal',
                'document',
                'other',
            ])->default('follow_up');

            $table->string('subject', 255);

            $table->text('description')->nullable();

            /*
             * Scheduling
             */
            $table->dateTime('due_at')->nullable();

            $table->dateTime('completed_at')->nullable();

            /*
             * Task management
             */
            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'urgent',
            ])->default('normal');

            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->string('outcome', 255)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
             * Indexes
             */
            $table->index('customer_id');
            $table->index('contact_id');
            $table->index('property_id');
            $table->index('property_location_id');
            $table->index('task_type');
            $table->index('priority');
            $table->index('status');
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};