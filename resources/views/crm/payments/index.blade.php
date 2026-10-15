
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payments</h1>
            <p class="text-muted mb-0">
                Manage customer invoice payments.
            </p>
        </div>

        <a href="{{ route('crm.payments.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Record Payment
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Payment No.</th>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                            <th>Recorded By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $payment->payment_number }}
                                    </strong>
                                </td>
                                <td>
                                    {{ $payment->invoice->invoice_number
                                        ?? 'Invoice #' . $payment->invoice_id }}
                                </td>
                                <td>
                                    {{ $payment->payment_date
                                        ->format('M d, Y') }}
                                </td>
                                <td>
                                    {{ str($payment->payment_method)
                                        ->replace('_', ' ')
                                        ->title() }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($payment->amount, 2) }}
                                </td>
                                <td>
                                    {{ $payment->creator->name
                                        ?? 'System / unavailable' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route(
                                        'crm.payments.show', $payment
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="text-center text-muted py-4">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
