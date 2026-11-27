@extends('layouts.admin')

@section('title', 'CRM Dashboard')
@section('page-title', 'CRM Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">CRM Dashboard</h1>
            <p class="text-muted mb-0">A current view of customers, sales, work, and cash flow.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('crm.customers.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> New customer</a>
            <a href="{{ route('crm.leads.create') }}" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i> New lead</a>
            <a href="{{ route('crm.tasks.create') }}" class="btn btn-outline-secondary"><i class="bi bi-check2-square me-1"></i> New task</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-3">
        <div class="col">
            <a href="{{ route('crm.customers.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="rounded-3 bg-primary-subtle text-primary p-3"><i class="bi bi-people fs-5"></i></span>
                        <div>
                            <div class="small text-muted">Active customers</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['customers']) }}</div>
                            <div class="small text-muted">+{{ number_format($stats['new_customers']) }} added this month</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.leads.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="rounded-3 bg-info-subtle text-info p-3"><i class="bi bi-funnel fs-5"></i></span>
                        <div>
                            <div class="small text-muted">Open leads</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['open_leads']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['leads_due']) }} follow-ups due</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.jobs.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="rounded-3 bg-success-subtle text-success p-3"><i class="bi bi-briefcase fs-5"></i></span>
                        <div>
                            <div class="small text-muted">Active jobs</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['open_jobs']) }}</div>
                            <div class="small text-muted">Scheduled, in progress, or on hold</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.invoices.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="rounded-3 bg-warning-subtle text-warning p-3"><i class="bi bi-receipt fs-5"></i></span>
                        <div>
                            <div class="small text-muted">Outstanding invoices</div>
                            <div class="fs-3 fw-semibold">₱{{ number_format($stats['outstanding_balance'], 2) }}</div>
                            <div class="small text-muted">{{ number_format($stats['overdue_invoice_count']) }} overdue</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <a href="{{ route('crm.properties.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Active properties</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['properties']) }}</div>
                </div></div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.opportunities.pipeline') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Open sales pipeline</div>
                    <div class="fs-4 fw-semibold">₱{{ number_format($stats['pipeline_value'], 2) }}</div>
                    <div class="small text-muted">{{ number_format($stats['open_opportunities']) }} open opportunities</div>
                </div></div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.payments.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Collected this month</div>
                    <div class="fs-4 fw-semibold">₱{{ number_format($stats['collected_this_month'], 2) }}</div>
                    <div class="small text-muted">Payments recorded this month</div>
                </div></div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('crm.tasks.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Open tasks</div>
                    <div class="fs-4 fw-semibold">{{ number_format($stats['open_tasks']) }}</div>
                    <div class="small text-muted">{{ number_format($stats['tasks_due_soon']) }} due in 7 days · {{ number_format($stats['overdue_tasks']) }} overdue</div>
                </div></div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Recent customers</h2>
                        <div class="small text-muted">Latest customer records</div>
                    </div>
                    <a href="{{ route('crm.customers.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Customer</th><th>Email</th><th>Properties</th><th>Added</th></tr></thead>
                        <tbody>
                        @forelse($recentCustomers as $customer)
                            <tr>
                                <td><a href="{{ route('crm.customers.show', $customer) }}" class="fw-semibold text-decoration-none">{{ $customer->display_name }}</a></td>
                                <td>{{ $customer->email ?: '—' }}</td>
                                <td>{{ number_format($customer->properties_count) }}</td>
                                <td class="text-nowrap">{{ $customer->created_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No customers have been added yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Upcoming tasks</h2>
                        <div class="small text-muted">Due in the next 7 days</div>
                    </div>
                    <a href="{{ route('crm.tasks.index') }}" class="btn btn-sm btn-outline-secondary">View tasks</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($upcomingTasks as $task)
                        <a href="{{ route('crm.tasks.show', $task) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                            <span>
                                <span class="d-block fw-semibold">{{ $task->subject }}</span>
                                <span class="small text-muted">{{ $task->customer?->display_name ?? 'No customer' }} · {{ $task->status_label }}</span>
                            </span>
                            <span class="small text-nowrap">{{ $task->due_at?->format('M j, g:i A') ?? 'No due date' }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">No tasks due in the next 7 days.</div>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Recent activity</h2>
                        <div class="small text-muted">Latest customer activity updates</div>
                    </div>
                    <a href="{{ route('crm.activities.index') }}" class="btn btn-sm btn-outline-secondary">View activities</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <a href="{{ route('crm.activities.show', $activity) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                            <span>
                                <span class="d-block fw-semibold">{{ $activity->subject }}</span>
                                <span class="small text-muted">{{ $activity->customer?->display_name ?? $activity->activity_type_label }} · {{ ucfirst($activity->status) }}</span>
                            </span>
                            <span class="small text-nowrap">{{ $activity->updated_at?->diffForHumans() }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">No activities have been recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Overdue invoices</h2>
                        <div class="small text-muted">₱{{ number_format($stats['overdue_invoice_balance'], 2) }} outstanding</div>
                    </div>
                    <a href="{{ route('crm.invoices.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-secondary">View invoices</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($overdueInvoices as $invoice)
                        <a href="{{ route('crm.invoices.show', $invoice) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                            <span>
                                <span class="d-block fw-semibold">{{ $invoice->invoice_number }}</span>
                                <span class="small text-muted">{{ $invoice->customer?->display_name ?? 'Customer unavailable' }} · due {{ $invoice->due_date?->format('M j, Y') }}</span>
                            </span>
                            <span class="fw-semibold text-nowrap">₱{{ number_format($invoice->balance_due, 2) }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">No overdue invoices.</div>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Overdue tasks</h2>
                        <div class="small text-muted">{{ number_format($stats['overdue_tasks']) }} open tasks past due</div>
                    </div>
                    <a href="{{ route('crm.tasks.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-secondary">View tasks</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($overdueTasks as $task)
                        <a href="{{ route('crm.tasks.show', $task) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                            <span>
                                <span class="d-block fw-semibold">{{ $task->subject }}</span>
                                <span class="small text-muted">{{ $task->customer?->display_name ?? 'No customer' }}</span>
                            </span>
                            <span class="small text-danger text-nowrap">{{ $task->due_at?->format('M j') }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">No overdue tasks.</div>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 mb-1">Lead follow-ups due</h2>
                        <div class="small text-muted">{{ number_format($stats['leads_due']) }} open leads need follow-up</div>
                    </div>
                    <a href="{{ route('crm.leads.index') }}" class="btn btn-sm btn-outline-secondary">View leads</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($leadsNeedingFollowUp as $lead)
                        <a href="{{ route('crm.leads.show', $lead) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                            <span>
                                <span class="d-block fw-semibold">{{ $lead->full_name }}</span>
                                <span class="small text-muted">{{ $lead->company_name ?: ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                            </span>
                            <span class="small text-danger text-nowrap">{{ $lead->follow_up_at?->format('M j') }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">No lead follow-ups are due.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
