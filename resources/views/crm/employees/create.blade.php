@extends('layouts.admin')

@section('title', 'Create Employee')

@section('page-title', 'Create Employee')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="page-breadcrumb">

                <a href="{{ route('crm.employees.index') }}">
                    Employees
                </a>

                <span>/</span>

                <span>Create</span>

            </div>

            <h1 class="page-title">
                Create Employee
            </h1>

            <p class="page-subtitle">
                Add a new employee to the CRM.
            </p>

        </div>

    </div>

</div>


<div class="erp-card">

    <div class="erp-card-body">

        <form
            method="POST"
            action="{{ route('crm.employees.store') }}"
        >

            @include(
                'crm.employees._form',
                ['employee' => null]
            )

            <div class="erp-form-actions">

                <a
                    href="{{ route('crm.employees.index') }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-person-plus"></i>
                    Create Employee
                </button>

            </div>

        </form>

    </div>

</div>

@endsection