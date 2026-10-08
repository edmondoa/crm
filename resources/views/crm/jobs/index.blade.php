@extends('layouts.admin')

@section('title', 'Jobs / Work Orders')

@section('page-title', 'Jobs / Work Orders')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Jobs / Work Orders
            </h1>

            <p class="page-subtitle">
                Manage scheduled work, service jobs
                and field work orders.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.jobs.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Create Work Order
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
     STATISTICS
========================================================== --}}

<div class="row g-3 mb-4">

    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-briefcase"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    Total Jobs
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['total']) }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-folder2-open"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    Open
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['open']) }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    Scheduled
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['scheduled']) }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-play-circle"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    In Progress
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['in_progress']) }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    Completed
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['completed']) }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>

                <div class="crm-stat-label">
                    Overdue
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['overdue']) }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================== --}}

<div class="card mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="job-filters">

                <div class="filter-search">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search job, customer or work order..."
                    >

                </div>


                <div>

                    <label class="form-label">
                        Customer
                    </label>

                    <select
                        name="customer_id"
                        class="form-select"
                    >

                        <option value="">
                            All Customers
                        </option>

                        @foreach($customers as $customer)

                            <option
                                value="{{ $customer->id }}"
                                @selected(
                                    request('customer_id')
                                    == $customer->id
                                )
                            >
                                {{ $customer->display_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label class="form-label">
                        Type
                    </label>

                    <select
                        name="job_type"
                        class="form-select"
                    >

                        <option value="">
                            All Types
                        </option>

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
                                    request('job_type')
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Statuses
                        </option>

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
                                    request('status')
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label class="form-label">
                        Priority
                    </label>

                    <select
                        name="priority"
                        class="form-select"
                    >

                        <option value="">
                            All Priorities
                        </option>

                        @foreach([
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                            'urgent' => 'Urgent',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('priority')
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route(
                            'crm.jobs.index'
                        ) }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     TABLE
========================================================== --}}

<div class="card">

    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Work Order</th>

                    <th>Customer</th>

                    <th>Property</th>

                    <th>Type</th>

                    <th>Schedule</th>

                    <th>Priority</th>

                    <th>Status</th>

                    <th class="text-end">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($jobs as $job)

                    <tr>

                        <td>

                            <div class="job-table-name">

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
                                    {{ $job->work_order_number
                                        ?: $job->job_code }}
                                </div>

                            </div>

                        </td>


                        <td>

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

                        </td>


                        <td>

                            @if($job->property)

                                <a
                                    href="{{ route(
                                        'crm.properties.show',
                                        $job->property
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $job->property->name }}
                                </a>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            <span class="job-type-badge">

                                <i
                                    class="bi {{ $job->job_type_icon }}"
                                ></i>

                                {{ $job->job_type_label }}

                            </span>

                        </td>


                        <td>

                            @if($job->scheduled_start_at)

                                <div
                                    class="{{ $job->is_overdue
                                        ? 'job-overdue'
                                        : 'job-date' }}"
                                >
                                    {{
                                        $job->scheduled_start_at
                                            ->format('M d, Y')
                                    }}
                                </div>

                                <div class="job-time">

                                    {{
                                        $job->scheduled_start_at
                                            ->format('h:i A')
                                    }}

                                </div>

                                @if($job->is_overdue)

                                    <div class="job-overdue-label">
                                        Overdue
                                    </div>

                                @endif

                            @else

                                <span class="text-muted">
                                    Not scheduled
                                </span>

                            @endif

                        </td>


                        <td>

                            <span
                                class="job-priority job-priority-{{ $job->priority }}"
                            >
                                {{ $job->priority_label }}
                            </span>

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
                                title="View"
                            >
                                <i class="bi bi-eye"></i>
                            </a>

                            <a
                                href="{{ route(
                                    'crm.jobs.edit',
                                    $job
                                ) }}"
                                class="btn btn-sm btn-light"
                                title="Edit"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <div class="text-muted">

                                <i
                                    class="bi bi-briefcase fs-3 d-block mb-2"
                                ></i>

                                No jobs or work orders found.

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($jobs->hasPages())

        <div class="card-footer">

            {{ $jobs->links() }}

        </div>

    @endif

</div>

@endsection