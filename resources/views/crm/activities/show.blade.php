@extends('layouts.admin')

@section('title', $activity->subject)

@section('page-title', $activity->subject)

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                {{ $activity->subject }}
            </h1>

            <p class="page-subtitle">
                {{ $activity->activity_type_label }}
            </p>

        </div>

        <div class="page-header-actions">

            @if($activity->status === 'planned')

                <form
                    method="POST"
                    action="{{ route(
                        'crm.activities.complete',
                        $activity
                    ) }}"
                    class="d-inline"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Mark Complete
                    </button>

                </form>

            @endif

            <a
                href="{{ route(
                    'crm.activities.edit',
                    $activity
                ) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route(
                    'crm.activities.index'
                ) }}"
                class="btn btn-light"
            >
                Back
            </a>

        </div>

    </div>

</div>


<div class="row g-4">

    {{-- =====================================================
         MAIN INFORMATION
    ====================================================== --}}

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Activity Details
                    </div>

                    <div class="card-subtitle">
                        CRM interaction information.
                    </div>

                </div>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">

                        <div class="detail-label">
                            Activity Type
                        </div>

                        <div class="detail-value">
                            {{ $activity->activity_type_label }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="detail-label">
                            Status
                        </div>

                        <div class="detail-value">

                            @if($activity->status === 'planned')

                                <span class="status-badge status-planned">
                                    Planned
                                </span>

                            @elseif($activity->status === 'completed')

                                <span class="status-badge status-active">
                                    Completed
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Cancelled
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="detail-label">
                            Priority
                        </div>

                        <div class="detail-value">

                            <span class="activity-priority activity-priority-{{ $activity->priority }}">
                                {{ $activity->priority_label }}
                            </span>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Scheduled
                        </div>

                        <div class="detail-value">

                            @if($activity->scheduled_at)

                                {{ $activity->scheduled_at->format('M d, Y g:i A') }}

                            @else

                                <span class="text-muted">
                                    Not scheduled
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Completed
                        </div>

                        <div class="detail-value">

                            @if($activity->completed_at)

                                {{ $activity->completed_at->format('M d, Y g:i A') }}

                            @else

                                <span class="text-muted">
                                    Not completed
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
             DESCRIPTION
        ================================================== --}}

        @if($activity->description)

            <div class="card mt-4">

                <div class="card-header">

                    <div class="card-title">
                        Description
                    </div>

                </div>

                <div class="card-body">

                    <div class="activity-description">
                        {!! nl2br(e($activity->description)) !!}
                    </div>

                </div>

            </div>

        @endif


        {{-- =================================================
             NOTES
        ================================================== --}}

        @if($activity->notes)

            <div class="card mt-4">

                <div class="card-header">

                    <div class="card-title">
                        Notes
                    </div>

                </div>

                <div class="card-body">

                    <div class="activity-description">
                        {!! nl2br(e($activity->notes)) !!}
                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- =====================================================
         RELATED RECORDS
    ====================================================== --}}

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Related Records
                </div>

            </div>

            <div class="card-body">

                @if($activity->customer)

                    <div class="activity-related-item">

                        <div class="detail-label">
                            Customer
                        </div>

                        <a
                            href="{{ route(
                                'crm.customers.show',
                                $activity->customer
                            ) }}"
                            class="customer-name-link"
                        >
                            {{ $activity->customer->display_name }}
                        </a>

                        <div class="customer-code">
                            {{ $activity->customer->customer_code }}
                        </div>

                    </div>

                @endif


                @if($activity->contact)

                    <div class="activity-related-item">

                        <div class="detail-label">
                            Contact
                        </div>

                        <a
                            href="{{ route(
                                'crm.contacts.show',
                                $activity->contact
                            ) }}"
                            class="customer-name-link"
                        >
                            {{ $activity->contact->display_name }}
                        </a>

                        @if($activity->contact->job_title)

                            <div class="activity-related-meta">
                                {{ $activity->contact->job_title }}
                            </div>

                        @endif

                    </div>

                @endif


                @if($activity->property)

                    <div class="activity-related-item">

                        <div class="detail-label">
                            Property
                        </div>

                        <a
                            href="{{ route(
                                'crm.properties.show',
                                $activity->property
                            ) }}"
                            class="customer-name-link"
                        >
                            {{ $activity->property->name }}
                        </a>

                        <div class="customer-code">
                            {{ $activity->property->property_code }}
                        </div>

                    </div>

                @endif


                @if($activity->propertyLocation)

                    <div class="activity-related-item">

                        <div class="detail-label">
                            Location
                        </div>

                        <a
                            href="{{ route(
                                'crm.property-locations.show',
                                $activity->propertyLocation
                            ) }}"
                            class="customer-name-link"
                        >
                            {{ $activity->propertyLocation->location_name }}
                        </a>

                        <div class="activity-related-meta">

                            {{ $activity->propertyLocation->city }},
                            {{ $activity->propertyLocation->state }}
                            {{ $activity->propertyLocation->zip_code }}

                        </div>

                    </div>

                @endif


                @if(
                    !$activity->customer &&
                    !$activity->contact &&
                    !$activity->property &&
                    !$activity->propertyLocation
                )

                    <div class="text-muted small">
                        No related CRM records.
                    </div>

                @endif

            </div>

        </div>


        {{-- =================================================
             DELETE
        ================================================== --}}

        <div class="card mt-4 border-danger">

            <div class="card-body">

                <strong class="text-danger">
                    Delete Activity
                </strong>

                <p class="text-muted small mt-1">
                    This permanently removes this activity from the CRM history.
                </p>

                <form
                    method="POST"
                    action="{{ route(
                        'crm.activities.destroy',
                        $activity
                    ) }}"
                    onsubmit="return confirm(
                        'Are you sure you want to delete this activity?'
                    );"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-outline-danger"
                    >
                        <i class="bi bi-trash me-1"></i>
                        Delete Activity
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection