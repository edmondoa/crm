
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payment Details</h1>
            <p class="text-muted mb-0">
                {{ $payment->payment_number }}
            </p>
        </div>

        <a href="{{ route('crm.payments.index') }}"
           class="btn btn-outline-secondary">
            Back to Payments
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <small class="text-muted">Payment Number</small>
                    <div class="fw-semibold">
                        {{ $payment->payment_number }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Invoice</small>
                    <div class="fw-semibold">
                        {{ $payment->invoice->invoice_number
                            ?? 'Invoice #' . $payment->invoice_id }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Payment Date</small>
                    <div>
                        {{ $payment->payment_date->format('F d, Y') }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Payment Method</small>
                    <div>
                        {{ str($payment->payment_method)
                            ->replace('_', ' ')
                            ->title() }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Amount Received</small>
                    <div class="fs-4 fw-bold text-success">
                        {{ number_format($payment->amount, 2) }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Reference Number</small>
                    <div>
                        {{ $payment->reference_number ?: '—' }}
                    </div>
                </div>

                <div class="col-12">
                    <small class="text-muted">Notes</small>
                    <div>{{ $payment->notes ?: '—' }}</div>
                </div>

                <div class="col-12">
                    <small class="text-muted">Recorded By</small>
                    <div>
                        {{ $payment->creator->name ?? 'Unavailable' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
