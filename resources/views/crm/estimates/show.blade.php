@extends('layouts.admin')

@section('title', $estimate->estimate_number)

@section('page-title', $estimate->estimate_number)

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                {{ $estimate->estimate_number }}
            </h1>

            <p class="page-subtitle">
                Job estimate / quotation
            </p>

        </div>

        <div class="d-flex gap-2">
            @if($estimate->invoices->isNotEmpty())
                <a href="{{ route('crm.invoices.show', $estimate->invoices->first()) }}"
                class="btn btn-outline-primary">
                    <i class="bi bi-receipt"></i>
                    View Invoice
                </a>
            @elseif(in_array($estimate->status, ['accepted', 'approved']))
                <a href="{{ route('crm.invoices.create', ['estimate_id' => $estimate->id]) }}"
                class="btn btn-success">
                    <i class="bi bi-receipt"></i>
                    Create Invoice
                </a>
            @endif

            <a
                href="{{ route('crm.estimates.edit', $estimate) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route('crm.estimates.index') }}"
                class="btn btn-outline-secondary"
            >
                Back
            </a>

        </div>

    </div>

</div>


<div class="row g-4">

    <div class="col-lg-8">

        <div class="erp-card">

            <div class="erp-card-body">

                <div class="row">

                    <div class="col-md-6">

                        <small class="text-muted">
                            CUSTOMER
                        </small>

                        <div class="fw-semibold">
                            {{ $estimate->customer->name ?? '—' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted">
                            JOB
                        </small>

                        <div class="fw-semibold">

                            @if($estimate->job)

                                {{ $estimate->job->job_number }}

                                @if($estimate->job->title)
                                    — {{ $estimate->job->title }}
                                @endif

                            @else

                                —

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6 mt-3">

                        <small class="text-muted">
                            ESTIMATE DATE
                        </small>

                        <div>
                            {{ $estimate->estimate_date?->format('M d, Y') }}
                        </div>

                    </div>


                    <div class="col-md-6 mt-3">

                        <small class="text-muted">
                            EXPIRATION
                        </small>

                        <div>
                            {{ $estimate->expiration_date?->format('M d, Y') ?? '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="erp-card mt-4">

            <div class="erp-card-header">
                <h5 class="mb-0">
                    Job Items
                </h5>
            </div>

            <div class="table-responsive">

                <table class="table erp-table mb-0">

                    <thead>

                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">Qty</th>
                            <th>Unit</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($estimate->items as $item)

                            <tr>

                                <td>
                                    {{ ucfirst($item->item_type) }}
                                </td>

                                <td>
                                    {{ $item->description }}
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
                                    ₱{{ number_format($item->tax_amount, 2) }}
                                </td>

                                <td class="text-end fw-semibold">
                                    ₱{{ number_format($item->total, 2) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="erp-card">

            <div class="erp-card-header">
                Estimate Summary
            </div>

            <div class="erp-card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>

                    <span>
                        ₱{{ number_format($estimate->subtotal, 2) }}
                    </span>
                </div>


                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>

                    <span>
                        ₱{{ number_format($estimate->discount, 2) }}
                    </span>
                </div>


                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>

                    <span>
                        ₱{{ number_format($estimate->tax, 2) }}
                    </span>
                </div>


                <div class="d-flex justify-content-between mb-3">
                    <span>Other Charges</span>

                    <span>
                        ₱{{ number_format($estimate->other_charges, 2) }}
                    </span>
                </div>


                <hr>


                <div class="d-flex justify-content-between">

                    <strong>
                        Grand Total
                    </strong>

                    <strong class="fs-4">
                        ₱{{ number_format($estimate->grand_total, 2) }}
                    </strong>

                </div>

            </div>

        </div>


        <div class="erp-card mt-4">

            <div class="erp-card-header">
                Status
            </div>

            <div class="erp-card-body">

                <span class="badge bg-primary">
                    {{ ucfirst($estimate->status) }}
                </span>

            </div>

        </div>

    </div>

</div>


@if($estimate->notes)

<div class="erp-card mt-4">

    <div class="erp-card-header">
        Notes
    </div>

    <div class="erp-card-body">
        {!! nl2br(e($estimate->notes)) !!}
    </div>

</div>

@endif


@if($estimate->terms)

<div class="erp-card mt-4">

    <div class="erp-card-header">
        Terms & Conditions
    </div>

    <div class="erp-card-body">
        {!! nl2br(e($estimate->terms)) !!}
    </div>

</div>

@endif

@endsection