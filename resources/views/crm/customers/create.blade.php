@extends('layouts.admin')

@section('title', 'Add Customer')

@section('page-title', 'Add Customer')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Add Customer
            </h1>

            <p class="page-subtitle">
                Create a new CRM customer record.
            </p>

        </div>


        <div class="page-header-actions">

            <a
                href="{{ route('crm.customers.index') }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Customers
            </a>

        </div>

    </div>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <div class="fw-semibold mb-1">
            Please correct the following errors:
        </div>

        <ul class="mb-0">

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('crm.customers.store') }}"
        >

            @csrf

            @php
                $customer = null;
            @endphp

            @include(
                'crm.customers._form',
                ['customer' => $customer]
            )


            <div class="form-actions">

                <a
                    href="{{ route('crm.customers.index') }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Save Customer
                </button>

            </div>

        </form>

    </div>

</div>

@endsection