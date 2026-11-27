@extends('layouts.admin')

@section('title', 'CRM Reports')
@section('page-title', 'Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Reports</h1>
            <p class="text-muted mb-0">Review sales, collections, customers, and operations for a selected period.</p>
        </div>
        <form method="GET" action="{{ route('crm.reports.index') }}" class="d-flex flex-wrap align-items-end gap-2">
            <div>
                <label for="start_date" class="form-label small mb-1">From</label>
                <input id="start_date" name="start_date" type="date" class="form-control" value="{{ $startDate->toDateString() }}" required>
            </div>
            <div>
                <label for="end_date" class="form-label small mb-1">To</label>
                <input id="end_date" name="end_date" type="date" class="form-control" value="{{ $endDate->toDateString() }}" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Apply</button>
            <a href="{{ route('crm.reports.index') }}" class="btn btn-outline-secondary">This month</a>
        </form>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="small text-muted mb-3">
        Period: {{ $startDate->format('M j, Y') }} – {{ $endDate->format('M j, Y') }}
        <span class="ms-2">Current outstanding, pipeline, and open tasks are live snapshots.</span>
    </div>

    <div class="report-metric-grid mb-4">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Collected in period</div>
            <div class="fs-4 fw-semibold">₱{{ number_format($stats['collected'], 2) }}</div>
            <div class="small text-muted">{{ number_format($stats['payment_count']) }} {{ $stats['payment_count'] === 1 ? 'payment' : 'payments' }}</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Invoices issued</div>
            <div class="fs-4 fw-semibold">₱{{ number_format($stats['invoiced'], 2) }}</div>
            <div class="small text-muted">{{ number_format($stats['invoice_count']) }} {{ $stats['invoice_count'] === 1 ? 'invoice' : 'invoices' }}</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">New customers</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['new_customers']) }}</div>
            <div class="small text-muted">Added during this period</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">New leads</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['new_leads']) }}</div>
            <div class="small text-muted">Created during this period</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Jobs created</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['jobs_created']) }}</div>
            <div class="small text-muted">Created during this period</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Jobs completed</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['jobs_completed']) }}</div>
            <div class="small text-muted">Completed during this period</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Current outstanding</div>
            <div class="fs-4 fw-semibold">₱{{ number_format($stats['current_outstanding'], 2) }}</div>
            <div class="small text-muted">Unpaid invoice balances</div>
        </div></div>
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-muted">Open sales pipeline</div>
            <div class="fs-4 fw-semibold">₱{{ number_format($stats['open_pipeline'], 2) }}</div>
            <div class="small text-muted">{{ number_format($stats['open_tasks']) }} open {{ $stats['open_tasks'] === 1 ? 'task' : 'tasks' }}</div>
        </div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Collections by month</h2>
                    <div class="small text-muted">Payments recorded within the selected period</div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Month</th><th class="text-end">Collected</th></tr></thead>
                        <tbody>
                        @forelse ($collectionsByMonth as $month)
                            <tr><td>{{ $month['month'] }}</td><td class="text-end">₱{{ number_format($month['total'], 2) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">No payments recorded in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Invoices by status</h2>
                    <div class="small text-muted">Invoices dated within the selected period</div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Status</th><th class="text-end">Count</th><th class="text-end">Invoiced</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                        @forelse ($invoiceStatuses as $row)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td>
                                <td class="text-end">{{ number_format($row->invoice_count) }}</td>
                                <td class="text-end">₱{{ number_format($row->total_amount, 2) }}</td>
                                <td class="text-end">₱{{ number_format($row->balance_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No invoices in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Lead status</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Status</th><th class="text-end">Leads</th></tr></thead>
                        <tbody>
                        @forelse ($leadStatuses as $row)
                            <tr><td>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td><td class="text-end">{{ number_format($row->lead_count) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">No new leads in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Jobs created by status</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Status</th><th class="text-end">Jobs</th></tr></thead>
                        <tbody>
                        @forelse ($jobStatuses as $row)
                            <tr><td>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td><td class="text-end">{{ number_format($row->job_count) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">No jobs created in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Open pipeline by stage</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Stage</th><th class="text-end">Deals</th><th class="text-end">Value</th></tr></thead>
                        <tbody>
                        @forelse ($opportunityStages as $row)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $row->stage)) }}</td>
                                <td class="text-end">{{ number_format($row->opportunity_count) }}</td>
                                <td class="text-end">₱{{ number_format($row->pipeline_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No open opportunities.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-1">Recent payments</h2>
            <div class="small text-muted">Latest payments within the selected period</div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse ($recentPayments as $payment)
                    <tr>
                        <td class="text-nowrap">{{ $payment->payment_date?->format('M j, Y') }}</td>
                        <td>
                            @if ($payment->invoice)
                                <a href="{{ route('crm.invoices.show', $payment->invoice) }}" class="text-decoration-none">{{ $payment->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $payment->invoice?->customer?->display_name ?? '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="text-end fw-semibold">₱{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No payments recorded in this period.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
