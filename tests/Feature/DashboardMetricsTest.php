<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_summarizes_live_customer_sales_work_and_invoice_data(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'DASH-CUST-001',
            'customer_type' => 'individual',
            'first_name' => 'Morgan',
            'last_name' => 'Lee',
            'email' => 'morgan@example.test',
            'status' => 'active',
        ]);

        Property::query()->create([
            'customer_id' => $customer->id,
            'property_code' => 'DASH-PROP-001',
            'name' => 'Morgan residence',
            'property_type' => 'residential',
            'status' => 'active',
        ]);

        Job::query()->create([
            'job_code' => 'DASH-JOB-001',
            'customer_id' => $customer->id,
            'title' => 'Install new fixtures',
            'status' => 'scheduled',
        ]);

        Task::query()->create([
            'task_code' => 'DASH-TASK-001',
            'customer_id' => $customer->id,
            'subject' => 'Confirm appointment',
            'status' => 'pending',
            'due_at' => now()->addDays(2),
        ]);

        Task::query()->create([
            'task_code' => 'DASH-TASK-002',
            'customer_id' => $customer->id,
            'subject' => 'Call about invoice',
            'status' => 'in_progress',
            'due_at' => now()->subDay(),
        ]);

        Lead::query()->create([
            'lead_number' => 'DASH-LEAD-001',
            'first_name' => 'Jamie',
            'last_name' => 'Reed',
            'status' => 'qualified',
            'follow_up_at' => today()->toDateString(),
        ]);

        Opportunity::query()->create([
            'opportunity_number' => 'DASH-OPP-001',
            'name' => 'Office maintenance contract',
            'stage' => 'negotiation',
            'estimated_value' => 5000,
            'probability' => 60,
        ]);

        $invoice = Invoice::query()->create([
            'invoice_number' => 'DASH-INV-001',
            'customer_id' => $customer->id,
            'invoice_date' => today()->subDays(10)->toDateString(),
            'due_date' => today()->subDays(2)->toDateString(),
            'status' => 'overdue',
            'grand_total' => 250,
            'amount_paid' => 50,
            'balance_due' => 200,
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'DASH-PAY-001',
            'amount' => 50,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'cash',
        ]);

        $this->get(route('crm.dashboard'))
            ->assertOk()
            ->assertViewHas('stats', function (array $stats): bool {
                return $stats['customers'] === 1
                    && $stats['new_customers'] === 1
                    && $stats['properties'] === 1
                    && $stats['open_jobs'] === 1
                    && $stats['open_leads'] === 1
                    && $stats['leads_due'] === 1
                    && $stats['open_tasks'] === 2
                    && $stats['tasks_due_soon'] === 1
                    && $stats['overdue_tasks'] === 1
                    && $stats['open_opportunities'] === 1
                    && (float) $stats['pipeline_value'] === 5000.0
                    && (float) $stats['collected_this_month'] === 50.0
                    && (float) $stats['outstanding_balance'] === 200.0
                    && $stats['overdue_invoice_count'] === 1
                    && (float) $stats['overdue_invoice_balance'] === 200.0;
            })
            ->assertSee('Morgan Lee')
            ->assertSee('Confirm appointment')
            ->assertSee('DASH-INV-001')
            ->assertSee('Jamie Reed');
    }
}
