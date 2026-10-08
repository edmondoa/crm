@extends('layouts.admin')

@section('title', 'Create Work Order')

@section('page-title', 'Create Work Order')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Create Work Order
            </h1>

            <p class="page-subtitle">
                Create a new job and define the work
                that needs to be performed.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.jobs.index') }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Jobs
            </a>

        </div>

    </div>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please correct the following errors:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


@include('crm.jobs._form')

@endsection