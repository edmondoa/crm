<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_show_period_totals_and_current_pipeline_snapshots(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'RPT-CUST-001',
            'customer_type' => 'individual',
            'first_name' => 'Taylor',
            'last_name' => 'Rivera',
            'status' => 'active',
        ]);

        $invoice = Invoice::query()->create([
            'invoice_number' => 'RPT-INV-001',
            'customer_id' => $customer->id,
            'invoice_date' => today()->toDateString(),
            'due_date' => today()->addDays(14)->toDateString(),
            'status' => 'sent',
            'grand_total' => 200,
            'amount_paid' => 50,
            'balance_due' => 150,
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'RPT-PAY-001',
            'amount' => 50,
            'payment_date' => today()->toDateString(),
            'payment_method' => 'bank_transfer',
        ]);

        Lead::query()->create([
            'lead_number' => 'RPT-LEAD-001',
            'first_name' => 'Jordan',
            'status' => 'qualified',
        ]);

        Job::query()->create([
            'job_code' => 'RPT-JOB-001',
            'customer_id' => $customer->id,
            'title' => 'Repair entrance door',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Opportunity::query()->create([
            'opportunity_number' => 'RPT-OPP-001',
            'name' => 'Quarterly service agreement',
            'stage' => 'negotiation',
            'estimated_value' => 3000,
            'probability' => 70,
        ]);

        Task::query()->create([
            'task_code' => 'RPT-TASK-001',
            'customer_id' => $customer->id,
            'subject' => 'Send service follow-up',
            'status' => 'pending',
        ]);

        $startDate = today()->startOfMonth()->toDateString();
        $endDate = today()->toDateString();

        $this->get(route('crm.reports.index', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]))
            ->assertOk()
            ->assertViewHas('stats', function (array $stats): bool {
                $this->assertEquals(50, $stats['collected']);
                $this->assertSame(1, $stats['payment_count']);
                $this->assertEquals(200, $stats['invoiced']);
                $this->assertSame(1, $stats['invoice_count']);
                $this->assertSame(1, $stats['new_customers']);
                $this->assertSame(1, $stats['new_leads']);
                $this->assertSame(1, $stats['jobs_created']);
                $this->assertSame(1, $stats['jobs_completed']);
                $this->assertEquals(150, $stats['current_outstanding']);
                $this->assertEquals(3000, $stats['open_pipeline']);
                $this->assertSame(1, $stats['open_tasks']);

                return true;
            })
            ->assertSee('Collections by month')
            ->assertSee('RPT-INV-001')
            ->assertSee('Taylor Rivera')
            ->assertSee('Qualified')
            ->assertSee('Negotiation');
    }

    public function test_reports_reject_an_end_date_before_the_start_date(): void
    {
        $this->from(route('crm.reports.index'))
            ->get(route('crm.reports.index', [
                'start_date' => '2026-11-20',
                'end_date' => '2026-11-01',
            ]))
            ->assertRedirect(route('crm.reports.index'))
            ->assertSessionHasErrors('end_date');
    }
}
