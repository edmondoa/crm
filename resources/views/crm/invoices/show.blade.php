@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')

<div class="crm-page">

    {{-- Page Header --}}
    <div class="page-header mb-4 no-print">

        <div class="page-header-row">

            <div>

                <div class="page-breadcrumb mb-2">

                    <a href="{{ route('crm.invoices.index') }}">
                        Invoices
                    </a>

                    <i class="bi bi-chevron-right mx-1"></i>

                    <span>
                        {{ $invoice->invoice_number }}
                    </span>

                </div>


                <h1 class="page-title">
                    {{ $invoice->invoice_number }}
                </h1>


                <p class="page-subtitle">
                    Invoice details and billing information.
                </p>

            </div>


            <div class="d-flex gap-2">

                @if($invoice->status === 'draft')

                    <a href="{{ route('crm.invoices.edit', $invoice) }}"
                       class="btn btn-primary">

                        <i class="bi bi-pencil me-1"></i>
                        Edit

                    </a>

                @endif


                <button type="button"
                        class="btn btn-outline-secondary"
                        onclick="window.print()">

                    <i class="bi bi-printer me-1"></i>
                    Print

                </button>


                <a href="{{ route('crm.invoices.index') }}"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back

                </a>

            </div>

        </div>

    </div>


    {{-- Success --}}
    @if(session('success'))

        <div class="alert alert-success no-print">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

        </div>

    @endif


    {{-- Invoice --}}
    <div class="invoice-document">

        {{-- Invoice header --}}
        <div class="invoice-document-header">

            <div>

                <h1 class="invoice-document-title">
                    INVOICE
                </h1>

                <div class="invoice-number">
                    {{ $invoice->invoice_number }}
                </div>

            </div>


            <div class="invoice-company">

                <div class="company-name">
                    Your Company Name
                </div>

                <div>
                    Your Company Address
                </div>

                <div>
                    Ormoc City, Leyte
                </div>

                <div>
                    Philippines
                </div>

                <div>
                    Phone: +63 XXX XXX XXXX
                </div>

            </div>

        </div>


        <hr>


        {{-- Billing --}}
        <div class="row invoice-meta">

            <div class="col-md-6">

                <div class="invoice-section-label">
                    BILL TO
                </div>


                <div class="invoice-customer-name">

                    {{ $invoice->customer->name
                        ?? $invoice->customer->company_name
                        ?? 'Customer' }}

                </div>


                @if($invoice->property)

                    <div class="text-muted">

                        {{ $invoice->property->name
                            ?? $invoice->property->address
                            ?? '' }}

                    </div>

                @endif


                @if($invoice->customer->email ?? false)

                    <div class="text-muted">
                        {{ $invoice->customer->email }}
                    </div>

                @endif


                @if($invoice->customer->phone ?? false)

                    <div class="text-muted">
                        {{ $invoice->customer->phone }}
                    </div>

                @endif

            </div>


            <div class="col-md-6">

                <div class="invoice-meta-grid">

                    <div>
                        <span>Invoice Date</span>
                        <strong>
                            {{ optional($invoice->invoice_date)->format('M d, Y') }}
                        </strong>
                    </div>


                    <div>
                        <span>Due Date</span>
                        <strong>
                            {{ optional($invoice->due_date)->format('M d, Y') }}
                        </strong>
                    </div>


                    @if($invoice->job)

                        <div>
                            <span>Job</span>
                            <strong>
                                {{ $invoice->job->job_number
                                    ?? $invoice->job->title
                                    ?? $invoice->job->name
                                    ?? ('Job #' . $invoice->job->id) }}
                            </strong>
                        </div>

                    @endif


                    @if($invoice->estimate)

                        <div>
                            <span>Estimate</span>
                            <strong>
                                {{ $invoice->estimate->estimate_number }}
                            </strong>
                        </div>

                    @endif


                    <div>
                        <span>Status</span>

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

                        <strong>
                            <span class="badge text-bg-{{ $statusClass }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        {{-- Items --}}
        <div class="table-responsive mt-5">

            <table class="table invoice-document-table">

                <thead>

                    <tr>

                        <th style="width: 50px;">
                            #
                        </th>

                        <th>
                            Description
                        </th>

                        <th class="text-end">
                            Qty
                        </th>

                        <th>
                            Unit
                        </th>

                        <th class="text-end">
                            Unit Price
                        </th>

                        <th class="text-end">
                            Tax
                        </th>

                        <th class="text-end">
                            Total
                        </th>

                    </tr>

                </thead>


                <tbody>

                @foreach($invoice->items as $index => $item)

                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>


                        <td>

                            <div class="fw-semibold">
                                {{ $item->description }}
                            </div>

                            @if($item->sku)

                                <div class="small text-muted">
                                    SKU: {{ $item->sku }}
                                </div>

                            @endif

                            @if($item->item_type)

                                <div class="small text-muted">
                                    {{ ucfirst($item->item_type) }}
                                </div>

                            @endif

                        </td>


                        <td class="text-end">
                            {{ number_format($item->quantity, 3) }}
                        </td>


                        <td>
                            {{ $item->unit }}
                        </td>


                        <td class="text-end">
                            ₱{{ number_format($item->unit_price, 2) }}
                        </td>


                        <td class="text-end">
                            {{ number_format($item->tax_percent ?? 0, 2) }}%
                        </td>


                        <td class="text-end fw-semibold">
                            ₱{{ number_format($item->line_total, 2) }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        {{-- Totals --}}
        <div class="row justify-content-end">

            <div class="col-md-5">

                <div class="invoice-total-box">

                    <div class="invoice-total-row">

                        <span>
                            Subtotal
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->subtotal, 2) }}
                        </strong>

                    </div>


                    <div class="invoice-total-row">

                        <span>
                            Discount
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->discount, 2) }}
                        </strong>

                    </div>


                    <div class="invoice-total-row">

                        <span>
                            Tax
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->tax, 2) }}
                        </strong>

                    </div>


                    <div class="invoice-total-row">

                        <span>
                            Other Charges
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->other_charges, 2) }}
                        </strong>

                    </div>


                    <hr>


                    <div class="invoice-total-grand">

                        <span>
                            Grand Total
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->grand_total, 2) }}
                        </strong>

                    </div>


                    <div class="invoice-total-row mt-3">

                        <span>
                            Amount Paid
                        </span>

                        <strong class="text-success">
                            ₱{{ number_format($invoice->amount_paid, 2) }}
                        </strong>

                    </div>


                    <div class="invoice-total-balance">

                        <span>
                            Balance Due
                        </span>

                        <strong>
                            ₱{{ number_format($invoice->balance_due, 2) }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        {{-- Notes --}}
        @if($invoice->notes)

            <div class="invoice-notes mt-5">

                <h6>
                    Notes
                </h6>

                <div>
                    {!! nl2br(e($invoice->notes)) !!}
                </div>

            </div>

        @endif


        {{-- Terms --}}
        @if($invoice->terms)

            <div class="invoice-notes mt-4">

                <h6>
                    Terms & Conditions
                </h6>

                <div>
                    {!! nl2br(e($invoice->terms)) !!}
                </div>

            </div>

        @endif


        <div class="invoice-footer mt-5">

            Thank you for your business.

        </div>

    </div>

</div>

@endsection

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/invoices.css') }}">
@endpush