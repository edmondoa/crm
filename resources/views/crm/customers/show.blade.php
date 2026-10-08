@extends('layouts.admin')

@section('title', $customer->display_name)

@section('page-title', 'Customer Details')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="d-flex align-items-center gap-2 mb-1">

                <h1 class="page-title mb-0">
                    {{ $customer->display_name }}
                </h1>

                @if($customer->status === 'active')

                    <span class="badge bg-success-subtle text-success">
                        Active
                    </span>

                @else

                    <span class="badge bg-secondary-subtle text-secondary">
                        Inactive
                    </span>

                @endif

            </div>

            <p class="page-subtitle">
                {{ $customer->customer_code }}
            </p>

        </div>


        <div class="page-header-actions">

            <a
                href="{{ route('crm.customers.edit', $customer) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit Customer
            </a>

            <a
                href="{{ route('crm.customers.index') }}"
                class="btn btn-light"
            >
                Back
            </a>

        </div>

    </div>

</div>


@if(session('success'))

    <div class="alert alert-success">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

    </div>

@endif


{{-- =========================================================
    CUSTOMER INFORMATION
========================================================= --}}

<div class="row g-4">

    {{-- =====================================================
        CUSTOMER SUMMARY
    ====================================================== --}}

    <div class="col-lg-4">

        <div class="card h-100">

            <div class="card-header">

                <div class="card-title">
                    Customer Summary
                </div>

            </div>


            <div class="card-body">

                <div class="customer-profile">

                    <div class="customer-profile-avatar">

                        {{ strtoupper(
                            substr(
                                $customer->display_name,
                                0,
                                1
                            )
                        ) }}

                    </div>


                    <h3 class="customer-profile-name">
                        {{ $customer->display_name }}
                    </h3>


                    <div class="customer-profile-code">
                        {{ $customer->customer_code }}
                    </div>

                </div>


                <div class="customer-detail-list">

                    <div class="customer-detail-item">

                        <span>
                            Type
                        </span>

                        <strong>
                            {{ ucfirst($customer->customer_type) }}
                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Status
                        </span>

                        <strong>

                            @if($customer->status === 'active')

                                Active

                            @else

                                Inactive

                            @endif

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Contacts
                        </span>

                        <strong>
                            {{ $customer->contacts_count ?? $customer->contacts->count() }}
                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Properties
                        </span>

                        <strong>
                            {{ $customer->properties_count ?? $customer->properties->count() }}
                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Customer Since
                        </span>

                        <strong>
                            {{ $customer->created_at?->format('M d, Y') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        CUSTOMER INFORMATION
    ====================================================== --}}

    <div class="col-lg-8">

        {{-- Contact Information --}}

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Contact Information
                </div>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Email
                        </div>

                        <div class="detail-value">

                            @if($customer->email)

                                <a href="mailto:{{ $customer->email }}">
                                    {{ $customer->email }}
                                </a>

                            @else

                                <span class="text-muted">
                                    Not provided
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Phone
                        </div>

                        <div class="detail-value">
                            {{ $customer->phone ?: 'Not provided' }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Mobile
                        </div>

                        <div class="detail-value">
                            {{ $customer->mobile ?: 'Not provided' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Address --}}

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Address
                </div>

            </div>


            <div class="card-body">

                @if($customer->full_address)

                    <div class="customer-address">

                        <i class="bi bi-geo-alt me-2"></i>

                        {{ $customer->full_address }}

                    </div>

                @else

                    <span class="text-muted">
                        No address information provided.
                    </span>

                @endif

            </div>

        </div>


        {{-- Tax --}}

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Tax Information
                </div>

            </div>


            <div class="card-body">

                <div class="detail-label">
                    TIN / Tax ID
                </div>

                <div class="detail-value">
                    {{ $customer->tax_id ?: 'Not provided' }}
                </div>

            </div>

        </div>


        {{-- Notes --}}

        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Notes
                </div>

            </div>


            <div class="card-body">

                @if($customer->notes)

                    <div class="customer-notes">
                        {!! nl2br(e($customer->notes)) !!}
                    </div>

                @else

                    <span class="text-muted">
                        No notes recorded.
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    CUSTOMER CRM TABS
========================================================= --}}

