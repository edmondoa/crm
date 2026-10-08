@php
    $isEdit = isset($task);

    $selectedCustomerId = old(
        'customer_id',
        $task->customer_id ?? request('customer_id')
    );

    $selectedContactId = old(
        'contact_id',
        $task->contact_id ?? null
    );

    $selectedPropertyId = old(
        'property_id',
        $task->property_id ?? request('property_id')
    );

    $selectedLocationId = old(
        'property_location_id',
        $task->property_location_id ?? null
    );

    $selectedType = old(
        'task_type',
        $task->task_type ?? 'follow_up'
    );

    $selectedPriority = old(
        'priority',
        $task->priority ?? 'normal'
    );

    $selectedStatus = old(
        'status',
        $task->status ?? 'pending'
    );

    $dueAt = old(
        'due_at',
        isset($task) && $task->due_at
            ? $task->due_at->format('Y-m-d\TH:i')
            : ''
    );
@endphp

<form
    method="POST"
    action="{{ $isEdit
        ? route('crm.tasks.update', $task)
        : route('crm.tasks.store') }}"
>
    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    {{-- =====================================================
         RELATIONSHIPS
    ====================================================== --}}

    <div class="card mb-4">
        <div class="card-header">
            <div>
                <div class="card-title">
                    Task Assignment
                </div>

                <div class="card-subtitle">
                    Associate this task with the appropriate
                    CRM records.
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
                        Contact
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

                {{-- Property Location --}}
                <div class="col-md-6">

                    <label
                        for="property_location_id"
                        class="form-label"
                    >
                        Property Location
                    </label>

                    <select
                        id="property_location_id"
                        name="property_location_id"
                        class="form-select @error('property_location_id') is-invalid @enderror"
                    >
                        <option value="">
                            Select property location
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
         TASK INFORMATION
    ====================================================== --}}

    <div class="card mb-4">

        <div class="card-header">
            <div>
                <div class="card-title">
                    Task Information
                </div>

                <div class="card-subtitle">
                    Define what needs to be completed.
                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Type --}}
                <div class="col-md-4">

                    <label
                        for="task_type"
                        class="form-label"
                    >
                        Task Type
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="task_type"
                        name="task_type"
                        class="form-select @error('task_type') is-invalid @enderror"
                        required
                    >
                        @foreach([
                            'follow_up' => 'Follow Up',
                            'call' => 'Call',
                            'email' => 'Email',
                            'meeting' => 'Meeting',
                            'site_visit' => 'Site Visit',
                            'estimate' => 'Estimate',
                            'proposal' => 'Proposal',
                            'document' => 'Document',
                            'other' => 'Other',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected($selectedType === $value)
                            >
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>

                    @error('task_type')
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
                        <option
                            value="low"
                            @selected($selectedPriority === 'low')
                        >
                            Low
                        </option>

                        <option
                            value="normal"
                            @selected($selectedPriority === 'normal')
                        >
                            Normal
                        </option>

                        <option
                            value="high"
                            @selected($selectedPriority === 'high')
                        >
                            High
                        </option>

                        <option
                            value="urgent"
                            @selected($selectedPriority === 'urgent')
                        >
                            Urgent
                        </option>
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
                        <option
                            value="pending"
                            @selected($selectedStatus === 'pending')
                        >
                            Pending
                        </option>

                        <option
                            value="in_progress"
                            @selected($selectedStatus === 'in_progress')
                        >
                            In Progress
                        </option>

                        <option
                            value="completed"
                            @selected($selectedStatus === 'completed')
                        >
                            Completed
                        </option>

                        <option
                            value="cancelled"
                            @selected($selectedStatus === 'cancelled')
                        >
                            Cancelled
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Subject --}}
                <div class="col-12">

                    <label
                        for="subject"
                        class="form-label"
                    >
                        Subject
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        value="{{ old('subject', $task->subject ?? '') }}"
                        class="form-control @error('subject') is-invalid @enderror"
                        maxlength="255"
                        required
                    >

                    @error('subject')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Due date --}}
                <div class="col-md-6">

                    <label
                        for="due_at"
                        class="form-label"
                    >
                        Due Date & Time
                    </label>

                    <input
                        type="datetime-local"
                        id="due_at"
                        name="due_at"
                        value="{{ $dueAt }}"
                        class="form-control @error('due_at') is-invalid @enderror"
                    >

                    @error('due_at')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Outcome --}}
                <div class="col-md-6">

                    <label
                        for="outcome"
                        class="form-label"
                    >
                        Outcome
                    </label>

                    <input
                        type="text"
                        id="outcome"
                        name="outcome"
                        value="{{ old('outcome', $task->outcome ?? '') }}"
                        class="form-control @error('outcome') is-invalid @enderror"
                        maxlength="255"
                    >

                    @error('outcome')
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
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description', $task->description ?? '') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Notes --}}
                <div class="col-12">

                    <label
                        for="notes"
                        class="form-label"
                    >
                        Internal Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="form-control @error('notes') is-invalid @enderror"
                    >{{ old('notes', $task->notes ?? '') }}</textarea>

                    @error('notes')
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
                ? route('crm.tasks.show', $task)
                : route('crm.tasks.index') }}"
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
                ? 'Update Task'
                : 'Create Task' }}
        </button>

    </div>

