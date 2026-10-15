
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3">Record Payment</h1>
        <p class="text-muted">
            Record a customer payment against an invoice.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('crm.payments.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="invoice_id" class="form-label">
                            Invoice
                        </label>
                        <select name="invoice_id"
                                id="invoice_id"
                                class="form-select"
                                required>
                            <option value="">Select invoice</option>

                            @foreach ($invoices as $invoice)
                                <option
                                    value="{{ $invoice->id }}"
                                    @selected(
                                        old('invoice_id') == $invoice->id
                                    )>
                                    {{ $invoice->invoice_number
                                        ?? 'Invoice #' . $invoice->id }}
                                    — Total:
                                    {{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label">
                            Payment Amount
                        </label>
                        <input type="number"
                               name="amount"
                               id="amount"
                               class="form-control"
                               min="0.01"
                               step="0.01"
                               value="{{ old('amount') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">
                            Payment Date
                        </label>
                        <input type="date"
                               name="payment_date"
                               id="payment_date"
                               class="form-control"
                               value="{{ old(
                                   'payment_date',
                                   now()->toDateString()
                               ) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">
                            Payment Method
                        </label>
                        <select name="payment_method"
                                id="payment_method"
                                class="form-select"
                                required>
                            <option value="">Select method</option>
                            @foreach ([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'check' => 'Check',
                                'other' => 'Other',
                            ] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(
                                        old('payment_method') === $value
                                    )>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="reference_number"
                               class="form-label">
                            Reference Number
                        </label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               class="form-control"
                               value="{{ old('reference_number') }}"
                               maxlength="100">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Notes
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  class="form-control"
                                  rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('crm.payments.index') }}"
                       class="btn btn-light">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
