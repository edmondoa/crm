@extends('layouts.admin')

@section('title', 'Work Order')

@section('page-title', 'Work Order')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="job-header-code">
                {{ $job->work_order_number }}
            </div>

            <h1 class="page-title">
                {{ $job->title }}
            </h1>

            <p class="page-subtitle">
                {{ $job->job_code }}
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="{{ route(
                    'crm.jobs.edit',
                    $job
                ) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route(
                    'crm.jobs.index'
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
         MAIN CONTENT
    ====================================================== --}}

    <div class="col-lg-8">


        {{-- Job overview --}}

        <div class="card mb-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Work Order
                    </div>

                    <div class="card-subtitle">
                        Job details and current status.
                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="job-profile">

                    <div class="job-profile-icon">
                        <i
                            class="bi {{ $job->job_type_icon }}"
                        ></i>
                    </div>

                    <div>

                        <h2 class="job-profile-name">
                            {{ $job->title }}
                        </h2>

                        <div class="job-profile-code">
                            {{ $job->work_order_number }}
                        </div>

                    </div>

                </div>


                <div class="row g-4 mt-1">


                    <div class="col-md-4">

                        <div class="detail-label">
                            Job Type
                        </div>

                        <div class="detail-value">
                            {{ $job->job_type_label }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="detail-label">
                            Priority
                        </div>

                        <div class="detail-value">
                            {{ $job->priority_label }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="detail-label">
                            Status
                        </div>

                        <div class="detail-value">

                            <span
                                class="status-badge status-{{ str_replace(
                                    '_',
                                    '-',
                                    $job->status
                                ) }}"
                            >
                                {{ $job->status_label }}
                            </span>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Scheduled Start
                        </div>

                        <div class="detail-value">

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

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Scheduled End
                        </div>

                        <div class="detail-value">

                            @if($job->scheduled_end_at)

                                {{
                                    $job->scheduled_end_at
                                        ->format(
                                            'M d, Y h:i A'
                                        )
                                }}

                            @else

                                <span class="text-muted">
                                    Not scheduled
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Started
                        </div>

                        <div class="detail-value">

                            @if($job->started_at)

                                {{
                                    $job->started_at
                                        ->format(
                                            'M d, Y h:i A'
                                        )
                                }}

                            @else

                                <span class="text-muted">
                                    Not started
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Completed
                        </div>

                        <div class="detail-value">

                            @if($job->completed_at)

                                {{
                                    $job->completed_at
                                        ->format(
                                            'M d, Y h:i A'
                                        )
                                }}

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


        {{-- Description --}}

        @if($job->description)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Job Description
                    </div>

                </div>

                <div class="card-body">

                    <div class="job-description">

                        {!! nl2br(
                            e($job->description)
                        ) !!}

                    </div>

                </div>

            </div>

        @endif


        {{-- Scope --}}

        @if($job->scope_of_work)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Scope of Work
                    </div>

                </div>

                <div class="card-body">

                    <div class="job-description">

                        {!! nl2br(
                            e($job->scope_of_work)
                        ) !!}

                    </div>

                </div>

            </div>

        @endif


        {{-- Financial --}}

        <div class="card mb-4">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Financial Reference
                    </div>

                    <div class="card-subtitle">
                        Preliminary job amounts.
                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Estimated Amount
                        </div>

                        <div class="job-amount">

                            @if(
                                $job->estimated_amount !== null
                            )

                                $
                                {{
                                    number_format(
                                        (float)
                                            $job->estimated_amount,
                                        2
                                    )
                                }}

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Approved Amount
                        </div>

                        <div class="job-amount">

                            @if(
                                $job->approved_amount !== null
                            )

                                $
                                {{
                                    number_format(
                                        (float)
                                            $job->approved_amount,
                                        2
                                    )
                                }}

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        @if($job->customer_notes)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Customer Notes
                    </div>

                </div>

                <div class="card-body">

                    <div class="job-description">

                        {!! nl2br(
                            e($job->customer_notes)
                        ) !!}

                    </div>

                </div>

            </div>

        @endif
        

        @if($job->internal_notes)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Internal Notes
                    </div>

                </div>

                <div class="card-body">

                    <div class="job-description">

                        {!! nl2br(
                            e($job->internal_notes)
                        ) !!}

                    </div>

                </div>

            </div>

        @endif
        {{-- =========================================================
            JOB ASSIGNMENTS
            ========================================================= --}}

        <div class="erp-card mt-4">

            <div class="erp-card-header">

                <div>

                    <div class="erp-card-title">
                        Job Assignments
                    </div>

                    <div class="erp-card-subtitle">
                        Employees assigned to this job.
                    </div>

                </div>

                <a
                    href="{{ route(
                        'crm.jobs.assignments.index',
                        $job
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-people"></i>
                    Manage Assignments
                </a>

            </div>

            <div class="erp-card-body">

                @php
                    $jobAssignments = $job->assignments()
                        ->with('employee')
                        ->orderByDesc('is_primary')
                        ->orderBy('assigned_at')
                        ->get();
                @endphp


                @if($jobAssignments->isEmpty())

                    <div class="erp-empty-state py-4">

                        <div class="erp-empty-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>

                        <h5>
                            No employees assigned
                        </h5>

                        <p>
                            Assign employees or technicians to this job.
                        </p>

                        <a
                            href="{{ route(
                                'crm.jobs.assignments.create',
                                $job
                            ) }}"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-person-plus"></i>
                            Assign Employee
                        </a>

                    </div>

                @else
                    @php
                        $estimatedLaborCost = $job->assignments->sum(
                            fn ($assignment) => $assignment->estimated_cost
                        );

                        $actualLaborCost = $job->assignments->sum(
                            fn ($assignment) => $assignment->actual_cost
                        );
                    @endphp
                    <div class="row g-3 mt-1">

                        <div class="col-md-4">

                            <div class="erp-info-label">
                                Assigned Employees
                            </div>

                            <div class="erp-info-value">
                                {{ $job->assignments->count() }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="erp-info-label">
                                Estimated Labor
                            </div>

                            <div class="erp-info-value">
                                ₱{{ number_format($estimatedLaborCost, 2) }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="erp-info-label">
                                Actual Labor
                            </div>

                            <div class="erp-info-value">
                                ₱{{ number_format($actualLaborCost, 2) }}
                            </div>

                        </div>

                    </div>
                    <div class="row g-3">

                        @foreach($jobAssignments as $assignment)

                            <div class="col-md-6 col-xl-4">

                                <div class="border rounded p-3 h-100">

                                    <div class="d-flex align-items-center">

                                        <div class="erp-avatar me-2">

                                            {{ $assignment->employee->initials }}

                                        </div>

                                        <div class="flex-grow-1">

                                            <a
                                                href="{{ route(
                                                    'crm.employees.show',
                                                    $assignment->employee
                                                ) }}"
                                                class="fw-semibold text-decoration-none"
                                            >
                                                {{ $assignment->employee->full_name }}
                                            </a>

                                            <div class="text-muted small">

                                                {{
                                                    $assignment->assignment_role
                                                    ?: 'Assigned Employee'
                                                }}

                                            </div>

                                        </div>

                                    </div>


                                    <div class="mt-3">

                                        @php

                                            $statusClass = match(
                                                $assignment->status
                                            ) {
                                                'assigned' => 'secondary',
                                                'accepted' => 'info',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary',
                                            };

                                        @endphp

                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $assignment->status
                                                )
                                            ) }}
                                        </span>


                                        @if($assignment->is_primary)

                                            <span class="badge bg-primary">
                                                Primary
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>
        <div class="erp-card mt-4">

            <div class="erp-card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Estimates
                </h5>

                <a
                    href="{{ route('crm.estimates.create', [
                        'job_id' => $job->id,
                        'customer_id' => $job->customer_id,
                        'property_id' => $job->property_id,
                    ]) }}"
                    class="btn btn-sm btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    New Estimate
                </a>

            </div>

            <div class="table-responsive">

                <table class="table erp-table mb-0">

                    <thead>

                        <tr>
                            <th>Estimate</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">
                                Total
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($job->estimates as $estimate)

                            <tr>

                                <td>

                                    <a
                                        href="{{ route(
                                            'crm.estimates.show',
                                            $estimate
                                        ) }}"
                                        class="fw-semibold"
                                    >
                                        {{ $estimate->estimate_number }}
                                    </a>

                                </td>

                                <td>
                                    {{ $estimate->estimate_date?->format('M d, Y') }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($estimate->status) }}
                                    </span>
                                </td>

                                <td class="text-end fw-semibold">
                                    ₱{{ number_format(
                                        $estimate->grand_total,
                                        2
                                    ) }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center text-muted py-4"
                                >
                                    No estimates for this job.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =====================================================
         RIGHT SIDEBAR
    ====================================================== --}}

    <div class="col-lg-4">


        {{-- CRM context --}}

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Customer & Location
                </div>

            </div>


            <div class="card-body">


                {{-- Customer --}}

                <div class="job-context-item">

                    <div class="job-context-icon">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Customer
                        </div>

                        @if($job->customer)

                            <a
                                href="{{ route(
                                    'crm.customers.show',
                                    $job->customer
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $job->customer->display_name }}
                            </a>

                            <div class="customer-code">
                                {{ $job->customer->customer_code }}
                            </div>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Contact --}}

                @if($job->contact)

                    <div class="job-context-item">

                        <div class="job-context-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Contact
                            </div>

                            <a
                                href="{{ route(
                                    'crm.contacts.show',
                                    $job->contact
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $job->contact->display_name }}
                            </a>

                            @if($job->contact->job_title)

                                <div class="customer-code">
                                    {{ $job->contact->job_title }}
                                </div>

                            @endif

                        </div>

                    </div>

                @endif


                {{-- Property --}}

                @if($job->property)

                    <div class="job-context-item">

                        <div class="job-context-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Property
                            </div>

                            <a
                                href="{{ route(
                                    'crm.properties.show',
                                    $job->property
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $job->property->name }}
                            </a>

                            <div class="customer-code">
                                {{ $job->property->property_code }}
                            </div>

                        </div>

                    </div>

                @endif


                {{-- Location --}}

                @if($job->propertyLocation)

                    <div class="job-context-item">

                        <div class="job-context-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Work Location
                            </div>

                            <div class="detail-value">
                                {{
                                    $job->propertyLocation
                                        ->location_name
                                }}
                            </div>

                            <div class="customer-code">

                                {{
                                    implode(', ', array_filter([
                                        $job->propertyLocation
                                            ->city,

                                        $job->propertyLocation
                                            ->state,

                                        $job->propertyLocation
                                            ->zip_code,
                                    ]))
                                }}

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- Actions --}}

        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Actions
                </div>

            </div>

            <div class="card-body">

                <a
                    href="{{ route(
                        'crm.jobs.edit',
                        $job
                    ) }}"
                    class="btn btn-primary w-100 mb-2"
                >
                    <i class="bi bi-pencil me-1"></i>
                    Edit Work Order
                </a>


                <form
                    method="POST"
                    action="{{ route(
                        'crm.jobs.destroy',
                        $job
                    ) }}"
                    onsubmit="return confirm(
                        'Are you sure you want to delete this work order?'
                    );"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-outline-danger w-100"
                    >
                        <i class="bi bi-trash me-1"></i>
                        Delete Work Order
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection