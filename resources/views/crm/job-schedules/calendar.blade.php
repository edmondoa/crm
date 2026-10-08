@extends('layouts.admin')

@section('title', 'Job Scheduling')

@section('page-title', 'Job Scheduling')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Job Scheduling
            </h1>

            <p class="page-subtitle">
                Schedule assigned employees and manage job appointments.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.job-schedules.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-list-ul me-1"></i>
                List View
            </a>

            <a
                href="{{ route('crm.job-schedules.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                New Schedule
            </a>

        </div>

    </div>

</div>


<div class="card erp-card">

    <div class="card-header">

        <div class="schedule-toolbar">

            <div class="schedule-filter">

                <label
                    for="calendarEmployee"
                    class="form-label"
                >
                    Employee
                </label>

                <select
                    id="calendarEmployee"
                    class="form-select"
                >
                    <option value="">
                        All Employees
                    </option>

                    @foreach($employees as $employee)

                        <option value="{{ $employee->id }}">
                            {{ $employee->full_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="schedule-filter">

                <label
                    for="calendarJob"
                    class="form-label"
                >
                    Job
                </label>

                <select
                    id="calendarJob"
                    class="form-select"
                >
                    <option value="">
                        All Jobs
                    </option>

                    @foreach($jobs as $job)

                        <option value="{{ $job->id }}">
                            {{ $job->title
                                ?? $job->name
                                ?? $job->job_name
                                ?? 'Job #' . $job->id }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="schedule-filter">

                <label
                    for="calendarStatus"
                    class="form-label"
                >
                    Status
                </label>

                <select
                    id="calendarStatus"
                    class="form-select"
                >
                    <option value="">
                        All Statuses
                    </option>

                    <option value="scheduled">
                        Scheduled
                    </option>

                    <option value="confirmed">
                        Confirmed
                    </option>

                    <option value="in_progress">
                        In Progress
                    </option>

                    <option value="completed">
                        Completed
                    </option>

                    <option value="cancelled">
                        Cancelled
                    </option>

                </select>

            </div>

        </div>

    </div>


    <div class="card-body">

        <div
            id="jobCalendar"
            class="job-calendar"
        ></div>

    </div>

</div>


{{-- Event Details Modal --}}
<div
    class="modal fade modal-backdrop"
    id="scheduleDetailsModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Schedule Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">

                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Job
                    </div>

                    <div
                        id="detailJob"
                        class="schedule-detail-value"
                    ></div>

                </div>


                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Employee
                    </div>

                    <div
                        id="detailEmployee"
                        class="schedule-detail-value"
                    ></div>

                </div>


                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Schedule
                    </div>

                    <div
                        id="detailTime"
                        class="schedule-detail-value"
                    ></div>

                </div>


                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Status
                    </div>

                    <div
                        id="detailStatus"
                        class="schedule-detail-value"
                    ></div>

                </div>


                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Location
                    </div>

                    <div
                        id="detailLocation"
                        class="schedule-detail-value"
                    ></div>

                </div>


                <div class="schedule-detail">

                    <div class="schedule-detail-label">
                        Notes
                    </div>

                    <div
                        id="detailNotes"
                        class="schedule-detail-value"
                    ></div>

                </div>

            </div>

            <div class="modal-footer">

                <a
                    href="#"
                    id="editScheduleButton"
                    class="btn btn-primary"
                >
                    <i class="bi bi-pencil me-1"></i>
                    Edit
                </a>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')



<style>
    .modal-backdrop {
        display: none !important;
    }
    .schedule-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: end;
    }

    .schedule-filter {
        min-width: 220px;
    }

    .schedule-filter .form-label {
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .job-calendar {
        min-height: 700px;
    }

    .fc {
        font-family: inherit;
    }

    .fc .fc-toolbar-title {
        font-size: 20px;
        font-weight: 700;
    }

    .fc .fc-button {
        border-radius: 6px;
        font-size: 13px;
    }

    .fc-event {
        border: 0;
        padding: 3px 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .schedule-detail {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 8px 15px;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .schedule-detail:last-child {
        border-bottom: 0;
    }

    .schedule-detail-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .schedule-detail-value {
        color: #1e293b;
        font-size: 14px;
    }

    @media (max-width: 768px) {

        .schedule-toolbar {
            display: block;
        }

        .schedule-filter {
            margin-bottom: 12px;
            width: 100%;
        }

        .job-calendar {
            min-height: 600px;
        }

    }
</style>

@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarElement =
        document.getElementById('jobCalendar');

    const employeeFilter =
        document.getElementById('calendarEmployee');

    const jobFilter =
        document.getElementById('calendarJob');

    const statusFilter =
        document.getElementById('calendarStatus');

    const calendar = new FullCalendar.Calendar(
        calendarElement,
        {

            initialView: 'dayGridMonth',

            height: 'auto',

            expandRows: true,

            nowIndicator: true,

            selectable: false,

            editable: false,

            dayMaxEvents: true,

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right:
                    'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            },

            events: function (
                fetchInfo,
                successCallback,
                failureCallback
            ) {

                const params = new URLSearchParams();

                params.append(
                    'start',
                    fetchInfo.startStr
                );

                params.append(
                    'end',
                    fetchInfo.endStr
                );

                if (employeeFilter.value) {
                    params.append(
                        'employee_id',
                        employeeFilter.value
                    );
                }

                if (jobFilter.value) {
                    params.append(
                        'job_id',
                        jobFilter.value
                    );
                }

                if (statusFilter.value) {
                    params.append(
                        'status',
                        statusFilter.value
                    );
                }

                fetch(
                    '{{ route('crm.job-schedules.events') }}'
                    + '?'
                    + params.toString()
                )
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error(error);
                    failureCallback(error);
                });
            },

            eventClick: function (info) {

                const props =
                    info.event.extendedProps;

                document.getElementById(
                    'detailJob'
                ).textContent =
                    props.job ?? '-';

                document.getElementById(
                    'detailEmployee'
                ).textContent =
                    props.employee ?? '-';

                document.getElementById(
                    'detailTime'
                ).textContent =
                    props.time_range ?? '-';

                document.getElementById(
                    'detailStatus'
                ).textContent =
                    props.status_label ?? '-';

                document.getElementById(
                    'detailLocation'
                ).textContent =
                    props.location ?? '-';

                document.getElementById(
                    'detailNotes'
                ).textContent =
                    props.notes ?? '-';

                document.getElementById(
                    'editScheduleButton'
                ).href =
                    '{{ url('/crm/job-schedules') }}'
                    + '/'
                    + info.event.id
                    + '/edit';

                const modal =
                    bootstrap.Modal.getOrCreateInstance(
                        document.getElementById(
                            'scheduleDetailsModal'
                        )
                    );

                modal.show();
            }

        }
    );

    calendar.render();


    employeeFilter.addEventListener(
        'change',
        function () {
            calendar.refetchEvents();
        }
    );

    jobFilter.addEventListener(
        'change',
        function () {
            calendar.refetchEvents();
        }
    );

    statusFilter.addEventListener(
        'change',
        function () {
            calendar.refetchEvents();
        }
    );

});
</script>

@endpush