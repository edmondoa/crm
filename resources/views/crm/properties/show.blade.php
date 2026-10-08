@extends('layouts.admin')

@section('title', $property->name)

@section('page-title', 'Property Details')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Property Details
            </h1>

            <p class="page-subtitle">
                View property information and customer relationship.
            </p>

        </div>


        <div class="page-header-actions">

            <a
                href="{{ route('crm.properties.index') }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Properties
            </a>

            <a
                href="{{ route('crm.properties.edit', $property) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit Property
            </a>

        </div>

    </div>

</div>


@if(session('success'))

    <div class="alert alert-success crm-alert">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

    </div>

@endif


<div class="row g-4">

    {{-- PROPERTY PROFILE --}}
    <div class="col-lg-4">

        <div class="card h-100">

            <div class="card-body">

                <div class="property-profile">

                    <div class="property-profile-icon">

                        <i class="bi bi-buildings"></i>

                    </div>


                    <h2 class="property-profile-name">
                        {{ $property->name }}
                    </h2>


                    <div class="property-profile-code">
                        {{ $property->property_code }}
                    </div>


                    <div class="property-profile-type">
                        {{ $property->property_type_label }}
                    </div>

                </div>


                <div class="customer-detail-list">

                    <div class="customer-detail-item">

                        <span>
                            Customer
                        </span>

                        <strong>

                            <a
                                href="{{ route('crm.customers.show', $property->customer) }}"
                            >
                                {{ $property->customer->display_name }}
                            </a>

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Status
                        </span>

                        <strong>

                            @if($property->status === 'active')

                                <span class="status-badge status-active">
                                    Active
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactive
                                </span>

                            @endif

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Primary Contact
                        </span>

                        <strong>

                            @if($property->primaryContact)

                                <a
                                    href="{{ route('crm.contacts.show', $property->primaryContact) }}"
                                >
                                    {{ $property->primaryContact->display_name }}
                                </a>

                            @else

                                —

                            @endif

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- PROPERTY INFORMATION --}}
    <div class="col-lg-8">

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Property Information
                </div>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Property Name
                        </div>

                        <div class="detail-value">
                            {{ $property->name }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Property Code
                        </div>

                        <div class="detail-value property-code-value">
                            {{ $property->property_code }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Type
                        </div>

                        <div class="detail-value">
                            {{ $property->property_type_label }}
                        </div>

                    </div>


                    <div class="col-12">

                        <div class="detail-label">
                            Description
                        </div>

                        <div class="detail-value">

                            @if($property->description)

                                {{ $property->description }}

                            @else

                                <span class="text-muted">
                                    No description provided.
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PRIMARY CONTACT --}}
        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Primary Contact
                </div>

            </div>


            <div class="card-body">

                @if($property->primaryContact)

                    <div class="property-contact-card">

                        <div class="property-contact-avatar">

                            {{ strtoupper(
                                substr(
                                    $property->primaryContact->first_name,
                                    0,
                                    1
                                )
                            ) }}

                        </div>


                        <div class="property-contact-content">

                            <a
                                href="{{ route('crm.contacts.show', $property->primaryContact) }}"
                                class="property-contact-name"
                            >
                                {{ $property->primaryContact->display_name }}
                            </a>


                            @if($property->primaryContact->job_title)

                                <div class="property-contact-role">
                                    {{ $property->primaryContact->job_title }}
                                </div>

                            @endif


                            @if($property->primaryContact->email)

                                <div class="property-contact-detail">

                                    <i class="bi bi-envelope"></i>

                                    {{ $property->primaryContact->email }}

                                </div>

                            @endif


                            @if($property->primaryContact->phone)

                                <div class="property-contact-detail">

                                    <i class="bi bi-telephone"></i>

                                    {{ $property->primaryContact->phone }}

                                </div>

                            @endif

                        </div>

                    </div>

                @else

                    <span class="text-muted">
                        No primary contact assigned.
                    </span>

                @endif

            </div>

        </div>


        {{-- =========================================================
            PROPERTY LOCATIONS
        ========================================================= --}}

        <div class="card mt-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Property Locations
                    </div>

                    <div class="card-subtitle">
                        Physical addresses associated with this property.
                    </div>

                </div>

                <a
                    href="{{ route(
                        'crm.property-locations.create',
                        [
                            'property_id' => $property->id
                        ]
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-geo-alt me-1"></i>
                    Add Location
                </a>

            </div>


            <div class="table-responsive">

                <table class="table crm-table align-middle mb-0">

                    <thead>

                        <tr>

                            <th>Location</th>

                            <th>Address</th>

                            <th>Type</th>

                            <th>Status</th>

                            <th class="text-end">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse(
                            $property->locations
                                ->sortByDesc('is_primary')
                            as $location
                        )

                            <tr>

                                <td>

                                    <a
                                        href="{{ route(
                                            'crm.property-locations.show',
                                            $location
                                        ) }}"
                                        class="customer-name-link"
                                    >
                                        {{ $location->location_name }}
                                    </a>

                                    <div class="customer-code">
                                        {{ $location->location_code }}
                                    </div>

                                    @if($location->is_primary)

                                        <span class="location-primary-badge">
                                            <i class="bi bi-star-fill me-1"></i>
                                            Primary
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="location-address">

                                        <div>
                                            {{ $location->address_line_1 }}
                                        </div>

                                        @if($location->address_line_2)

                                            <div>
                                                {{ $location->address_line_2 }}
                                            </div>

                                        @endif

                                        <div>
                                            {{ $location->city }},
                                            {{ $location->state }}
                                            {{ $location->zip_code }}
                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="property-type-badge">
                                        {{ $location->location_type_label }}
                                    </span>

                                </td>


                                <td>

                                    @if($location->status === 'active')

                                        <span class="status-badge status-active">
                                            Active
                                        </span>

                                    @else

                                        <span class="status-badge status-inactive">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td class="text-end">

                                    <div class="btn-group">

                                        <a
                                            href="{{ route(
                                                'crm.property-locations.show',
                                                $location
                                            ) }}"
                                            class="btn btn-sm btn-light"
                                            title="View"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a
                                            href="{{ route(
                                                'crm.property-locations.edit',
                                                $location
                                            ) }}"
                                            class="btn btn-sm btn-light"
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-4"
                                >

                                    <span class="text-muted">
                                        No locations have been added for this property.
                                    </span>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- =========================================================
            PROPERTY ACTIVITIES
        ========================================================= --}}

        <div class="card mt-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Activities
                    </div>

                    <div class="card-subtitle">
                        CRM activity history for this property.
                    </div>

                </div>

                <a
                    href="{{ route(
                        'crm.activities.create',
                        [
                            'customer_id' => $property->customer_id,
                            'property_id' => $property->id
                        ]
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Activity
                </a>

            </div>


            <div class="card-body">

                @forelse(
                    $property->activities
                        ->sortByDesc('scheduled_at')
                        ->take(10)
                    as $activity
                )

                    <div class="activity-timeline-item">

                        <div class="activity-timeline-icon activity-type-{{ $activity->activity_type }}">

                            <i class="bi bi-{{ match($activity->activity_type) {
                                'call' => 'telephone',
                                'email' => 'envelope',
                                'meeting' => 'people',
                                'site_visit' => 'geo-alt',
                                'note' => 'journal-text',
                                'sms' => 'chat',
                                default => 'activity',
                            } }}"></i>

                        </div>


                        <div class="activity-timeline-content">

                            <div class="activity-timeline-header">

                                <a
                                    href="{{ route(
                                        'crm.activities.show',
                                        $activity
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $activity->subject }}
                                </a>

                                @if($activity->status === 'completed')

                                    <span class="status-badge status-active">
                                        Completed
                                    </span>

                                @elseif($activity->status === 'cancelled')

                                    <span class="status-badge status-inactive">
                                        Cancelled
                                    </span>

                                @else

                                    <span class="status-badge status-planned">
                                        Planned
                                    </span>

                                @endif

                            </div>


                            <div class="activity-timeline-meta">

                                {{ $activity->activity_type_label }}

                                @if($activity->scheduled_at)
                                    ·
                                    {{ $activity->scheduled_at->format('M d, Y g:i A') }}
                                @endif

                            </div>


                            @if($activity->propertyLocation)

                                <div class="activity-location">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $activity->propertyLocation->location_name }}
                                </div>

                            @endif

                        </div>

                    </div>

                @empty

                    <div class="text-center py-4">

                        <div class="text-muted">
                            No activities recorded for this property.
                        </div>

                        <a
                            href="{{ route(
                                'crm.activities.create',
                                [
                                    'customer_id' => $property->customer_id,
                                    'property_id' => $property->id
                                ]
                            ) }}"
                            class="btn btn-sm btn-primary mt-3"
                        >
                            <i class="bi bi-plus-lg me-1"></i>
                            Add First Activity
                        </a>

                    </div>

                @endforelse

            </div>

        </div>
        <div class="card mt-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Tasks
                    </div>

                    <div class="card-subtitle">
                        Tasks associated with this property.
                    </div>

                </div>

                <a
                    href="{{ route(
                        'crm.tasks.create',
                        [
                            'customer_id' => $property->customer_id,
                            'property_id' => $property->id,
                        ]
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Task
                </a>

            </div>


            <div class="table-responsive">

                <table class="table crm-table align-middle mb-0">

                    <thead>

                        <tr>
                            <th>Task</th>
                            <th>Contact</th>
                            <th>Due</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($property->tasks as $task)

                            <tr>

                                <td>

                                    <a
                                        href="{{ route(
                                            'crm.tasks.show',
                                            $task
                                        ) }}"
                                        class="customer-name-link"
                                    >
                                        {{ $task->subject }}
                                    </a>

                                    <div class="customer-code">
                                        {{ $task->task_code }}
                                    </div>

                                </td>

                                <td>

                                    @if($task->contact)

                                        {{ $task->contact->display_name }}

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($task->due_at)

                                        {{ $task->due_at->format(
                                            'M d, Y h:i A'
                                        ) }}

                                    @else

                                        <span class="text-muted">
                                            No due date
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $task->priority_label }}
                                </td>

                                <td>

                                    <span
                                        class="status-badge status-{{ str_replace(
                                            '_',
                                            '-',
                                            $task->status
                                        ) }}"
                                    >
                                        {{ $task->status_label }}
                                    </span>

                                </td>

                                <td class="text-end">

                                    <a
                                        href="{{ route(
                                            'crm.tasks.show',
                                            $task
                                        ) }}"
                                        class="btn btn-sm btn-light"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4"
                                >
                                    <span class="text-muted">
                                        No tasks have been created
                                        for this property.
                                    </span>
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card mt-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Jobs / Work Orders
                    </div>

                    <div class="card-subtitle">
                        Work orders associated with this property.
                    </div>

                </div>

                <a
                    href="{{ route(
                        'crm.jobs.create',
                        [
                            'customer_id' =>
                                $property->customer_id,

                            'property_id' =>
                                $property->id,
                        ]
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Create Work Order
                </a>

            </div>


            <div class="table-responsive">

                <table class="table crm-table align-middle mb-0">

                    <thead>

                        <tr>
                            <th>Work Order</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($property->jobs as $job)

                            <tr>

                                <td>

                                    <a
                                        href="{{ route(
                                            'crm.jobs.show',
                                            $job
                                        ) }}"
                                        class="customer-name-link"
                                    >
                                        {{ $job->title }}
                                    </a>

                                    <div class="customer-code">
                                        {{ $job->work_order_number }}
                                    </div>

                                </td>


                                <td>

                                    @if($job->contact)

                                        {{ $job->contact->display_name }}

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($job->propertyLocation)

                                        {{ $job->propertyLocation->location_name }}

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($job->scheduled_start_at)

                                        {{
                                            $job->scheduled_start_at
                                                ->format(
                                                    'M d, Y h:i A'
                                                )
                                        }}

                                    @else

                                        <span class="text-muted">
                                            Not scheduled
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <span
                                        class="status-badge status-{{ str_replace(
                                            '_',
                                            '-',
                                            $job->status
                                        ) }}"
                                    >
                                        {{ $job->status_label }}
                                    </span>

                                </td>


                                <td class="text-end">

                                    <a
                                        href="{{ route(
                                            'crm.jobs.show',
                                            $job
                                        ) }}"
                                        class="btn btn-sm btn-light"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4"
                                >

                                    <span class="text-muted">
                                        No work orders have been
                                        created for this property.
                                    </span>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- NOTES --}}
        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Notes
                </div>

            </div>


            <div class="card-body">

                @if($property->notes)

                    <div class="customer-notes">
                        {{ $property->notes }}
                    </div>

                @else

                    <span class="text-muted">
                        No notes available.
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- DELETE --}}
<div class="card mt-4">

    <div class="card-body">

        <div class="danger-zone">

            <div>

                <div class="danger-zone-title">
                    Delete Property
                </div>

                <div class="danger-zone-description">
                    Permanently remove this property from the CRM.
                </div>

            </div>


            <form
                method="POST"
                action="{{ route('crm.properties.destroy', $property) }}"
                onsubmit="return confirm('Are you sure you want to delete this property?');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Delete Property
                </button>

            </form>

        </div>

    </div>

</div>

@endsection