</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect =
        document.getElementById('customer_id');

    const contactSelect =
        document.getElementById('contact_id');

    const propertySelect =
        document.getElementById('property_id');

    const locationSelect =
        document.getElementById('property_location_id');

    const customerContextTemplate =
        @json(
            route(
                'crm.customers.task-context',
                ['customer' => '__CUSTOMER__']
            )
        );

    const propertyLocationsTemplate =
        @json(
            route(
                'crm.properties.task-locations',
                ['property' => '__PROPERTY__']
            )
        );

    function setLoading(select, message)
    {
        select.innerHTML = '';

        const option =
            document.createElement('option');

        option.value = '';
        option.textContent = message;

        select.appendChild(option);
        select.disabled = true;
    }

    function resetSelect(select, label)
    {
        select.innerHTML = '';

        const option =
            document.createElement('option');

        option.value = '';
        option.textContent = label;

        select.appendChild(option);
        select.disabled = false;
    }

    function populateContacts(
        contacts,
        selectedId = null
    ) {
        resetSelect(
            contactSelect,
            'Select contact'
        );

        contacts.forEach(function (contact) {

            const option =
                document.createElement('option');

            option.value = contact.id;

            option.textContent =
                contact.name +
                (
                    contact.job_title
                        ? ' — ' + contact.job_title
                        : ''
                );

            if (
                selectedId &&
                String(selectedId) ===
                String(contact.id)
            ) {
                option.selected = true;
            }

            contactSelect.appendChild(option);
        });
    }

    function populateProperties(
        properties,
        selectedId = null
    ) {
        resetSelect(
            propertySelect,
            'Select property'
        );

        properties.forEach(function (property) {

            const option =
                document.createElement('option');

            option.value = property.id;

            option.textContent =
                property.name +
                ' — ' +
                property.property_code;

            if (
                selectedId &&
                String(selectedId) ===
                String(property.id)
            ) {
                option.selected = true;
            }

            propertySelect.appendChild(option);
        });
    }

    function populateLocations(
        locations,
        selectedId = null
    ) {
        resetSelect(
            locationSelect,
            'Select property location'
        );

        locations.forEach(function (location) {

            const option =
                document.createElement('option');

            option.value = location.id;

            option.textContent =
                location.name +
                (
                    location.address
                        ? ' — ' + location.address
                        : ''
                );

            if (
                selectedId &&
                String(selectedId) ===
                String(location.id)
            ) {
                option.selected = true;
            }

            locationSelect.appendChild(option);
        });
    }

    async function loadCustomerContext(
        customerId,
        selectedContactId = null,
        selectedPropertyId = null
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
                'Select property location'
            );

            return;
        }

        setLoading(
            contactSelect,
            'Loading contacts...'
        );

        setLoading(
            propertySelect,
            'Loading properties...'
        );

        setLoading(
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
                await fetch(url, {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                });

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
                    {{ $selectedLocationId ?: 'null' }}
                );

            } else {

                resetSelect(
                    locationSelect,
                    'Select property location'
                );

                locationSelect.disabled = true;
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
                'Select property location'
            );

            locationSelect.disabled = true;

            return;
        }

        setLoading(
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
                await fetch(url, {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                });

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

});
</script>