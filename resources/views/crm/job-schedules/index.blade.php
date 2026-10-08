@extends('layouts.admin')

@section('title', 'Job Schedules')

@section('page-title', 'Job Schedules')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Job Schedules
            </h1>

            <p class="page-subtitle">
                Manage scheduled jobs and employee assignments.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.job-schedules.calendar') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-calendar3 me-1"></i>
                Calendar
            </a>

            <a
                href="{{ route('crm.job-schedules.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                New Schedule
            </a>

        </div>

    </div>

</div>


@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


<div class="card erp-card mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('crm.job-schedules.index') }}"
        >

            <div class="row g-3">

                <div class="col-lg-2 col-md-6">

                    <label class="form-label">
                        From
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="form-control"
                    >

                </div>


                <div class="col-lg-2 col-md-6">

                    <label class="form-label">
                        To
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="form-control"
                    >

                </div>


                <div class="col-lg-3 col-md-6">

                    <label class="form-label">
                        Employee
                    </label>

                    <select
                        name="employee_id"
                        class="form-select"
                    >

                        <option value="">
                            All Employees
                        </option>

                        @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    request('employee_id')
                                    == $employee->id
                                )
                            >
                                {{ $employee->full_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-lg-3 col-md-6">

                    <label class="form-label">
                        Job
                    </label>

                    <select
                        name="job_id"
                        class="form-select"
                    >

                        <option value="">
                            All Jobs
                        </option>

                        @foreach($jobs as $job)

                            <option
                                value="{{ $job->id }}"
                                @selected(
                                    request('job_id')
                                    == $job->id
                                )
                            >
                                {{ $job->title
                                    ?? $job->name
                                    ?? $job->job_name
                                    ?? 'Job #' . $job->id }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-lg-2 col-md-6">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All
                        </option>

                        <option
                            value="scheduled"
                            @selected(
                                request('status') === 'scheduled'
                            )
                        >
                            Scheduled
                        </option>

                        <option
                            value="confirmed"
                            @selected(
                                request('status') === 'confirmed'
                            )
                        >
                            Confirmed
                        </option>

                        <option
                            value="in_progress"
                            @selected(
                                request('status') === 'in_progress'
                            )
                        >
                            In Progress
                        </option>

                        <option
                            value="completed"
                            @selected(
                                request('status') === 'completed'
                            )
                        >
                            Completed
                        </option>

                        <option
                            value="cancelled"
                            @selected(
                                request('status') === 'cancelled'
                            )
                        >
                            Cancelled
                        </option>

                    </select>

                </div>


                <div class="col-12">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-search me-1"></i>
                            Filter
                        </button>

                        <a
                            href="{{ route('crm.job-schedules.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card erp-card">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table erp-table mb-0">

                <thead>

                    <tr>

                        <th>
                            Date
                        </th>

                        <th>
                            Time
                        </th>

                        <th>
                            Job
                        </th>

                        <th>
                            Employee
                        </th>

                        <th>
                            Location
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-end">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($schedules as $schedule)

                        <tr>

                            <td>
                                <strong>
                                    {{
                                        $schedule->scheduled_date
                                            ->format('M d, Y')
                                    }}
                                </strong>
                            </td>

                            <td>
                                {{ $schedule->time_range }}
                            </td>

                            <td>

                                <a
                                    href="{{ route(
                                        'crm.job-schedules.show',
                                        $schedule
                                    ) }}"
                                    class="erp-link"
                                >
                                    {{
                                        $schedule->job->title
                                        ?? $schedule->job->name
                                        ?? $schedule->job->job_name
                                        ?? 'Job #' . $schedule->job_id
                                    }}
                                </a>

                            </td>

                            <td>
                                {{
                                    $schedule->employee->full_name
                                    ?? '—'
                                }}
                            </td>

                            <td>
                                {{ $schedule->location ?? '—' }}
                            </td>

                            <td>

                                @php

                                    $badge = match(
                                        $schedule->status
                                    ) {
                                        'scheduled'
                                            => 'bg-secondary',

                                        'confirmed'
                                            => 'bg-primary',

                                        'in_progress'
                                            => 'bg-warning text-dark',

                                        'completed'
                                            => 'bg-success',

                                        'cancelled'
                                            => 'bg-danger',

                                        default
                                            => 'bg-secondary',
                                    };

                                @endphp

                                <span
                                    class="badge {{ $badge }}"
                                >
                                    {{
                                        $schedule->status_label
                                    }}
                                </span>

                            </td>

                            <td class="text-end">

                                <div class="dropdown">

                                    <button
                                        class="btn btn-sm btn-light"
                                        data-bs-toggle="dropdown"
                                    >
                                        <i class="bi bi-three-dots"></i>
                                    </button>

                                    <ul
                                        class="dropdown-menu dropdown-menu-end"
                                    >

                                        <li>

                                            <a
                                                class="dropdown-item"
                                                href="{{ route(
                                                    'crm.job-schedules.show',
                                                    $schedule
                                                ) }}"
                                            >
                                                <i class="bi bi-eye me-2"></i>
                                                View
                                            </a>

                                        </li>

                                        <li>

                                            <a
                                                class="dropdown-item"
                                                href="{{ route(
                                                    'crm.job-schedules.edit',
                                                    $schedule
                                                ) }}"
                                            >
                                                <i class="bi bi-pencil me-2"></i>
                                                Edit
                                            </a>

                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'crm.job-schedules.destroy',
                                                    $schedule
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Delete this schedule?'
                                                    );
                                                "
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="dropdown-item text-danger"
                                                >
                                                    <i class="bi bi-trash me-2"></i>
                                                    Delete
                                                </button>

                                            </form>

                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <div class="empty-state">

                                    <i
                                        class="bi bi-calendar-x"
                                    ></i>

                                    <h5>
                                        No schedules found
                                    </h5>

                                    <p>
                                        Create a schedule to start
                                        planning jobs.
                                    </p>

                                    <a
                                        href="{{ route(
                                            'crm.job-schedules.create'
                                        ) }}"
                                        class="btn btn-primary"
                                    >
                                        Create Schedule
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if($schedules->hasPages())

        <div class="card-footer">

            {{ $schedules->links() }}

        </div>

    @endif

</div>

@endsection