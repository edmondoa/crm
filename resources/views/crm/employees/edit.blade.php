@extends('layouts.admin')

@section('title', 'Edit Employee')

@section('page-title', 'Edit Employee')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="page-breadcrumb">

                <a href="{{ route('crm.employees.index') }}">
                    Employees
                </a>

                <span>/</span>

                <a href="{{ route('crm.employees.show', $employee) }}">
                    {{ $employee->full_name }}
                </a>

                <span>/</span>

                <span>Edit</span>

            </div>

            <h1 class="page-title">
                Edit Employee
            </h1>

            <p class="page-subtitle">
                Update employee information.
            </p>

        </div>

    </div>

</div>


<div class="erp-card">

    <div class="erp-card-body">

        <form
            method="POST"
            action="{{ route(
                'crm.employees.update',
                $employee
            ) }}"
        >

            @method('PUT')

            @include(
                'crm.employees._form',
                ['employee' => $employee]
            )

            <div class="erp-form-actions">

                <a
                    href="{{ route(
                        'crm.employees.show',
                        $employee
                    ) }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection