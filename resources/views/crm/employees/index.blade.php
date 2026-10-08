@extends('layouts.admin')

@section('title', 'Employees')

@section('page-title', 'Employees')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Employees
            </h1>

            <p class="page-subtitle">
                Manage employees and field personnel.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.employees.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-person-plus"></i>
                Add Employee
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


{{-- Filters --}}

<div class="erp-card mb-4">

    <div class="erp-card-body">

        <form
            method="GET"
            action="{{ route('crm.employees.index') }}"
        >

            <div class="row g-3">

                <div class="col-md-5">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Employee no., name, email, position..."
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Department
                    </label>

                    <select
                        name="department"
                        class="form-select"
                    >

                        <option value="">
                            All Departments
                        </option>

                        @foreach($departments as $department)

                            <option
                                value="{{ $department }}"
                                @selected(
                                    request('department') === $department
                                )
                            >
                                {{ $department }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2">

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
                            value="active"
                            @selected(request('status') === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected(request('status') === 'inactive')
                        >
                            Inactive
                        </option>

                        <option
                            value="on_leave"
                            @selected(request('status') === 'on_leave')
                        >
                            On Leave
                        </option>

                        <option
                            value="terminated"
                            @selected(request('status') === 'terminated')
                        >
                            Terminated
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <div class="d-flex gap-2 w-100">

                        <button
                            type="submit"
                            class="btn btn-primary flex-grow-1"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('crm.employees.index') }}"
                            class="btn btn-light"
                        >
                            <i class="bi bi-x-lg"></i>
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- Employee Table --}}

<div class="erp-card">

    <div class="erp-card-header">

        <div>

            <div class="erp-card-title">
                Employee Directory
            </div>

            <div class="erp-card-subtitle">
                {{ $employees->total() }} employee(s)
            </div>

        </div>

    </div>

    <div class="erp-card-body p-0">

        @if($employees->isEmpty())

            <div class="erp-empty-state">

                <div class="erp-empty-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h5>
                    No employees found
                </h5>

                <p>
                    Add your first employee to start assigning jobs.
                </p>

                <a
                    href="{{ route('crm.employees.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-person-plus"></i>
                    Add Employee
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
                                Position
                            </th>

                            <th>
                                Department
                            </th>

                            <th>
                                Employment
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

                        @foreach($employees as $employee)

                            <tr>

                                <td>

                                    <div class="erp-employee-cell">

                                        <div class="erp-avatar">
                                            {{ $employee->initials }}
                                        </div>

                                        <div>

                                            <a
                                                href="{{ route(
                                                    'crm.employees.show',
                                                    $employee
                                                ) }}"
                                                class="erp-employee-name text-decoration-none"
                                            >
                                                {{ $employee->full_name }}
                                            </a>

                                            <div class="erp-table-meta">

                                                {{ $employee->employee_no }}

                                                @if($employee->email)
                                                    · {{ $employee->email }}
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    {{ $employee->position ?: '—' }}
                                </td>


                                <td>
                                    {{ $employee->department ?: '—' }}
                                </td>


                                <td>

                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $employee->employment_type
                                        )
                                    ) }}

                                </td>


                                <td>

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

                                </td>


                                <td class="text-end">

                                    <div class="dropdown">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light"
                                            data-bs-toggle="dropdown"
                                        >
                                            <i class="bi bi-three-dots"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>

                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route(
                                                        'crm.employees.show',
                                                        $employee
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
                                                        'crm.employees.edit',
                                                        $employee
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
                                                        'crm.employees.toggle-status',
                                                        $employee
                                                    ) }}"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item"
                                                    >

                                                        @if($employee->status === 'active')

                                                            <i class="bi bi-person-dash me-2"></i>
                                                            Deactivate

                                                        @else

                                                            <i class="bi bi-person-check me-2"></i>
                                                            Activate

                                                        @endif

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

            <div class="erp-pagination">
                {{ $employees->links() }}
            </div>

        @endif

    </div>

</div>

@endsection