@extends('layouts.admin')

@section('title', $employee->full_name)

@section('page-title', $employee->full_name)

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="page-breadcrumb">

                <a href="{{ route('crm.employees.index') }}">
                    Employees
                </a>

                <span>/</span>

                <span>
                    {{ $employee->employee_no }}
                </span>

            </div>

            <h1 class="page-title">
                {{ $employee->full_name }}
            </h1>

            <p class="page-subtitle">
                {{ $employee->position ?: 'Employee' }}
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route(
                    'crm.employees.edit',
                    $employee
                ) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil"></i>
                Edit Employee
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


<div class="row g-4">

    {{-- =====================================================
         EMPLOYEE SUMMARY
         ===================================================== --}}

    <div class="col-lg-4">

        <div class="erp-card">

            <div class="erp-card-body">

                <div class="employee-profile">

                    <div class="employee-profile-avatar">
                        {{ $employee->initials }}
                    </div>

                    <h2 class="employee-profile-name">
                        {{ $employee->full_name }}
                    </h2>

                    <div class="employee-profile-position">
                        {{ $employee->position ?: 'Employee' }}
                    </div>

                    <div class="mt-3">

                        @php

                            $statusClass = match(
                                $employee->status
                            ) {
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'on_leave' => 'warning',
                                'terminated' => 'danger',
                                default => 'secondary',
                            };

                        @endphp

                        <span class="badge bg-{{ $statusClass }}">
                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $employee->status
                                )
                            ) }}
                        </span>

                    </div>

                </div>


                <hr>


                <div class="erp-detail-list">

                    <div class="erp-detail-row">

                        <span>
                            Employee No.
                        </span>

                        <strong>
                            {{ $employee->employee_no }}
                        </strong>

                    </div>


                    <div class="erp-detail-row">

                        <span>
                            Department
                        </span>

                        <strong>
                            {{ $employee->department ?: '—' }}
                        </strong>

                    </div>


                    <div class="erp-detail-row">

                        <span>
                            Employment
                        </span>

                        <strong>
                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $employee->employment_type
                                )
                            ) }}
                        </strong>

                    </div>


                    <div class="erp-detail-row">

                        <span>
                            Hire Date
                        </span>

                        <strong>
                            {{ $employee->hire_date?->format('M d, Y') ?: '—' }}
                        </strong>

                    </div>


                    <div class="erp-detail-row">

                        <span>
                            Hourly Rate
                        </span>

                        <strong>

                            @if($employee->hourly_rate !== null)

                                ₱{{ number_format(
                                    $employee->hourly_rate,
                                    2
                                ) }}

                            @else

                                —

                            @endif

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         CONTACT
         ===================================================== --}}

    <div class="col-lg-8">

        <div class="erp-card mb-4">

            <div class="erp-card-header">

                <div>

                    <div class="erp-card-title">
                        Contact Information
                    </div>

                </div>

            </div>

            <div class="erp-card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="erp-info-label">
                            Email
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->email ?: '—' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="erp-info-label">
                            Phone
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->phone ?: '—' }}
                        </div>

                    </div>


                    <div class="col-12">

                        <div class="erp-info-label">
                            Address
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->address ?: '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
             EMERGENCY CONTACT
             ================================================= --}}

        <div class="erp-card mb-4">

            <div class="erp-card-header">

                <div class="erp-card-title">
                    Emergency Contact
                </div>

            </div>

            <div class="erp-card-body">

                <div class="row g-4">

                    <div class="col-md-5">

                        <div class="erp-info-label">
                            Name
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->emergency_contact_name ?: '—' }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="erp-info-label">
                            Relationship
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->emergency_contact_relationship ?: '—' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="erp-info-label">
                            Phone
                        </div>

                        <div class="erp-info-value">
                            {{ $employee->emergency_contact_phone ?: '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     JOB HISTORY
     ========================================================= --}}

<div class="erp-card mt-4">

    <div class="erp-card-header">

        <div>

            <div class="erp-card-title">
                Job Assignments
            </div>

            <div class="erp-card-subtitle">
                Jobs assigned to this employee.
            </div>

        </div>

    </div>

    <div class="erp-card-body p-0">

        @if($employee->jobAssignments->isEmpty())

            <div class="erp-empty-state">

                <div class="erp-empty-icon">
                    <i class="bi bi-briefcase"></i>
                </div>

                <h5>
                    No job assignments
                </h5>

                <p>
                    This employee has not been assigned to any jobs yet.
                </p>

            </div>

        @else

            <div class="table-responsive">

                <table class="table erp-table mb-0">

                    <thead>

                        <tr>

                            <th>
                                Job
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
                                Hours
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach(
                            $employee->jobAssignments
                            as $assignment
                        )

                            <tr>

                                <td>

                                    @if($assignment->job)

                                        <div class="fw-semibold">

                                            {{ $assignment->job->job_code ?? 'Job #' . $assignment->job->id }}

                                        </div>

                                        <div class="erp-table-meta">

                                            {{ $assignment->job->title ?? '—' }}

                                        </div>

                                    @else

                                        —

                                    @endif

                                </td>


                                <td>
                                    {{ $assignment->assignment_role ?: '—' }}
                                </td>


                                <td>

                                    <span class="badge bg-secondary">

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
                                    {{ $assignment->assigned_at?->format('M d, Y') ?: '—' }}
                                </td>


                                <td class="text-end">

                                    @if($assignment->actual_hours !== null)

                                        {{ number_format(
                                            $assignment->actual_hours,
                                            2
                                        ) }}

                                    @else

                                        —

                                    @endif

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