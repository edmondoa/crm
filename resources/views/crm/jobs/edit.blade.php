@extends('layouts.admin')

@section('title', 'Edit Work Order')

@section('page-title', 'Edit Work Order')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Work Order
            </h1>

            <p class="page-subtitle">
                {{ $job->job_code }}
            </p>

        </div>

        <div>

            <a
                href="{{ route(
                    'crm.jobs.show',
                    $job
                ) }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Work Order
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