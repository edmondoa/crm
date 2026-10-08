<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Employee Identification
            |--------------------------------------------------------------------------
            */

            $table->string('employee_no', 50)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Name
            |--------------------------------------------------------------------------
            */

            $table->string('first_name', 100);

            $table->string('middle_name', 100)
                ->nullable();

            $table->string('last_name', 100);

            $table->string('suffix', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            $table->string('email', 150)
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Employment
            |--------------------------------------------------------------------------
            */

            $table->string('position', 100)
                ->nullable();

            $table->string('department', 100)
                ->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'contract',
                'temporary',
                'casual',
                'project_based',
            ])->default('full_time');

            $table->date('hire_date')
                ->nullable();

            $table->decimal('hourly_rate', 12, 2)
                ->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'on_leave',
                'terminated',
            ])->default('active');

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $table->text('address')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Emergency Contact
            |--------------------------------------------------------------------------
            */

            $table->string('emergency_contact_name', 150)
                ->nullable();

            $table->string('emergency_contact_relationship', 50)
                ->nullable();

            $table->string('emergency_contact_phone', 50)
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

            $table->index('status');
            $table->index('department');
            $table->index('position');
            $table->index('employment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};