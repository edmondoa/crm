<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'start_date' => $request->input(
                'start_date',
                today()->startOfMonth()->toDateString()
            ),
            'end_date' => $request->input(
                'end_date',
                today()->toDateString()
            ),
        ]);

        $filters = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($filters['start_date'])->startOfDay();
        $endDate = Carbon::parse($filters['end_date'])->endOfDay();
        $startDay = $startDate->toDateString();
        $endDay = $endDate->toDateString();

        $periodPayments = Payment::query()
            ->whereDate('payment_date', '>=', $startDay)
            ->whereDate('payment_date', '<=', $endDay);
        $periodInvoices = Invoice::query()
            ->whereDate('invoice_date', '>=', $startDay)
            ->whereDate('invoice_date', '<=', $endDay);
        $periodCustomers = Customer::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
        $periodLeads = Lead::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
        $periodJobs = Job::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
        $openOpportunities = Opportunity::query()
            ->whereNotIn('stage', ['won', 'lost']);

        $stats = [
            'collected' => (clone $periodPayments)->sum('amount'),
            'payment_count' => (clone $periodPayments)->count(),
            'invoiced' => (clone $periodInvoices)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->sum('grand_total'),
            'invoice_count' => (clone $periodInvoices)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->count(),
            'new_customers' => (clone $periodCustomers)->count(),
            'new_leads' => (clone $periodLeads)->count(),
            'jobs_created' => (clone $periodJobs)->count(),
            'jobs_completed' => Job::query()
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
            'current_outstanding' => Invoice::query()
                ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
                ->where('balance_due', '>', 0)
                ->sum('balance_due'),
            'open_pipeline' => (clone $openOpportunities)
                ->sum('estimated_value'),
            'open_tasks' => Task::query()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        $invoiceStatuses = (clone $periodInvoices)
            ->selectRaw(
                'status, COUNT(*) as invoice_count, ' .
                'SUM(grand_total) as total_amount, ' .
                'SUM(balance_due) as balance_amount'
            )
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $leadStatuses = (clone $periodLeads)
            ->selectRaw('status, COUNT(*) as lead_count')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $jobStatuses = (clone $periodJobs)
            ->selectRaw('status, COUNT(*) as job_count')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $opportunityStages = (clone $openOpportunities)
            ->selectRaw(
                'stage, COUNT(*) as opportunity_count, ' .
                'SUM(estimated_value) as pipeline_value'
            )
            ->groupBy('stage')
            ->orderBy('stage')
            ->get();

        $collectionsByMonth = (clone $periodPayments)
            ->selectRaw('payment_date, SUM(amount) as day_total')
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get()
            ->groupBy(
                fn ($payment) => Carbon::parse($payment->payment_date)
                    ->format('Y-m')
            )
            ->map(function ($days, $month) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $month)
                        ->format('M Y'),
                    'total' => $days->sum(
                        fn ($day) => (float) $day->day_total
                    ),
                ];
            })
            ->values();

        $recentPayments = (clone $periodPayments)
            ->with('invoice.customer')
            ->latest('payment_date')
            ->limit(8)
            ->get();

        return view('crm.reports.index', compact(
            'startDate',
            'endDate',
            'stats',
            'invoiceStatuses',
            'leadStatuses',
            'jobStatuses',
            'opportunityStages',
            'collectionsByMonth',
            'recentPayments'
        ));
    }
}