<div class="card mt-4">

    <div class="card-body pb-0">

        <ul
            class="nav nav-tabs crm-tabs"
            id="customerTabs"
            role="tablist"
        >

            {{-- Contacts --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link active"
                    id="contacts-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#contacts-pane"
                    type="button"
                    role="tab"
                    aria-controls="contacts-pane"
                    aria-selected="true"
                >

                    <i class="bi bi-people me-1"></i>

                    Contacts

                    <span class="badge bg-secondary ms-1">
                        {{ $customer->contacts->count() }}
                    </span>

                </button>

            </li>


            {{-- Properties --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="properties-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#properties-pane"
                    type="button"
                    role="tab"
                    aria-controls="properties-pane"
                    aria-selected="false"
                >

                    <i class="bi bi-building me-1"></i>

                    Properties

                    <span class="badge bg-secondary ms-1">
                        {{ $customer->properties->count() }}
                    </span>

                </button>

            </li>


            {{-- Activities --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="activities-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#activities-pane"
                    type="button"
                    role="tab"
                    aria-controls="activities-pane"
                    aria-selected="false"
                >

                    <i class="bi bi-activity me-1"></i>

                    Activities

                    <span class="badge bg-secondary ms-1">
                        {{ $customer->activities->count() }}
                    </span>

                </button>

            </li>


            {{-- Jobs --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="jobs-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#jobs-pane"
                    type="button"
                    role="tab"
                    aria-controls="jobs-pane"
                    aria-selected="false"
                >

                    <i class="bi bi-briefcase me-1"></i>

                    Jobs

                    <span class="badge bg-secondary ms-1">
                        {{ $customer->jobs->count() }}
                    </span>

                </button>

            </li>


            {{-- Work Orders --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="work-orders-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#work-orders-pane"
                    type="button"
                    role="tab"
                    aria-controls="work-orders-pane"
                    aria-selected="false"
                >

                    <i class="bi bi-clipboard-check me-1"></i>

                    Work Orders

                </button>

            </li>


            {{-- Schedules --}}

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="schedules-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#schedules-pane"
                    type="button"
                    role="tab"
                    aria-controls="schedules-pane"
                    aria-selected="false"
                >

                    <i class="bi bi-calendar3 me-1"></i>

                    Schedules

                </button>

            </li>

        </ul>

    </div>


    <div class="card-body pt-4">

        <div
            class="tab-content"
            id="customerTabsContent"
        >

            {{-- =====================================================
                CONTACTS TAB
            ====================================================== --}}

            <div
                class="tab-pane fade show active"
                id="contacts-pane"
                role="tabpanel"
                aria-labelledby="contacts-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Contacts
                        </h5>

                        <div class="text-muted small">
                            Contacts associated with this customer.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.contacts.create', [
                            'customer_id' => $customer->id
                        ]) }}"
                        class="btn btn-sm btn-primary"
                    >

                        <i class="bi bi-person-plus me-1"></i>

                        Add Contact

                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table crm-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Contact
                                </th>

                                <th>
                                    Role
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Phone
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($customer->contacts as $contact)

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route('crm.contacts.show', $contact) }}"
                                            class="customer-name-link"
                                        >
                                            {{ $contact->display_name }}
                                        </a>


                                        @if($contact->is_primary)

                                            <span class="contact-primary-badge ms-1">

                                                <i class="bi bi-star-fill"></i>

                                                Primary

                                            </span>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $contact->job_title ?: '—' }}
                                    </td>


                                    <td>

                                        @if($contact->email)

                                            <a href="mailto:{{ $contact->email }}">
                                                {{ $contact->email }}
                                            </a>

                                        @else

                                            —

                                        @endif

                                    </td>


                                    <td>
                                        {{ $contact->phone ?: $contact->mobile ?: '—' }}
                                    </td>


                                    <td>

                                        @if($contact->status === 'active')

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

                                        <a
                                            href="{{ route('crm.contacts.show', $contact) }}"
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
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted mb-3">
                                            No contacts have been added.
                                        </div>

                                        <a
                                            href="{{ route('crm.contacts.create', [
                                                'customer_id' => $customer->id
                                            ]) }}"
                                            class="btn btn-sm btn-primary"
                                        >
                                            <i class="bi bi-person-plus me-1"></i>
                                            Add First Contact
                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
                PROPERTIES TAB
            ====================================================== --}}

            <div
                class="tab-pane fade"
                id="properties-pane"
                role="tabpanel"
                aria-labelledby="properties-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Properties
                        </h5>

                        <div class="text-muted small">
                            Properties associated with this customer.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.properties.create', [
                            'customer_id' => $customer->id
                        ]) }}"
                        class="btn btn-sm btn-primary"
                    >

                        <i class="bi bi-building-add me-1"></i>

                        Add Property

                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table crm-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Property
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Primary Contact
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($customer->properties as $property)

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route('crm.properties.show', $property) }}"
                                            class="customer-name-link"
                                        >
                                            {{ $property->name }}
                                        </a>

                                        <div class="customer-code">
                                            {{ $property->property_code }}
                                        </div>

                                    </td>


                                    <td>
                                        {{ $property->property_type_label }}
                                    </td>


                                    <td>

                                        @if($property->primaryContact)

                                            <a
                                                href="{{ route('crm.contacts.show', $property->primaryContact) }}"
                                                class="customer-name-link"
                                            >
                                                {{ $property->primaryContact->display_name }}
                                            </a>

                                        @else

                                            <span class="text-muted">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        @if($property->status === 'active')

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

                                        <a
                                            href="{{ route('crm.properties.show', $property) }}"
                                            class="btn btn-sm btn-light"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted mb-3">
                                            No properties have been added.
                                        </div>

                                        <a
                                            href="{{ route('crm.properties.create', [
                                                'customer_id' => $customer->id
                                            ]) }}"
                                            class="btn btn-sm btn-primary"
                                        >

                                            <i class="bi bi-building-add me-1"></i>

                                            Add First Property

                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
                ACTIVITIES TAB
            ====================================================== --}}

            <div
                class="tab-pane fade"
                id="activities-pane"
                role="tabpanel"
                aria-labelledby="activities-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Activities
                        </h5>

                        <div class="text-muted small">
                            Recent interactions and CRM history.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.activities.create', [
                            'customer_id' => $customer->id
                        ]) }}"
                        class="btn btn-sm btn-primary"
                    >

                        <i class="bi bi-plus-lg me-1"></i>

                        Add Activity

                    </a>

                </div>


                <div class="activity-timeline">

                    @forelse(
                        $customer->activities
                            ->sortByDesc('scheduled_at')
                            ->take(10)
                        as $activity
                    )

                        <div class="activity-timeline-item">

                            <div class="activity-timeline-icon activity-type-{{ $activity->activity_type }}">

                                <i class="bi bi-{{
                                    match($activity->activity_type) {

                                        'call' => 'telephone',

                                        'email' => 'envelope',

                                        'meeting' => 'people',

                                        'site_visit' => 'geo-alt',

                                        'note' => 'journal-text',

                                        'sms' => 'chat',

                                        default => 'activity',

                                    }
                                }}"></i>

                            </div>


                            <div class="activity-timeline-content">

                                <div class="activity-timeline-header">

                                    <a
                                        href="{{ route('crm.activities.show', $activity) }}"
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


                                @if($activity->description)

                                    <div class="activity-timeline-description">

                                        {{ \Illuminate\Support\Str::limit(
                                            $activity->description,
                                            180
                                        ) }}

                                    </div>

                                @endif

                            </div>

                        </div>

                    @empty

                        <div class="text-center py-5">

                            <div class="text-muted mb-3">
                                No activities recorded for this customer.
                            </div>

                            <a
                                href="{{ route('crm.activities.create', [
                                    'customer_id' => $customer->id
                                ]) }}"
                                class="btn btn-sm btn-primary"
                            >

                                <i class="bi bi-plus-lg me-1"></i>

                                Add First Activity

                            </a>

                        </div>

                    @endforelse

                </div>

            </div>


            {{-- =====================================================
                JOBS TAB
            ====================================================== --}}

            <div
                class="tab-pane fade"
                id="jobs-pane"
                role="tabpanel"
                aria-labelledby="jobs-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Jobs
                        </h5>

                        <div class="text-muted small">
                            Jobs associated with this customer.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.jobs.create', [
                            'customer_id' => $customer->id
                        ]) }}"
                        class="btn btn-sm btn-primary"
                    >

                        <i class="bi bi-plus-lg me-1"></i>

                        Create Job

                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table crm-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Job
                                </th>

                                <th>
                                    Property
                                </th>

                                <th>
                                    Schedule
                                </th>

                                <th>
                                    Priority
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($customer->jobs as $job)

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
                                            class="customer-name-link"
                                        >
                                            {{ $job->title }}
                                        </a>

                                        <div class="customer-code">
                                            {{ $job->work_order_number }}
                                        </div>

                                    </td>


                                    <td>

                                        @if($job->property)

                                            {{ $job->property->name }}

                                        @else

                                            <span class="text-muted">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        @if($job->scheduled_start_at)

                                            {{ $job->scheduled_start_at->format('M d, Y h:i A') }}

                                        @else

                                            <span class="text-muted">
                                                Not scheduled
                                            </span>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $job->priority_label }}
                                    </td>


                                    <td>

                                        <span
                                            class="status-badge status-{{
                                                str_replace(
                                                    '_',
                                                    '-',
                                                    $job->status
                                                )
                                            }}"
                                        >
                                            {{ $job->status_label }}
                                        </span>

                                    </td>


                                    <td class="text-end">

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
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
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted mb-3">
                                            No jobs have been created.
                                        </div>

                                        <a
                                            href="{{ route('crm.jobs.create', [
                                                'customer_id' => $customer->id
                                            ]) }}"
                                            class="btn btn-sm btn-primary"
                                        >

                                            <i class="bi bi-plus-lg me-1"></i>

                                            Create First Job

                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
                WORK ORDERS TAB
            ====================================================== --}}

            <div
                class="tab-pane fade"
                id="work-orders-pane"
                role="tabpanel"
                aria-labelledby="work-orders-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Work Orders
                        </h5>

                        <div class="text-muted small">
                            Work orders associated with this customer.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.jobs.create', [
                            'customer_id' => $customer->id
                        ]) }}"
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

                                <th>
                                    Work Order
                                </th>

                                <th>
                                    Property
                                </th>

                                <th>
                                    Schedule
                                </th>

                                <th>
                                    Priority
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($customer->jobs as $job)

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
                                            class="customer-name-link"
                                        >
                                            {{ $job->work_order_number }}
                                        </a>

                                        <div class="customer-code">
                                            {{ $job->title }}
                                        </div>

                                    </td>


                                    <td>

                                        @if($job->property)

                                            {{ $job->property->name }}

                                        @else

                                            <span class="text-muted">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        @if($job->scheduled_start_at)

                                            {{ $job->scheduled_start_at->format('M d, Y h:i A') }}

                                        @else

                                            <span class="text-muted">
                                                Not scheduled
                                            </span>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $job->priority_label }}
                                    </td>


                                    <td>

                                        <span
                                            class="status-badge status-{{
                                                str_replace(
                                                    '_',
                                                    '-',
                                                    $job->status
                                                )
                                            }}"
                                        >
                                            {{ $job->status_label }}
                                        </span>

                                    </td>


                                    <td class="text-end">

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
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
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted mb-3">
                                            No work orders have been created.
                                        </div>

                                        <a
                                            href="{{ route('crm.jobs.create', [
                                                'customer_id' => $customer->id
                                            ]) }}"
                                            class="btn btn-sm btn-primary"
                                        >

                                            <i class="bi bi-plus-lg me-1"></i>

                                            Create First Work Order

                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
                SCHEDULES TAB
            ====================================================== --}}

            <div
                class="tab-pane fade"
                id="schedules-pane"
                role="tabpanel"
                aria-labelledby="schedules-tab"
                tabindex="0"
            >

                <div class="section-header mb-3">

                    <div>

                        <h5 class="mb-1">
                            Schedules
                        </h5>

                        <div class="text-muted small">
                            Scheduled jobs and work orders for this customer.
                        </div>

                    </div>


                    <a
                        href="{{ route('crm.jobs.index') }}"
                        class="btn btn-sm btn-primary"
                    >

                        <i class="bi bi-calendar-plus me-1"></i>

                        Manage Schedules

                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table crm-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Job / Work Order
                                </th>

                                <th>
                                    Property
                                </th>

                                <th>
                                    Start
                                </th>

                                <th>
                                    End
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse(
                                $customer->jobs
                                    ->filter(
                                        fn ($job) =>
                                            $job->scheduled_start_at
                                    )
                                    ->sortBy('scheduled_start_at')
                                as $job
                            )

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
                                            class="customer-name-link"
                                        >
                                            {{ $job->title }}
                                        </a>

                                        <div class="customer-code">
                                            {{ $job->work_order_number }}
                                        </div>

                                    </td>


                                    <td>

                                        @if($job->property)

                                            {{ $job->property->name }}

                                        @else

                                            <span class="text-muted">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $job->scheduled_start_at
                                            ->format('M d, Y h:i A')
                                        }}

                                    </td>


                                    <td>

                                        @if($job->scheduled_end_at)

                                            {{ $job->scheduled_end_at
                                                ->format('M d, Y h:i A')
                                            }}

                                        @else

                                            —

                                        @endif

                                    </td>


                                    <td>

                                        <span
                                            class="status-badge status-{{
                                                str_replace(
                                                    '_',
                                                    '-',
                                                    $job->status
                                                )
                                            }}"
                                        >
                                            {{ $job->status_label }}
                                        </span>

                                    </td>


                                    <td class="text-end">

                                        <a
                                            href="{{ route('crm.jobs.show', $job) }}"
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
                                        class="text-center py-5"
                                    >

                                        <div class="text-muted">
                                            No schedules found for this customer.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


@endsection

