@extends('layouts.admin')

@section('title', 'Schedule Details')

@section('page-title', 'Schedule Details')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Schedule Details
            </h1>

            <p class="page-subtitle">
                Job scheduling information.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route(
                    'crm.job-schedules.edit',
                    $jobSchedule
                ) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route(
                    'crm.job-schedules.index'
                ) }}"
                class="btn btn-outline-secondary"
            >
                Back
            </a>

        </div>

    </div>

</div>


<div class="row g-4">

    <div class="col-lg-8">

        <div class="card erp-card">

            <div class="card-header">

                <div class="card-title">
                    Schedule Information
                </div>

            </div>

            <div class="card-body">

                <div class="details-grid">

                    <div class="detail-item">

                        <div class="detail-label">
                            Job
                        </div>

                        <div class="detail-value">
                            {{
                                $jobSchedule->job->title
                                ?? $jobSchedule->job->name
                                ?? $jobSchedule->job->job_name
                                ?? 'Job #' . $jobSchedule->job_id
                            }}
                        </div>

                    </div>


                    <div class="detail-item">

                        <div class="detail-label">
                            Employee
                        </div>

                        <div class="detail-value">
                            {{
                                $jobSchedule->employee->full_name
                                ?? '—'
                            }}
                        </div>

                    </div>


                    <div class="detail-item">

                        <div class="detail-label">
                            Date
                        </div>

                        <div class="detail-value">
                            {{
                                $jobSchedule->scheduled_date
                                    ->format('F d, Y')
                            }}
                        </div>

                    </div>


                    <div class="detail-item">

                        <div class="detail-label">
                            Time
                        </div>

                        <div class="detail-value">
                            {{ $jobSchedule->time_range }}
                        </div>

                    </div>


                    <div class="detail-item">

                        <div class="detail-label">
                            Status
                        </div>

                        <div class="detail-value">
                            {{ $jobSchedule->status_label }}
                        </div>

                    </div>


                    <div class="detail-item">

                        <div class="detail-label">
                            Location
                        </div>

                        <div class="detail-value">
                            {{ $jobSchedule->location ?? '—' }}
                        </div>

                    </div>


                    <div class="detail-item detail-full">

                        <div class="detail-label">
                            Notes
                        </div>

                        <div class="detail-value">
                            {!! nl2br(
                                e($jobSchedule->notes ?? '—')
                            ) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="card erp-card">

            <div class="card-header">

                <div class="card-title">
                    Actions
                </div>

            </div>

            <div class="card-body">

                <div class="d-grid gap-2">

                    <a
                        href="{{ route(
                            'crm.job-schedules.edit',
                            $jobSchedule
                        ) }}"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-pencil me-1"></i>
                        Edit Schedule
                    </a>


                    <a
                        href="{{ route(
                            'crm.job-schedules.calendar'
                        ) }}"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-calendar3 me-1"></i>
                        View Calendar
                    </a>


                    <form
                        method="POST"
                        action="{{ route(
                            'crm.job-schedules.destroy',
                            $jobSchedule
                        ) }}"
                        onsubmit="
                            return confirm(
                                'Delete this schedule?'
                            );
                        "
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-outline-danger w-100"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Delete Schedule
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

    .details-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 0;
    }

    .detail-item {
        padding: 18px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .detail-item:nth-child(odd) {
        padding-right: 25px;
    }

    .detail-item:nth-child(even) {
        padding-left: 25px;
        border-left: 1px solid #eef2f7;
    }

    .detail-full {
        grid-column: 1 / -1;
        padding-right: 0 !important;
        padding-left: 0 !important;
        border-left: 0 !important;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 15px;
        color: #1e293b;
    }

    @media (max-width: 768px) {

        .details-grid {
            grid-template-columns: 1fr;
        }

        .detail-item:nth-child(even) {
            padding-left: 0;
            border-left: 0;
        }

        .detail-item:nth-child(odd) {
            padding-right: 0;
        }

    }

</style>

@endpush