<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | CRM Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

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
            | Activity Information
            |--------------------------------------------------------------------------
            */

            $table->enum('activity_type', [
                'call',
                'email',
                'meeting',
                'site_visit',
                'note',
                'sms',
                'other',
            ])->default('note');

            $table->string('subject', 255);

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Scheduling
            |--------------------------------------------------------------------------
            */

            $table->dateTime('scheduled_at')
                ->nullable();

            $table->dateTime('completed_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Activity Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'planned',
                'completed',
                'cancelled',
            ])->default('planned');

            $table->enum('priority', [
                'low',
                'normal',
                'high',
            ])->default('normal');

            /*
            |--------------------------------------------------------------------------
            | Additional Information
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

            $table->index('customer_id');
            $table->index('contact_id');
            $table->index('property_id');
            $table->index('property_location_id');

            $table->index('activity_type');
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_at');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};