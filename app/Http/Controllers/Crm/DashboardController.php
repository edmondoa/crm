<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $openLeads = Lead::query()->whereIn('status', ['new', 'contacted', 'qualified']);
        $openOpportunities = Opportunity::query()->whereNotIn('stage', ['won', 'lost']);
        $unpaidInvoices = Invoice::query()
            ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
            ->where('balance_due', '>', 0);
        $openTasks = Task::open();

        $stats = [
            'customers' => Customer::active()->count(),
            'new_customers' => Customer::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count(),
            'properties' => Property::active()->count(),
            'open_jobs' => Job::active()->count(),
            'open_leads' => (clone $openLeads)->count(),
            'leads_due' => (clone $openLeads)
                ->whereNotNull('follow_up_at')
                ->whereDate('follow_up_at', '<=', today())
                ->count(),
            'open_tasks' => (clone $openTasks)->count(),
            'tasks_due_soon' => (clone $openTasks)
                ->whereBetween('due_at', [now(), now()->addDays(7)->endOfDay()])
                ->count(),
            'overdue_tasks' => (clone $openTasks)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
            'open_opportunities' => (clone $openOpportunities)->count(),
            'pipeline_value' => (clone $openOpportunities)->sum('estimated_value'),
            'collected_this_month' => Payment::query()
                ->whereBetween('payment_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->sum('amount'),
            'outstanding_balance' => (clone $unpaidInvoices)->sum('balance_due'),
            'overdue_invoice_count' => (clone $unpaidInvoices)
                ->whereDate('due_date', '<', today())
                ->count(),
            'overdue_invoice_balance' => (clone $unpaidInvoices)
                ->whereDate('due_date', '<', today())
                ->sum('balance_due'),
        ];

        $recentCustomers = Customer::query()
            ->withCount('properties')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $upcomingTasks = Task::query()
            ->with('customer')
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereBetween('due_at', [now(), now()->addDays(7)->endOfDay()])
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $overdueTasks = Task::query()
            ->with('customer')
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $overdueInvoices = (clone $unpaidInvoices)
            ->with('customer')
            ->whereDate('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $leadsNeedingFollowUp = (clone $openLeads)
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '<=', today())
            ->orderBy('follow_up_at')
            ->limit(5)
            ->get();

        $recentActivities = Activity::query()
            ->with('customer')
            ->latest('updated_at')
            ->limit(6)
            ->get();

        return view('crm.dashboard.index', compact(
            'stats',
            'recentCustomers',
            'upcomingTasks',
            'overdueTasks',
            'overdueInvoices',
            'leadsNeedingFollowUp',
            'recentActivities'
        ));
    }
}