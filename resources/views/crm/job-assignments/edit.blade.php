@extends('layouts.admin')

@section('title', 'Edit Job Assignment')

@section('page-title', 'Edit Job Assignment')

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
                    Edit Assignment
                </span>

            </div>

            <h1 class="page-title">
                Edit Job Assignment
            </h1>

            <p class="page-subtitle">
                Update the employee's assignment.
            </p>

        </div>

    </div>

</div>


<div class="erp-card">

    <div class="erp-card-body">

        <form
            method="POST"
            action="{{ route(
                'crm.jobs.assignments.update',
                [
                    $job,
                    $assignment
                ]
            ) }}"
        >

            @method('PUT')

            @include(
                'crm.job-assignments._form',
                ['assignment' => $assignment]
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
                    <i class="bi bi-check-lg"></i>
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection