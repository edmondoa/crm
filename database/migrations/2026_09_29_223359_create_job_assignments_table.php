<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_assignments', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('job_id')
                ->constrained('jobs')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Assignment
            |--------------------------------------------------------------------------
            */

            $table->string('assignment_role', 100)
                ->nullable();

            $table->boolean('is_primary')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'assigned',
                'accepted',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('assigned');

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->dateTime('assigned_at')
                ->nullable();

            $table->dateTime('started_at')
                ->nullable();

            $table->dateTime('completed_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Labor
            |--------------------------------------------------------------------------
            */

            $table->decimal('estimated_hours', 8, 2)
                ->nullable();

            $table->decimal('actual_hours', 8, 2)
                ->nullable();

            $table->decimal('hourly_rate', 12, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'job_id',
                'employee_id',
            ]);

            $table->index('status');
            $table->index('is_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};