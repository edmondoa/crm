
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();

            $table->string('opportunity_number')->unique();
            $table->string('name');

            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('stage')->default('qualification')->index();
            $table->decimal('estimated_value', 14, 2)->default(0);
            $table->unsignedTinyInteger('probability')->default(20);
            $table->date('expected_close_date')->nullable()->index();

            $table->string('loss_reason')->nullable();
            $table->text('description')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['stage', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
