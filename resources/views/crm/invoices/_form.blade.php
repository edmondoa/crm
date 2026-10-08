@php
    $isEdit = ($mode ?? 'create') === 'edit';

    /*
     * Preserve submitted items after validation errors.
     */
    $submittedItems = old('items');

    if ($submittedItems !== null) {

        // Validation failed: preserve what the user submitted.
        $items = collect($submittedItems)
            ->map(fn ($item) => (object) $item);

    } elseif ($isEdit && $invoice) {

        // Edit existing invoice.
        $items = $invoice->items
            ->sortBy('sort_order')
            ->values();

    } elseif (!empty($estimate) && $estimate->items->isNotEmpty()) {

        // Creating invoice from an estimate.
        $items = $estimate->items
            ->sortBy('sort_order')
            ->values()
            ->map(function ($item) {
                return (object) [
                    'id' => null,

                    'item_type' => $item->item_type ?? 'service',

                    'description' => $item->description ?? '',

                    'sku' => $item->sku ?? '',

                    'quantity' => $item->quantity ?? 1,

                    'unit' => $item->unit ?? 'unit',

                    'unit_price' => $item->unit_price ?? 0,

                    'discount_percent' =>
                        $item->discount_percent ?? 0,

                    'tax_percent' =>
                        $item->tax_percent ?? 0,

                    'notes' => $item->notes ?? '',
                ];
            });

    } else {

        // Normal new invoice.
        $items = collect([
            (object) [
                'id' => null,
                'item_type' => 'service',
                'description' => '',
                'sku' => '',
                'quantity' => 1,
                'unit' => 'unit',
                'unit_price' => 0,
                'discount_percent' => 0,
                'tax_percent' => 0,
                'notes' => '',
            ]
        ]);
    }
@endphp


