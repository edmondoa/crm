<div class="row g-4">

    {{-- =========================
         ESTIMATE INFORMATION
    ========================== --}}

    <div class="col-lg-8">

        <div class="erp-card">

            <div class="erp-card-header">
                <h5 class="mb-0">
                    Estimate Information
                </h5>
            </div>

            <div class="erp-card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Customer
                        </label>

                        <select
                            name="customer_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Customer
                            </option>

                            @foreach($customers as $customer)

                                <option
                                    value="{{ $customer->id }}"
                                    @selected(
                                        old(
                                            'customer_id',
                                            $estimate->customer_id ?? null
                                        ) == $customer->id
                                    )
                                >
                                    {{ $customer->display_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Job
                        </label>

                        <select
                            name="job_id"
                            class="form-select"
                        >

                            <option value="">
                                Select Job
                            </option>

                            @foreach($jobs as $job)

                                <option
                                    value="{{ $job->id }}"
                                    @selected(
                                        old(
                                            'job_id',
                                            $estimate->job_id ?? null
                                        ) == $job->id
                                    )
                                >
                                    {{ $job->job_number }}
                                    —
                                    {{ $job->title }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Property
                        </label>

                        <select
                            name="property_id"
                            class="form-select"
                        >

                            <option value="">
                                Select Property
                            </option>

                            @foreach($properties as $property)

                                <option
                                    value="{{ $property->id }}"
                                    @selected(
                                        old(
                                            'property_id',
                                            $estimate->property_id ?? null
                                        ) == $property->id
                                    )
                                >
                                    {{ $property->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Estimate Date
                        </label>

                        <input
                            type="date"
                            name="estimate_date"
                            class="form-control"
                            value="{{ old(
                                'estimate_date',
                                isset($estimate)
                                    ? $estimate->estimate_date?->format('Y-m-d')
                                    : now()->format('Y-m-d')
                            ) }}"
                            required
                        >

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Expiration
                        </label>

                        <input
                            type="date"
                            name="expiration_date"
                            class="form-control"
                            value="{{ old(
                                'expiration_date',
                                isset($estimate)
                                    ? $estimate->expiration_date?->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            @foreach([
                                'draft',
                                'sent',
                                'accepted',
                                'rejected',
                                'expired',
                                'converted'
                            ] as $status)

                                <option
                                    value="{{ $status }}"
                                    @selected(
                                        old(
                                            'status',
                                            $estimate->status ?? 'draft'
                                        ) === $status
                                    )
                                >
                                    {{ ucfirst($status) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================
         SUMMARY
    ========================== --}}

    <div class="col-lg-4">

        <div class="erp-card">

            <div class="erp-card-header">
                <h5 class="mb-0">
                    Estimate Summary
                </h5>
            </div>

            <div class="erp-card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <strong id="summarySubtotal">
                        ₱0.00
                    </strong>
                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Discount
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="discount"
                        id="discount"
                        class="form-control"
                        value="{{ old(
                            'discount',
                            $estimate->discount ?? 0
                        ) }}"
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Tax
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="tax"
                        id="tax"
                        class="form-control"
                        value="{{ old(
                            'tax',
                            $estimate->tax ?? 0
                        ) }}"
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Other Charges
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="other_charges"
                        id="other_charges"
                        class="form-control"
                        value="{{ old(
                            'other_charges',
                            $estimate->other_charges ?? 0
                        ) }}"
                    >

                </div>

                <hr>

                <div class="d-flex justify-content-between">

                    <span class="fw-semibold">
                        Grand Total
                    </span>

                    <strong
                        class="fs-4"
                        id="summaryGrandTotal"
                    >
                        ₱0.00
                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================
     ITEMS
========================== --}}

<div class="erp-card mt-4">

    <div class="erp-card-header d-flex justify-content-between">

        <h5 class="mb-0">
            Job Items
        </h5>

        <button
            type="button"
            class="btn btn-sm btn-primary"
            id="addEstimateItem"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Add Item
        </button>

    </div>

    <div class="erp-card-body p-0">

        <div class="table-responsive">

            <table
                class="table erp-table mb-0"
                id="estimateItemsTable"
            >

                <thead>

                    <tr>
                        <th style="width:120px">
                            Type
                        </th>

                        <th>
                            Description
                        </th>

                        <th style="width:100px">
                            Qty
                        </th>

                        <th style="width:100px">
                            Unit
                        </th>

                        <th style="width:130px">
                            Cost
                        </th>

                        <th style="width:110px">
                            Markup %
                        </th>

                        <th style="width:130px">
                            Unit Price
                        </th>

                        <th style="width:100px">
                            Tax %
                        </th>

                        <th
                            style="width:130px"
                            class="text-end"
                        >
                            Total
                        </th>

                        <th style="width:50px"></th>
                    </tr>

                </thead>

                <tbody id="estimateItemsBody">

                    @if(isset($estimate) && $estimate->items->count())

                        @foreach($estimate->items as $index => $item)

                            @include(
                                'crm.estimates.item-row',
                                [
                                    'index' => $index,
                                    'item' => $item
                                ]
                            )

                        @endforeach

                    @else

                        @include(
                            'crm.estimates.item-row',
                            [
                                'index' => 0,
                                'item' => null
                            ]
                        )

                    @endif

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================
     NOTES
========================== --}}

<div class="row g-4 mt-1">

    <div class="col-lg-6">

        <div class="erp-card">

            <div class="erp-card-header">
                Notes
            </div>

            <div class="erp-card-body">

                <textarea
                    name="notes"
                    class="form-control"
                    rows="5"
                >{{ old(
                    'notes',
                    $estimate->notes ?? ''
                ) }}</textarea>

            </div>

        </div>

    </div>


    <div class="col-lg-6">

        <div class="erp-card">

            <div class="erp-card-header">
                Terms & Conditions
            </div>

            <div class="erp-card-body">

                <textarea
                    name="terms"
                    class="form-control"
                    rows="5"
                >{{ old(
                    'terms',
                    $estimate->terms ?? ''
                ) }}</textarea>

            </div>

        </div>

    </div>

</div>


<div class="d-flex justify-content-end gap-2 mt-4">

    <a
        href="{{ route('crm.estimates.index') }}"
        class="btn btn-outline-secondary"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        Save Estimate
    </button>

</div>


@include('crm.estimates.item-template')

@push('scripts')
    @vite('resources/js/estimate.js')
@endpush