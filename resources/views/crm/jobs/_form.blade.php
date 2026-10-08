@php

    $isEdit = isset($job);

    $selectedCustomerId = old(
        'customer_id',
        $job->customer_id ?? request('customer_id')
    );

    $selectedContactId = old(
        'contact_id',
        $job->contact_id ?? null
    );

    $selectedPropertyId = old(
        'property_id',
        $job->property_id ?? request('property_id')
    );

    $selectedLocationId = old(
        'property_location_id',
        $job->property_location_id ?? null
    );

    $selectedType = old(
        'job_type',
        $job->job_type ?? 'service'
    );

    $selectedPriority = old(
        'priority',
        $job->priority ?? 'normal'
    );

    $selectedStatus = old(
        'status',
        $job->status ?? 'draft'
    );

    $scheduledStart = old(
        'scheduled_start_at',
        isset($job) && $job->scheduled_start_at
            ? $job->scheduled_start_at->format('Y-m-d\TH:i')
            : ''
    );

    $scheduledEnd = old(
        'scheduled_end_at',
        isset($job) && $job->scheduled_end_at
            ? $job->scheduled_end_at->format('Y-m-d\TH:i')
            : ''
    );

@endphp


<form
    method="POST"
    action="{{ $isEdit
        ? route('crm.jobs.update', $job)
        : route('crm.jobs.store') }}"
