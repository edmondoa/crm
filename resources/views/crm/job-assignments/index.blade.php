@extends('layouts.admin')

@section('title', 'Job Assignments')

@section('page-title', 'Job Assignments')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="page-breadcrumb">

                <a href="{{ route('crm.jobs.index') }}">
                    Jobs
                </a>

                <span>/</span>

                <span>
                    {{ $job->job_code ?? 'Job #' . $job->id }}
                </span>

            </div>

            <h1 class="page-title">
                Job Assignments
            </h1>

            <p class="page-subtitle">
                Employees assigned to this job.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.jobs.show', $job) }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left"></i>
                Back to Job
            </a>

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

    </div>

</div>


@if(session('success'))

    <div class="alert alert-success erp-alert">
        <i class="bi bi-check-circle"></i>
        {{ session('success') }}
    </div>

@endif


<div class="erp-card">

    <div class="erp-card-header">

        <div>

            <div class="erp-card-title">
                {{ $job->job_code ?? 'Job #' . $job->id }}
            </div>

            <div class="erp-card-subtitle">
                {{ $job->title ?? 'Job / Work Order' }}
            </div>

        </div>

    </div>


    <div class="erp-card-body p-0">

        @if($assignments->isEmpty())

            <div class="erp-empty-state">

                <div class="erp-empty-icon">
                    <i class="bi bi-people"></i>
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

            <div class="table-responsive">

                <table class="table erp-table mb-0">

                    <thead>

                        <tr>

                            <th>
                                Employee
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Assigned
                            </th>

                            <th class="text-end">
                                Est. Hours
                            </th>

                            <th class="text-end">
                                Actual
                            </th>

                            <th class="text-end">
                                Rate
                            </th>

                            <th class="text-end">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($assignments as $assignment)

                            <tr>

                                <td>

                                    <div class="erp-employee-cell">

                                        <div class="erp-avatar">
                                            {{ $assignment->employee->initials }}
                                        </div>

                                        <div>

                                            <a
                                                href="{{ route(
                                                    'crm.employees.show',
                                                    $assignment->employee
                                                ) }}"
                                                class="erp-employee-name text-decoration-none"
                                            >
                                                {{ $assignment->employee->full_name }}
                                            </a>

                                            <div class="erp-table-meta">

                                                {{ $assignment->employee->employee_no }}

                                                @if($assignment->is_primary)

                                                    <span class="badge bg-primary ms-1">
                                                        Primary
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    {{ $assignment->assignment_role ?: '—' }}
                                </td>


                                <td>

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

                                </td>


                                <td>
                                    {{ $assignment->assigned_at?->format(
                                        'M d, Y'
                                    ) ?: '—' }}
                                </td>


                                <td class="text-end">

                                    {{ $assignment->estimated_hours !== null
                                        ? number_format(
                                            $assignment->estimated_hours,
                                            2
                                        )
                                        : '—'
                                    }}

                                </td>


                                <td class="text-end">

                                    {{ $assignment->actual_hours !== null
                                        ? number_format(
                                            $assignment->actual_hours,
                                            2
                                        )
                                        : '—'
                                    }}

                                </td>


                                <td class="text-end">

                                    @if($assignment->hourly_rate !== null)

                                        ₱{{ number_format(
                                            $assignment->hourly_rate,
                                            2
                                        ) }}

                                    @else

                                        —

                                    @endif

                                </td>


                                <td class="text-end">

                                    <div class="dropdown">

                                        <button
                                            class="btn btn-sm btn-light"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                        >
                                            <i class="bi bi-three-dots"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>

                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route(
                                                        'crm.jobs.assignments.edit',
                                                        [
                                                            $job,
                                                            $assignment
                                                        ]
                                                    ) }}"
                                                >
                                                    <i class="bi bi-pencil me-2"></i>
                                                    Edit
                                                </a>

                                            </li>


                                            @if(!$assignment->is_primary)

                                                <li>

                                                    <form
                                                        method="POST"
                                                        action="{{ route(
                                                            'crm.jobs.assignments.primary',
                                                            [
                                                                $job,
                                                                $assignment
                                                            ]
                                                        ) }}"
                                                    >

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="dropdown-item"
                                                        >
                                                            <i class="bi bi-star me-2"></i>
                                                            Make Primary
                                                        </button>

                                                    </form>

                                                </li>

                                            @endif


                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>


                                            <li>

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'crm.jobs.assignments.destroy',
                                                        [
                                                            $job,
                                                            $assignment
                                                        ]
                                                    ) }}"
                                                    onsubmit="return confirm('Remove this employee from the job?')"
                                                >

                                                    @csrf

                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item text-danger"
                                                    >
                                                        <i class="bi bi-trash me-2"></i>
                                                        Remove
                                                    </button>

                                                </form>

                                            </li>

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>

@endsection