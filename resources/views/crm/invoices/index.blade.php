@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')

<div class="crm-page">

    {{-- Header --}}
    <div class="page-header mb-4">
        <div class="page-header-row">

            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt me-2"></i>
                    Invoices
                </h1>

                <p class="page-subtitle">
                    Manage customer invoices, billing, and outstanding balances.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('crm.invoices.create') }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    New Invoice
                </a>
            </div>

        </div>
    </div>


    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Summary --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="crm-stat-card">
                <div class="crm-stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <div class="crm-stat-label">
                        Total Invoices
                    </div>

                    <div class="crm-stat-value">
                        {{ $invoices->total() }}
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="crm-stat-card">
                <div class="crm-stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <div>
                    <div class="crm-stat-label">
                        Total Invoiced
                    </div>

                    <div class="crm-stat-value">
                        ₱{{ number_format($totalInvoiced ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="crm-stat-card">
                <div class="crm-stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <div>
                    <div class="crm-stat-label">
                        Outstanding
                    </div>

                    <div class="crm-stat-value">
                        ₱{{ number_format($totalOutstanding ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="crm-stat-card">
                <div class="crm-stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <div class="crm-stat-label">
                        Paid
                    </div>

                    <div class="crm-stat-value">
                        ₱{{ number_format($totalPaid ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Filters --}}
    <div class="crm-card mb-4">

        <div class="crm-card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Filters
            </h5>
        </div>

        <div class="crm-card-body">

            <form method="GET"
                  action="{{ route('crm.invoices.index') }}">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Invoice number or customer">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Statuses
                            </option>

                            @foreach([
                                'draft',
                                'sent',
                                'viewed',
                                'partial',
                                'paid',
                                'overdue',
                                'cancelled'
                            ] as $status)

                                <option value="{{ $status }}"
                                    @selected(request('status') === $status)>
                                    {{ ucfirst($status) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input type="date"
                               name="date_from"
                               class="form-control"
                               value="{{ request('date_from') }}">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input type="date"
                               name="date_to"
                               class="form-control"
                               value="{{ request('date_to') }}">

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <div class="d-flex gap-2 w-100">

                            <button type="submit"
                                    class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i>
                                Search
                            </button>

                            <a href="{{ route('crm.invoices.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Invoice table --}}
    <div class="crm-card">

        <div class="crm-card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Invoice List
            </h5>

            <span class="text-muted small">
                {{ $invoices->total() }} invoice(s)
            </span>

        </div>


        <div class="table-responsive">

            <table class="table crm-table align-middle mb-0">

                <thead>

                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($invoices as $invoice)

                    <tr>

                        <td>

                            <a href="{{ route('crm.invoices.show', $invoice) }}"
                               class="fw-semibold text-decoration-none">

                                {{ $invoice->invoice_number }}

                            </a>

                        </td>


                        <td>

                            @if($invoice->customer)

                                <div class="fw-semibold">
                                    {{ $invoice->customer->name
                                        ?? $invoice->customer->company_name
                                        ?? '—' }}
                                </div>

                                @if($invoice->property)
                                    <div class="small text-muted">
                                        {{ $invoice->property->name ?? '' }}
                                    </div>
                                @endif

                            @else
                                —
                            @endif

                        </td>


                        <td>
                            {{ optional($invoice->invoice_date)->format('M d, Y') }}
                        </td>


                        <td>

                            {{ optional($invoice->due_date)->format('M d, Y') }}

                            @if(
                                $invoice->due_date &&
                                $invoice->balance_due > 0 &&
                                $invoice->due_date->isPast() &&
                                !in_array($invoice->status, ['paid', 'cancelled'])
                            )

                                <div class="small text-danger">
                                    <i class="bi bi-exclamation-circle"></i>
                                    Overdue
                                </div>

                            @endif

                        </td>


                        <td class="text-end">
                            ₱{{ number_format($invoice->grand_total, 2) }}
                        </td>


                        <td class="text-end">
                            ₱{{ number_format($invoice->amount_paid, 2) }}
                        </td>


                        <td class="text-end fw-semibold">

                            ₱{{ number_format($invoice->balance_due, 2) }}

                        </td>


                        <td>

                            @php

                                $statusClass = match($invoice->status) {
                                    'draft' => 'secondary',
                                    'sent' => 'primary',
                                    'viewed' => 'info',
                                    'partial' => 'warning',
                                    'paid' => 'success',
                                    'overdue' => 'danger',
                                    'cancelled' => 'dark',
                                    default => 'secondary',
                                };

                            @endphp

                            <span class="badge text-bg-{{ $statusClass }}">
                                {{ ucfirst($invoice->status) }}
                            </span>

                        </td>


                        <td class="text-end">

                            <div class="dropdown">

                                <button class="btn btn-sm btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="dropdown">

                                    <i class="bi bi-three-dots-vertical"></i>

                                </button>


                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('crm.invoices.show', $invoice) }}">

                                            <i class="bi bi-eye me-2"></i>
                                            View

                                        </a>
                                    </li>


                                    @if($invoice->status === 'draft')

                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('crm.invoices.edit', $invoice) }}">

                                                <i class="bi bi-pencil me-2"></i>
                                                Edit

                                            </a>
                                        </li>

                                    @endif


                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                onclick="window.print()">

                                            <i class="bi bi-printer me-2"></i>
                                            Print

                                        </button>
                                    </li>


                                    @if($invoice->status === 'draft')

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>

                                            <form method="POST"
                                                  action="{{ route('crm.invoices.destroy', $invoice) }}"
                                                  onsubmit="return confirm('Delete this draft invoice?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="dropdown-item text-danger">

                                                    <i class="bi bi-trash me-2"></i>
                                                    Delete

                                                </button>

                                            </form>

                                        </li>

                                    @endif

                                </ul>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9"
                            class="text-center py-5">

                            <div class="empty-state">

                                <i class="bi bi-receipt"></i>

                                <h5>
                                    No invoices found
                                </h5>

                                <p class="text-muted">
                                    Create your first invoice to get started.
                                </p>

                                <a href="{{ route('crm.invoices.create') }}"
                                   class="btn btn-primary">

                                    <i class="bi bi-plus-lg me-1"></i>
                                    Create Invoice

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($invoices->hasPages())

            <div class="crm-card-footer">

                {{ $invoices->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/invoices.css') }}">
@endpush