>

    @csrf

    @if($isEdit)
        @method('PUT')
    @endif


    {{-- =====================================================
         CUSTOMER / PROPERTY
    ====================================================== --}}

    <div class="card mb-4">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Customer & Work Location
                </div>

                <div class="card-subtitle">
                    Identify who the work is for and
                    where it will be performed.
                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row g-3">

                {{-- Customer --}}

                <div class="col-md-6">

                    <label
                        for="customer_id"
                        class="form-label"
                    >
                        Customer
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="customer_id"
                        name="customer_id"
                        class="form-select @error('customer_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select customer
                        </option>

                        @foreach($customers as $customer)

                            <option
                                value="{{ $customer->id }}"
                                @selected(
                                    $selectedCustomerId == $customer->id
                                )
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


                {{-- Contact --}}

                <div class="col-md-6">

                    <label
                        for="contact_id"
                        class="form-label"
                    >
                        Customer Contact
                    </label>

                    <select
                        id="contact_id"
                        name="contact_id"
                        class="form-select @error('contact_id') is-invalid @enderror"
                    >

                        <option value="">
                            Select contact
                        </option>

                        @foreach($contacts as $contact)

                            <option
                                value="{{ $contact->id }}"
                                @selected(
                                    $selectedContactId == $contact->id
                                )
                            >
                                {{ $contact->display_name }}

                                @if($contact->job_title)
                                    — {{ $contact->job_title }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('contact_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Property --}}

                <div class="col-md-6">

                    <label
                        for="property_id"
                        class="form-label"
                    >
                        Property
                    </label>

                    <select
                        id="property_id"
                        name="property_id"
                        class="form-select @error('property_id') is-invalid @enderror"
                    >

                        <option value="">
                            Select property
                        </option>

                        @foreach($properties as $property)

                            <option
                                value="{{ $property->id }}"
                                @selected(
                                    $selectedPropertyId == $property->id
                                )
                            >
                                {{ $property->name }}
                                — {{ $property->property_code }}
                            </option>

                        @endforeach

                    </select>

                    @error('property_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Location --}}

                <div class="col-md-6">

                    <label
                        for="property_location_id"
                        class="form-label"
                    >
                        Work Location
                    </label>

                    <select
                        id="property_location_id"
                        name="property_location_id"
                        class="form-select @error('property_location_id') is-invalid @enderror"
                    >

                        <option value="">
                            Select work location
                        </option>

                        @foreach($locations as $location)

                            <option
                                value="{{ $location->id }}"
                                @selected(
                                    $selectedLocationId == $location->id
                                )
                            >
                                {{ $location->location_name }}

                                @if($location->is_primary)
                                    — Primary
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('property_location_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         JOB INFORMATION
    ====================================================== --}}

    <div class="card mb-4">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Work Order Information
                </div>

                <div class="card-subtitle">
                    Define the work that needs to be performed.
                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row g-3">

                {{-- Title --}}

                <div class="col-12">

                    <label
                        for="title"
                        class="form-label"
                    >
                        Job Title
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old(
                            'title',
                            $job->title ?? ''
                        ) }}"
                        class="form-control @error('title') is-invalid @enderror"
                        maxlength="255"
                        required
                    >

                    @error('title')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Type --}}

                <div class="col-md-4">

                    <label
                        for="job_type"
                        class="form-label"
                    >
                        Job Type
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="job_type"
                        name="job_type"
                        class="form-select @error('job_type') is-invalid @enderror"
                        required
                    >

                        @foreach([
                            'service' => 'Service',
                            'repair' => 'Repair',
                            'maintenance' => 'Maintenance',
                            'installation' => 'Installation',
                            'inspection' => 'Inspection',
                            'replacement' => 'Replacement',
                            'construction' => 'Construction',
                            'renovation' => 'Renovation',
                            'emergency' => 'Emergency',
                            'other' => 'Other',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    $selectedType === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                    @error('job_type')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Priority --}}

                <div class="col-md-4">

                    <label
                        for="priority"
                        class="form-label"
                    >
                        Priority
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        class="form-select @error('priority') is-invalid @enderror"
                        required
                    >

                        @foreach([
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                            'urgent' => 'Urgent',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    $selectedPriority === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                    @error('priority')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Status --}}

                <div class="col-md-4">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Status
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >

                        @foreach([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'in_progress' => 'In Progress',
                            'on_hold' => 'On Hold',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    $selectedStatus === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                    @error('status')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Schedule --}}

                <div class="col-md-6">

                    <label
                        for="scheduled_start_at"
                        class="form-label"
                    >
                        Scheduled Start
                    </label>

                    <input
                        type="datetime-local"
                        id="scheduled_start_at"
                        name="scheduled_start_at"
                        value="{{ $scheduledStart }}"
                        class="form-control @error('scheduled_start_at') is-invalid @enderror"
                    >

                    @error('scheduled_start_at')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-md-6">

                    <label
                        for="scheduled_end_at"
                        class="form-label"
                    >
                        Scheduled End
                    </label>

                    <input
                        type="datetime-local"
                        id="scheduled_end_at"
                        name="scheduled_end_at"
                        value="{{ $scheduledEnd }}"
                        class="form-control @error('scheduled_end_at') is-invalid @enderror"
                    >

                    @error('scheduled_end_at')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Description --}}

                <div class="col-12">

                    <label
                        for="description"
                        class="form-label"
                    >
                        Job Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old(
                        'description',
                        $job->description ?? ''
                    ) }}</textarea>

                    @error('description')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Scope --}}

                <div class="col-12">

                    <label
                        for="scope_of_work"
                        class="form-label"
                    >
                        Scope of Work
                    </label>

                    <textarea
                        id="scope_of_work"
                        name="scope_of_work"
                        rows="6"
                        class="form-control @error('scope_of_work') is-invalid @enderror"
                    >{{ old(
                        'scope_of_work',
                        $job->scope_of_work ?? ''
                    ) }}</textarea>

                    @error('scope_of_work')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         FINANCIAL INFORMATION
    ====================================================== --}}

    <div class="card mb-4">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Financial Reference
                </div>

                <div class="card-subtitle">
                    Preliminary amounts only. Detailed
                    job costing and billing will be handled
                    by later CRM phases.
                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <label
                        for="estimated_amount"
                        class="form-label"
                    >
                        Estimated Amount
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            $
                        </span>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="estimated_amount"
                            name="estimated_amount"
                            value="{{ old(
                                'estimated_amount',
                                $job->estimated_amount ?? ''
                            ) }}"
                            class="form-control @error('estimated_amount') is-invalid @enderror"
                        >

                    </div>

                    @error('estimated_amount')

                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-md-6">

                    <label
                        for="approved_amount"
                        class="form-label"
                    >
                        Approved Amount
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            $
                        </span>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="approved_amount"
                            name="approved_amount"
                            value="{{ old(
                                'approved_amount',
                                $job->approved_amount ?? ''
                            ) }}"
                            class="form-control @error('approved_amount') is-invalid @enderror"
                        >

                    </div>

                    @error('approved_amount')

                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         NOTES
    ====================================================== --}}

    <div class="card mb-4">

        <div class="card-header">

            <div class="card-title">
                Notes
            </div>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <label
                        for="customer_notes"
                        class="form-label"
                    >
                        Customer Notes
                    </label>

                    <textarea
                        id="customer_notes"
                        name="customer_notes"
                        rows="5"
                        class="form-control @error('customer_notes') is-invalid @enderror"
                    >{{ old(
                        'customer_notes',
                        $job->customer_notes ?? ''
                    ) }}</textarea>

                    @error('customer_notes')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-md-6">

                    <label
                        for="internal_notes"
                        class="form-label"
                    >
                        Internal Notes
                    </label>

                    <textarea
                        id="internal_notes"
                        name="internal_notes"
                        rows="5"
                        class="form-control @error('internal_notes') is-invalid @enderror"
                    >{{ old(
                        'internal_notes',
                        $job->internal_notes ?? ''
                    ) }}</textarea>

                    @error('internal_notes')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ACTIONS
    ====================================================== --}}

    <div class="d-flex justify-content-end gap-2">

        <a
            href="{{ $isEdit
                ? route('crm.jobs.show', $job)
                : route('crm.jobs.index') }}"
            class="btn btn-light"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-check-lg me-1"></i>

            {{ $isEdit
                ? 'Update Work Order'
                : 'Create Work Order' }}

        </button>

    </div>