{{-- Main Information --}}
<div class="crm-card mb-4">

    <div class="crm-card-header">

        <h5 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>
            Invoice Information
        </h5>

    </div>


    <div class="crm-card-body">

        <div class="row g-3">


            {{-- Customer --}}
            <div class="col-md-6">

                <label class="form-label required">
                    Customer
                </label>

                <select name="customer_id"
                        id="customer_id"
                        class="form-select @error('customer_id') is-invalid @enderror"
                        required>

                    <option value="">
                        Select Customer
                    </option>

                    @foreach($customers as $customer)

                        @php
                            $customerName =
                                $customer->name
                                ?? $customer->company_name
                                ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                        @endphp

                        <option value="{{ $customer->id }}"
                            @selected(old('customer_id', $invoice->customer_id ?? '') == $customer->id)>

                            {{ $customerName ?: 'Customer #' . $customer->id }}

                        </option>

                    @endforeach

                </select>

                @error('customer_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Property --}}
            <div class="col-md-6">

                <label class="form-label">
                    Property
                </label>

                <select name="property_id"
                        id="property_id"
                        class="form-select @error('property_id') is-invalid @enderror">

                    <option value="">
                        Select Property
                    </option>

                    @foreach($properties as $property)

                        <option value="{{ $property->id }}"
                            data-customer-id="{{ $property->customer_id }}"
                            @selected(old('property_id', $invoice->property_id ?? '') == $property->id)>

                            {{ $property->name ?? $property->address ?? ('Property #' . $property->id) }}

                        </option>

                    @endforeach

                </select>

                @error('property_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Job --}}
            <div class="col-md-6">

                <label class="form-label">
                    Job / Work Order
                </label>

                <select name="job_id"
                        id="job_id"
                        class="form-select @error('job_id') is-invalid @enderror">

                    <option value="">
                        Select Job
                    </option>

                    @foreach($jobs as $job)

                        <option value="{{ $job->id }}"
                            @selected(old('job_id', $invoice->job_id ?? '') == $job->id)>

                            {{ $job->job_number
                                ?? $job->title
                                ?? $job->name
                                ?? ('Job #' . $job->id) }}

                        </option>

                    @endforeach

                </select>

                @error('job_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Estimate --}}
            <div class="col-md-6">

                <label class="form-label">
                    Source Estimate
                </label>

                <select name="estimate_id"
                        id="estimate_id"
                        class="form-select @error('estimate_id') is-invalid @enderror">

                    <option value="">
                        No Estimate
                    </option>

                    @foreach($estimates as $estimate)

                        <option value="{{ $estimate->id }}"
                            @selected(old('estimate_id', $invoice->estimate_id ?? request('estimate_id')) == $estimate->id)>

                            {{ $estimate->estimate_number }}

                            @if($estimate->customer)
                                — {{ $estimate->customer->name
                                    ?? $estimate->customer->company_name
                                    ?? '' }}
                            @endif

                        </option>

                    @endforeach

                </select>

                @error('estimate_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Invoice Date --}}
            <div class="col-md-4">

                <label class="form-label required">
                    Invoice Date
                </label>

                <input type="date"
                       name="invoice_date"
                       class="form-control @error('invoice_date') is-invalid @enderror"
                       value="{{ old(
                           'invoice_date',
                           isset($invoice->invoice_date)
                               ? $invoice->invoice_date->format('Y-m-d')
                               : now()->format('Y-m-d')
                       ) }}"
                       required>

                @error('invoice_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Due Date --}}
            <div class="col-md-4">

                <label class="form-label required">
                    Due Date
                </label>

                <input type="date"
                       name="due_date"
                       class="form-control @error('due_date') is-invalid @enderror"
                       value="{{ old(
                           'due_date',
                           isset($invoice->due_date)
                               ? $invoice->due_date->format('Y-m-d')
                               : now()->addDays(30)->format('Y-m-d')
                       ) }}"
                       required>

                @error('due_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Status --}}
            <div class="col-md-4">

                <label class="form-label required">
                    Status
                </label>

                <select name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required>

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
                            @selected(old('status', $invoice->status ?? 'draft') === $status)>

                            {{ ucfirst($status) }}

                        </option>

                    @endforeach

                </select>

                @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>

</div>



{{-- Items --}}
<div class="crm-card mb-4">

    <div class="crm-card-header d-flex justify-content-between align-items-center">

        <div>

            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Invoice Items
            </h5>

            <div class="small text-muted mt-1">
                Add labor, materials, services, equipment, or other charges.
            </div>

        </div>


        <button type="button"
                class="btn btn-sm btn-primary"
                id="addInvoiceItem">

            <i class="bi bi-plus-lg me-1"></i>
            Add Item

        </button>

    </div>


    <div class="crm-card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered invoice-items-table mb-0">

                <thead>

                    <tr>

                        <th style="width: 110px;">
                            Type
                        </th>

                        <th style="min-width: 220px;">
                            Description
                        </th>

                        <th style="width: 100px;">
                            Qty
                        </th>

                        <th style="width: 100px;">
                            Unit
                        </th>

                        <th style="width: 130px;">
                            Unit Price
                        </th>

                        <th style="width: 110px;">
                            Discount %
                        </th>

                        <th style="width: 110px;">
                            Tax %
                        </th>

                        <th style="width: 140px;"
                            class="text-end">

                            Total

                        </th>

                        <th style="width: 50px;"></th>

                    </tr>

                </thead>


                <tbody id="invoiceItems">

                    @foreach($items as $index => $item)

                        @include('crm.invoices.item-row', [
                            'index' => $index,
                            'item' => $item
                        ])

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- Totals --}}
<div class="row justify-content-end mb-4">

    <div class="col-lg-5">

        <div class="crm-card">

            <div class="crm-card-header">

                <h5 class="mb-0">
                    Invoice Summary
                </h5>

            </div>


            <div class="crm-card-body">

                <div class="invoice-summary-row">

                    <span>
                        Subtotal
                    </span>

                    <strong id="invoiceSubtotal">
                        ₱0.00
                    </strong>

                </div>


                <div class="invoice-summary-row">

                    <span>
                        Discount
                    </span>

                    <strong id="invoiceDiscount">
                        ₱0.00
                    </strong>

                </div>


                <div class="invoice-summary-row">

                    <span>
                        Tax
                    </span>

                    <strong id="invoiceTax">
                        ₱0.00
                    </strong>

                </div>


                <div class="invoice-summary-row">

                    <div class="d-flex align-items-center gap-2">

                        <span>
                            Other Charges
                        </span>

                        <input type="number"
                               name="other_charges"
                               id="otherCharges"
                               class="form-control form-control-sm invoice-other-charge"
                               min="0"
                               step="0.01"
                               value="{{ old('other_charges', $invoice->other_charges ?? 0) }}">

                    </div>

                    <strong id="invoiceOtherCharges">
                        ₱0.00
                    </strong>

                </div>


                <hr>


                <div class="invoice-grand-total">

                    <span>
                        Grand Total
                    </span>

                    <strong id="invoiceGrandTotal">
                        ₱0.00
                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- Notes / Terms --}}
<div class="crm-card mb-4">

    <div class="crm-card-header">

        <h5 class="mb-0">
            <i class="bi bi-card-text me-2"></i>
            Notes & Terms
        </h5>

    </div>


    <div class="crm-card-body">

        <div class="row g-3">

            <div class="col-md-6">

                <label class="form-label">
                    Customer Notes
                </label>

                <textarea name="notes"
                          rows="5"
                          class="form-control"
                          placeholder="Notes displayed on the invoice...">{{ old('notes', $invoice->notes ?? '') }}</textarea>

            </div>


            <div class="col-md-6">

                <label class="form-label">
                    Terms & Conditions
                </label>

                <textarea name="terms"
                          rows="5"
                          class="form-control"
                          placeholder="Payment terms and conditions...">{{ old('terms', $invoice->terms ?? '') }}</textarea>

            </div>

        </div>

    </div>

</div>



{{-- Footer actions --}}
<div class="invoice-form-actions">

    <a href="{{ $isEdit
        ? route('crm.invoices.show', $invoice)
        : route('crm.invoices.index') }}"
       class="btn btn-outline-secondary">

        Cancel

    </a>


    <button type="submit"
            class="btn btn-primary">

        <i class="bi bi-check-lg me-1"></i>

        {{ $isEdit ? 'Update Invoice' : 'Create Invoice' }}

    </button>

</div>