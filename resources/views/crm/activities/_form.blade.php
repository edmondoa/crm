{{-- =========================================================
     CRM RELATIONSHIPS
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Related Records
    </div>

    <div class="form-section-description">
        Associate this activity with the appropriate customer, contact, property, and location.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label class="form-label">
                Customer
            </label>

            <select
                name="customer_id"
                id="customer_id"
                class="form-select @error('customer_id') is-invalid @enderror"
            >

                <option value="">
                    Select customer
                </option>

                @foreach($customers as $customer)

                    <option
                        value="{{ $customer->id }}"
                        @selected(
                            old(
                                'customer_id',
                                $selectedCustomerId
                                ?? $activity->customer_id
                                ?? ''
                            ) == $customer->id
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


        <div class="col-md-6">

            <label class="form-label">
                Contact
            </label>

            <select
                name="contact_id"
                id="contact_id"
                class="form-select @error('contact_id') is-invalid @enderror"
            >

                <option value="">
                    Select contact
                </option>

                @foreach($contacts as $contact)

                    <option
                        value="{{ $contact->id }}"
                        @selected(
                            old(
                                'contact_id',
                                $selectedContactId
                                ?? $activity->contact_id
                                ?? ''
                            ) == $contact->id
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


        <div class="col-md-6">

            <label class="form-label">
                Property
            </label>

            <select
                name="property_id"
                id="property_id"
                class="form-select @error('property_id') is-invalid @enderror"
            >

                <option value="">
                    Select property
                </option>

                @foreach($properties as $property)

                    <option
                        value="{{ $property->id }}"
                        data-customer-id="{{ $property->customer_id }}"
                        @selected(
                            old(
                                'property_id',
                                $selectedPropertyId
                                ?? $activity->property_id
                                ?? ''
                            ) == $property->id
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


        <div class="col-md-6">

            <label class="form-label">
                Property Location
            </label>

            <select
                name="property_location_id"
                id="property_location_id"
                class="form-select @error('property_location_id') is-invalid @enderror"
            >

                <option value="">
                    Select location
                </option>

                @foreach($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        @selected(
                            old(
                                'property_location_id',
                                $selectedLocationId
                                ?? $activity->property_location_id
                                ?? ''
                            ) == $location->id
                        )
                    >
                        {{ $location->location_name }}
                        — {{ $location->city }},
                        {{ $location->state }}
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


{{-- =========================================================
     ACTIVITY INFORMATION
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Activity Information
    </div>

    <div class="form-section-description">
        Record the type and purpose of this CRM activity.
    </div>

    <div class="row g-3">

        <div class="col-md-4">

            <label class="form-label">
                Activity Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="activity_type"
                class="form-select @error('activity_type') is-invalid @enderror"
                required
            >

                <option value="">
                    Select type
                </option>

                @php
                    $activityTypes = [
                        'call' => 'Call',
                        'email' => 'Email',
                        'meeting' => 'Meeting',
                        'site_visit' => 'Site Visit',
                        'note' => 'Note',
                        'sms' => 'SMS',
                        'other' => 'Other',
                    ];
                @endphp

                @foreach($activityTypes as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'activity_type',
                                $activity->activity_type ?? 'note'
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

            @error('activity_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-8">

            <label class="form-label">
                Subject
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="subject"
                class="form-control @error('subject') is-invalid @enderror"
                value="{{ old('subject', $activity->subject ?? '') }}"
                placeholder="e.g. Discuss warehouse inspection"
                required
            >

            @error('subject')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-12">

            <label class="form-label">
                Description
            </label>

            <textarea
                name="description"
                rows="5"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Describe the activity..."
            >{{ old('description', $activity->description ?? '') }}</textarea>

            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     SCHEDULING
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Scheduling
    </div>

    <div class="form-section-description">
        Record when the activity is scheduled or was completed.
    </div>

    <div class="row g-3">

        <div class="col-md-4">

            <label class="form-label">
                Scheduled Date & Time
            </label>

            <input
                type="datetime-local"
                name="scheduled_at"
                class="form-control @error('scheduled_at') is-invalid @enderror"
                value="{{ old(
                    'scheduled_at',
                    isset($activity) && $activity->scheduled_at
                        ? $activity->scheduled_at->format('Y-m-d\TH:i')
                        : ''
                ) }}"
            >

            @error('scheduled_at')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Completed Date & Time
            </label>

            <input
                type="datetime-local"
                name="completed_at"
                class="form-control @error('completed_at') is-invalid @enderror"
                value="{{ old(
                    'completed_at',
                    isset($activity) && $activity->completed_at
                        ? $activity->completed_at->format('Y-m-d\TH:i')
                        : ''
                ) }}"
            >

            @error('completed_at')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Priority
                <span class="text-danger">*</span>
            </label>

            <select
                name="priority"
                class="form-select @error('priority') is-invalid @enderror"
                required
            >

                <option
                    value="low"
                    @selected(
                        old(
                            'priority',
                            $activity->priority ?? 'normal'
                        ) === 'low'
                    )
                >
                    Low
                </option>

                <option
                    value="normal"
                    @selected(
                        old(
                            'priority',
                            $activity->priority ?? 'normal'
                        ) === 'normal'
                    )
                >
                    Normal
                </option>

                <option
                    value="high"
                    @selected(
                        old(
                            'priority',
                            $activity->priority ?? 'normal'
                        ) === 'high'
                    )
                >
                    High
                </option>

            </select>

            @error('priority')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                class="form-select @error('status') is-invalid @enderror"
                required
            >

                <option
                    value="planned"
                    @selected(
                        old(
                            'status',
                            $activity->status ?? 'planned'
                        ) === 'planned'
                    )
                >
                    Planned
                </option>

                <option
                    value="completed"
                    @selected(
                        old(
                            'status',
                            $activity->status ?? ''
                        ) === 'completed'
                    )
                >
                    Completed
                </option>

                <option
                    value="cancelled"
                    @selected(
                        old(
                            'status',
                            $activity->status ?? ''
                        ) === 'cancelled'
                    )
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

    </div>

</div>


{{-- =========================================================
     NOTES
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Notes
    </div>

    <div class="form-section-description">
        Add internal notes or additional details.
    </div>

    <textarea
        name="notes"
        rows="4"
        class="form-control @error('notes') is-invalid @enderror"
        placeholder="Additional notes..."
    >{{ old('notes', $activity->notes ?? '') }}</textarea>

    @error('notes')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>


{{-- =========================================================
     ACTIONS
========================================================= --}}

<div class="form-actions">

    <a
        href="{{ isset($activity)
            ? route('crm.activities.show', $activity)
            : route('crm.activities.index')
        }}"
        class="btn btn-light"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        <i class="bi bi-check-lg me-1"></i>

        {{ isset($activity)
            ? 'Update Activity'
            : 'Create Activity'
        }}
    </button>

</div>