</form>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const customerSelect =
            document.getElementById(
                'customer_id'
            );

        const contactSelect =
            document.getElementById(
                'contact_id'
            );

        const propertySelect =
            document.getElementById(
                'property_id'
            );

        const locationSelect =
            document.getElementById(
                'property_location_id'
            );


        const customerContextTemplate =
            @json(
                route(
                    'crm.customers.job-context',
                    [
                        'customer' =>
                            '__CUSTOMER__'
                    ]
                )
            );


        const propertyLocationsTemplate =
            @json(
                route(
                    'crm.properties.job-locations',
                    [
                        'property' =>
                            '__PROPERTY__'
                    ]
                )
            );


        function resetSelect(
            select,
            label
        ) {

            select.innerHTML = '';

            const option =
                document.createElement(
                    'option'
                );

            option.value = '';
            option.textContent = label;

            select.appendChild(option);

            select.disabled = false;
        }


        function loadingSelect(
            select,
            label
        ) {

            resetSelect(
                select,
                label
            );

            select.disabled = true;
        }


        function populateContacts(
            contacts,
            selectedId = null
        ) {

            resetSelect(
                contactSelect,
                'Select contact'
            );

            contacts.forEach(
                function (contact) {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        contact.id;

                    option.textContent =
                        contact.name +
                        (
                            contact.job_title
                                ? ' — ' +
                                  contact.job_title
                                : ''
                        );

                    if (
                        selectedId &&
                        String(selectedId) ===
                        String(contact.id)
                    ) {
                        option.selected =
                            true;
                    }

                    contactSelect.appendChild(
                        option
                    );
                }
            );
        }


        function populateProperties(
            properties,
            selectedId = null
        ) {

            resetSelect(
                propertySelect,
                'Select property'
            );

            properties.forEach(
                function (property) {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        property.id;

                    option.textContent =
                        property.name +
                        ' — ' +
                        property.property_code;

                    if (
                        selectedId &&
                        String(selectedId) ===
                        String(property.id)
                    ) {
                        option.selected =
                            true;
                    }

                    propertySelect.appendChild(
                        option
                    );
                }
            );
        }


        function populateLocations(
            locations,
            selectedId = null
        ) {

            resetSelect(
                locationSelect,
                'Select work location'
            );

            locations.forEach(
                function (location) {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        location.id;

                    option.textContent =
                        location.name +
                        (
                            location.address
                                ? ' — ' +
                                  location.address
                                : ''
                        );

                    if (
                        selectedId &&
                        String(selectedId) ===
                        String(location.id)
                    ) {
                        option.selected =
                            true;
                    }

                    locationSelect.appendChild(
                        option
                    );
                }
            );
        }


        async function loadCustomerContext(
            customerId,
            selectedContactId = null,
            selectedPropertyId = null,
            selectedLocationId = null
        ) {

            if (!customerId) {

                resetSelect(
                    contactSelect,
                    'Select contact'
                );

                resetSelect(
                    propertySelect,
                    'Select property'
                );

                resetSelect(
                    locationSelect,
                    'Select work location'
                );

                locationSelect.disabled =
                    true;

                return;
            }


            loadingSelect(
                contactSelect,
                'Loading contacts...'
            );

            loadingSelect(
                propertySelect,
                'Loading properties...'
            );

            loadingSelect(
                locationSelect,
                'Select property first'
            );


            const url =
                customerContextTemplate.replace(
                    '__CUSTOMER__',
                    customerId
                );


            try {

                const response =
                    await fetch(
                        url,
                        {
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error(
                        'Unable to load customer data.'
                    );
                }


                const data =
                    await response.json();


                populateContacts(
                    data.contacts,
                    selectedContactId
                );


                populateProperties(
                    data.properties,
                    selectedPropertyId
                );


                if (selectedPropertyId) {

                    await loadPropertyLocations(
                        selectedPropertyId,
                        selectedLocationId
                    );

                } else {

                    resetSelect(
                        locationSelect,
                        'Select work location'
                    );

                    locationSelect.disabled =
                        true;
                }

            } catch (error) {

                console.error(error);

                resetSelect(
                    contactSelect,
                    'Unable to load contacts'
                );

                resetSelect(
                    propertySelect,
                    'Unable to load properties'
                );

                resetSelect(
                    locationSelect,
                    'Unable to load locations'
                );
            }
        }


        async function loadPropertyLocations(
            propertyId,
            selectedLocationId = null
        ) {

            if (!propertyId) {

                resetSelect(
                    locationSelect,
                    'Select work location'
                );

                locationSelect.disabled =
                    true;

                return;
            }


            loadingSelect(
                locationSelect,
                'Loading locations...'
            );


            const url =
                propertyLocationsTemplate.replace(
                    '__PROPERTY__',
                    propertyId
                );


            try {

                const response =
                    await fetch(
                        url,
                        {
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error(
                        'Unable to load locations.'
                    );
                }


                const locations =
                    await response.json();


                populateLocations(
                    locations,
                    selectedLocationId
                );

            } catch (error) {

                console.error(error);

                resetSelect(
                    locationSelect,
                    'Unable to load locations'
                );
            }
        }


        customerSelect.addEventListener(
            'change',
            function () {

                loadCustomerContext(
                    this.value
                );
            }
        );


        propertySelect.addEventListener(
            'change',
            function () {

                loadPropertyLocations(
                    this.value
                );
            }
        );

    }
);

</script>