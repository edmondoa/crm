@extends('layouts.admin')

@section('title', 'Assign Employee')

@section('page-title', 'Assign Employee')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <div class="page-breadcrumb">

                <a href="{{ route('crm.jobs.index') }}">
                    Jobs
                </a>

                <span>/</span>

                <a href="{{ route('crm.jobs.show', $job) }}">
                    {{ $job->job_code ?? 'Job #' . $job->id }}
                </a>

                <span>/</span>

                <span>
                    Assign Employee
                </span>

            </div>

            <h1 class="page-title">
                Assign Employee
            </h1>

            <p class="page-subtitle">
                Assign an employee to this job.
            </p>

        </div>

    </div>

</div>


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


    <div class="erp-card-body">

        <form
            method="POST"
            action="{{ route(
                'crm.jobs.assignments.store',
                $job
            ) }}"
        >

            @include(
                'crm.job-assignments._form',
                ['assignment' => null]
            )

            <div class="erp-form-actions">

                <a
                    href="{{ route(
                        'crm.jobs.assignments.index',
                        $job
                    ) }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-person-plus"></i>
                    Assign Employee
                </button>

            </div>

        </form>

    </div>

</div>

@endsection