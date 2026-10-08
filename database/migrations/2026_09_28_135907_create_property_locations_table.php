<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_locations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Property Relationship
            |--------------------------------------------------------------------------
            */

            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Location Identification
            |--------------------------------------------------------------------------
            */

            $table->string('location_code', 50)
                ->unique();

            $table->string('location_name', 255);

            $table->enum('location_type', [
                'office',
                'warehouse',
                'job_site',
                'billing',
                'mailing',
                'residential',
                'retail',
                'other',
            ])->default('job_site');

            /*
            |--------------------------------------------------------------------------
            | US Address
            |--------------------------------------------------------------------------
            */

            $table->string('address_line_1', 255);

            $table->string('address_line_2', 255)
                ->nullable();

            $table->string('city', 100);

            $table->string('state', 2);

            $table->string('zip_code', 10);

            /*
            |--------------------------------------------------------------------------
            | Optional Geographic Information
            |--------------------------------------------------------------------------
            */

            $table->string('county', 100)
                ->nullable();

            $table->decimal('latitude', 10, 7)
                ->nullable();

            $table->decimal('longitude', 10, 7)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_primary')
                ->default(false);

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

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

            $table->index('property_id');
            $table->index('location_type');
            $table->index('state');
            $table->index('zip_code');
            $table->index('is_primary');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_locations');
    }
};