@extends('layouts.admin')

@section('title', 'Edit Job Schedule')

@section('page-title', 'Edit Job Schedule')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Job Schedule
            </h1>

            <p class="page-subtitle">
                Update schedule details and assignment.
            </p>

        </div>

        <div>

            <a
                href="{{ route(
                    'crm.job-schedules.show',
                    $jobSchedule
                ) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please correct the following:
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


<form
    method="POST"
    action="{{ route(
        'crm.job-schedules.update',
        $jobSchedule
    ) }}"
>

    @csrf

    @method('PUT')

    <div class="card erp-card">

        <div class="card-header">

            <div class="card-title">
                Schedule Information
            </div>

        </div>

        <div class="card-body">

            @include(
                'crm.job-schedules._form',
                [
                    'jobSchedule' => $jobSchedule
                ]
            )

        </div>


        <div class="card-footer d-flex justify-content-end gap-2">

            <a
                href="{{ route(
                    'crm.job-schedules.show',
                    $jobSchedule
                ) }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-save me-1"></i>
                Save Changes
            </button>

        </div>

    </div>

</form>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const jobSelect =
        document.getElementById('job_id');

    const employeeSelect =
        document.getElementById('employee_id');

    const assignmentSelect =
        document.getElementById('job_assignment_id');


    function filterAssignments() {

        const jobId =
            jobSelect.value;

        const employeeId =
            employeeSelect.value;

        Array.from(
            assignmentSelect.options
        ).forEach(function (option) {

            if (!option.value) {
                return;
            }

            const matchesJob =
                !jobId
                ||
                option.dataset.job == jobId;

            const matchesEmployee =
                !employeeId
                ||
                option.dataset.employee == employeeId;

            option.hidden =
                !(matchesJob && matchesEmployee);

        });

    }


    jobSelect.addEventListener(
        'change',
        filterAssignments
    );

    employeeSelect.addEventListener(
        'change',
        filterAssignments
    );

    filterAssignments();

});
</script>

@endpush