<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_schedules', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Job
            |--------------------------------------------------------------------------
            */
            $table->foreignId('job_id')
                ->constrained('jobs')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Job Assignment
            |--------------------------------------------------------------------------
            */
            $table->foreignId('job_assignment_id')
                ->nullable()
                ->constrained('job_assignments')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            |
            | Stored directly so schedules can still be queried efficiently.
            | The employee must correspond to the job assignment.
            |
            */
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */
            $table->date('scheduled_date');

            $table->time('start_time');

            $table->time('end_time');

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('scheduled');

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */
            $table->string('location')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */
            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index([
                'employee_id',
                'scheduled_date',
            ]);

            $table->index([
                'job_id',
                'scheduled_date',
            ]);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_schedules');
    }
};