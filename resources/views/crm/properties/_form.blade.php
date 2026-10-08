@php

    $property = $property ?? null;

    $selectedCustomerId = old(
        'customer_id',
        $property->customer_id ?? request('customer_id')
    );

    $selectedContactId = old(
        'primary_contact_id',
        $property->primary_contact_id ?? ''
    );

@endphp


{{-- CUSTOMER RELATIONSHIP --}}
<div class="form-section">

    <div class="form-section-title">
        Customer Relationship
    </div>

    <div class="form-section-description">
        Select the customer that owns or manages this property.
    </div>


    <div class="row g-3">

        <div class="col-md-7">

            <label
                for="customer_id"
                class="form-label"
            >
                Customer
                <span class="text-danger">*</span>
            </label>

            <select
                name="customer_id"
                id="customer_id"
                class="form-select @error('customer_id') is-invalid @enderror"
                required
            >

                <option value="">
                    Select Customer
                </option>

                @foreach($customers as $customer)

                    <option
                        value="{{ $customer->id }}"
                        {{ (string) $selectedCustomerId === (string) $customer->id ? 'selected' : '' }}
                    >
                        {{ $customer->display_name }}
                        — {{ $customer->customer_code }}
                    </option>

                @endforeach

            </select>

            @error('customer_id')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-5">

            <label
                for="primary_contact_id"
                class="form-label"
            >
                Primary Contact
            </label>

            <select
                name="primary_contact_id"
                id="primary_contact_id"
                class="form-select @error('primary_contact_id') is-invalid @enderror"
            >

                <option value="">
                    No Primary Contact
                </option>

                @foreach($contacts as $contact)

                    <option
                        value="{{ $contact->id }}"
                        {{ (string) $selectedContactId === (string) $contact->id ? 'selected' : '' }}
                    >
                        {{ $contact->display_name }}

                        @if($contact->job_title)
                            — {{ $contact->job_title }}
                        @endif

                    </option>

                @endforeach

            </select>

            @error('primary_contact_id')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

            <div class="form-text">
                Primary contact for this property.
            </div>

        </div>

    </div>

</div>


{{-- PROPERTY INFORMATION --}}
<div class="form-section">

    <div class="form-section-title">
        Property Information
    </div>

    <div class="form-section-description">
        Enter the basic property identification and classification.
    </div>


    <div class="row g-3">

        <div class="col-md-8">

            <label
                for="name"
                class="form-label"
            >
                Property Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $property->name ?? '') }}"
                placeholder="Downtown Office Portfolio"
                required
            >

            @error('name')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-4">

            <label
                for="property_type"
                class="form-label"
            >
                Property Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="property_type"
                id="property_type"
                class="form-select @error('property_type') is-invalid @enderror"
                required
            >

                <option
                    value="commercial"
                    {{ old('property_type', $property->property_type ?? 'commercial') === 'commercial' ? 'selected' : '' }}
                >
                    Commercial
                </option>

                <option
                    value="residential"
                    {{ old('property_type', $property->property_type ?? '') === 'residential' ? 'selected' : '' }}
                >
                    Residential
                </option>

                <option
                    value="industrial"
                    {{ old('property_type', $property->property_type ?? '') === 'industrial' ? 'selected' : '' }}
                >
                    Industrial
                </option>

                <option
                    value="office"
                    {{ old('property_type', $property->property_type ?? '') === 'office' ? 'selected' : '' }}
                >
                    Office
                </option>

                <option
                    value="retail"
                    {{ old('property_type', $property->property_type ?? '') === 'retail' ? 'selected' : '' }}
                >
                    Retail
                </option>

                <option
                    value="warehouse"
                    {{ old('property_type', $property->property_type ?? '') === 'warehouse' ? 'selected' : '' }}
                >
                    Warehouse
                </option>

                <option
                    value="multi_family"
                    {{ old('property_type', $property->property_type ?? '') === 'multi_family' ? 'selected' : '' }}
                >
                    Multi-Family
                </option>

                <option
                    value="land"
                    {{ old('property_type', $property->property_type ?? '') === 'land' ? 'selected' : '' }}
                >
                    Land
                </option>

                <option
                    value="other"
                    {{ old('property_type', $property->property_type ?? '') === 'other' ? 'selected' : '' }}
                >
                    Other
                </option>

            </select>

            @error('property_type')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-12">

            <label
                for="description"
                class="form-label"
            >
                Description
            </label>

            <textarea
                name="description"
                id="description"
                rows="3"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Brief description of the property..."
            >{{ old('description', $property->description ?? '') }}</textarea>

            @error('description')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>

    </div>

</div>


{{-- STATUS --}}
<div class="form-section">

    <div class="form-section-title">
        Status
    </div>

    <div class="form-section-description">
        Control whether this property is currently active in the CRM.
    </div>


    <div class="row g-3">

        <div class="col-md-6">

            <label
                for="status"
                class="form-label"
            >
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                id="status"
                class="form-select"
                required
            >

                <option
                    value="active"
                    {{ old('status', $property->status ?? 'active') === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="inactive"
                    {{ old('status', $property->status ?? '') === 'inactive' ? 'selected' : '' }}
                >
                    Inactive
                </option>

            </select>

        </div>

    </div>

</div>


{{-- NOTES --}}
<div class="form-section">

    <div class="form-section-title">
        Notes
    </div>

    <div class="form-section-description">
        Add additional information about this property.
    </div>


    <textarea
        name="notes"
        id="notes"
        rows="4"
        class="form-control @error('notes') is-invalid @enderror"
        placeholder="Additional property notes..."
    >{{ old('notes', $property->notes ?? '') }}</textarea>

    @error('notes')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- ACTIONS --}}
<div class="form-actions">

    <a
        href="{{ $property?->exists
            ? route('crm.properties.show', $property)
            : route('crm.properties.index') }}"
        class="btn btn-light"
    >
        Cancel
    </a>


    <button
        type="submit"
        class="btn btn-primary"
    >

        <i class="bi bi-check-lg me-1"></i>

        {{ $property?->exists
            ? 'Update Property'
            : 'Create Property' }}

    </button>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect =
        document.getElementById('customer_id');

    const contactSelect =
        document.getElementById('primary_contact_id');

    if (!customerSelect || !contactSelect) {
        return;
    }

    customerSelect.addEventListener(
        'change',
        function () {

            const customerId =
                this.value;

            if (!customerId) {

                contactSelect.innerHTML = `
                    <option value="">
                        No Primary Contact
                    </option>
                `;

                return;
            }

            contactSelect.innerHTML = `
                <option value="">
                    Loading contacts...
                </option>
            `;

            /*
             * Contacts will be loaded from the CRM endpoint.
             * This endpoint is added below.
             */

            fetch(
                `/crm/customers/${customerId}/contacts`
            )
                .then(response => response.json())
                .then(data => {

                    contactSelect.innerHTML = `
                        <option value="">
                            No Primary Contact
                        </option>
                    `;

                    data.forEach(contact => {

                        const option =
                            document.createElement('option');

                        option.value =
                            contact.id;

                        option.textContent =
                            contact.name +
                            (
                                contact.job_title
                                    ? ' — ' + contact.job_title
                                    : ''
                            );

                        contactSelect.appendChild(
                            option
                        );

                    });

                })
                .catch(() => {

                    contactSelect.innerHTML = `
                        <option value="">
                            Unable to load contacts
                        </option>
                    `;

                });

        }
    );

});
</script>