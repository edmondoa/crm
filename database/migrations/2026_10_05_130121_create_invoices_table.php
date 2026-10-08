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
        Schema::create('invoices', function (Blueprint $table) {

            $table->id();

            $table->string('invoice_number', 50)->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->foreignId('job_id')
                ->nullable()
                ->constrained('jobs')
                ->nullOnDelete();

            $table->foreignId('estimate_id')
                ->nullable()
                ->constrained('crm_estimates')
                ->nullOnDelete();

            $table->date('invoice_date');

            $table->date('due_date');

            $table->enum('status', [
                'draft',
                'sent',
                'viewed',
                'partial',
                'paid',
                'overdue',
                'cancelled',
            ])->default('draft');

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('discount', 12, 2)->default(0);

            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('other_charges', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->decimal('balance_due', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->text('terms')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('job_id');
            $table->index('estimate_id');
            $table->index('invoice_date');
            $table->index('due_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
