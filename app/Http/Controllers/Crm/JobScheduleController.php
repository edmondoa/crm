<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\JobSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    public function calendar(Request $request): View
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $jobs = Job::query()
            ->orderBy('id', 'desc')
            ->get();

        return view('crm.job-schedules.calendar', [
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar Events
    |--------------------------------------------------------------------------
    */

    public function events(Request $request): JsonResponse
    {
        $query = JobSchedule::query()
            ->with([
                'job',
                'employee',
                'jobAssignment',
            ]);

        if ($request->filled('start')) {
            $query->whereDate(
                'scheduled_date',
                '>=',
                Carbon::parse($request->start)
            );
        }

        if ($request->filled('end')) {
            $query->whereDate(
                'scheduled_date',
                '<=',
                Carbon::parse($request->end)
            );
        }

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('job_id')) {
            $query->where(
                'job_id',
                $request->job_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $schedules = $query
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        $events = $schedules->map(function (JobSchedule $schedule) {

            $start = Carbon::parse(
                $schedule->scheduled_date->format('Y-m-d')
                . ' '
                . $schedule->start_time
            );

            $end = Carbon::parse(
                $schedule->scheduled_date->format('Y-m-d')
                . ' '
                . $schedule->end_time
            );

            $employeeName = $schedule->employee
                ? $schedule->employee->full_name
                : 'Unassigned';

            $jobName = $this->jobName($schedule->job);

            return [
                'id' => $schedule->id,

                'title' => $jobName
                    . ' • '
                    . $employeeName,

                'start' => $start->toIso8601String(),

                'end' => $end->toIso8601String(),

                'allDay' => false,

                'extendedProps' => [
                    'job_id' => $schedule->job_id,
                    'job_assignment_id' =>
                        $schedule->job_assignment_id,
                    'employee_id' =>
                        $schedule->employee_id,
                    'employee' => $employeeName,
                    'job' => $jobName,
                    'status' => $schedule->status,
                    'status_label' =>
                        $schedule->status_label,
                    'location' => $schedule->location,
                    'notes' => $schedule->notes,
                    'time_range' =>
                        $schedule->time_range,
                ],
            ];
        });

        return response()->json($events);
    }

    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $query = JobSchedule::query()
            ->with([
                'job',
                'employee',
                'jobAssignment',
            ]);

        if ($request->filled('date_from')) {
            $query->whereDate(
                'scheduled_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'scheduled_date',
                '<=',
                $request->date_to
            );
        }

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('job_id')) {
            $query->where(
                'job_id',
                $request->job_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $schedules = $query
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $jobs = Job::query()
            ->orderBy('id', 'desc')
            ->get();

        return view('crm.job-schedules.index', [
            'schedules' => $schedules,
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): View
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $jobs = Job::query()
            ->orderBy('id', 'desc')
            ->get();

        $assignments = JobAssignment::query()
            ->with([
                'employee',
                'job',
            ])
            ->whereIn('status', [
                'assigned',
                'accepted',
                'in_progress',
            ])
            ->get();

        return view('crm.job-schedules.create', [
            'employees' => $employees,
            'jobs' => $jobs,
            'assignments' => $assignments,
            'selectedDate' => $request->date,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_id' => [
                'required',
                'exists:jobs,id',
            ],

            'job_assignment_id' => [
                'nullable',
                'exists:job_assignments,id',
            ],

            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'scheduled_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'status' => [
                'required',
                'in:scheduled,confirmed,in_progress,completed,cancelled',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate assignment belongs to selected employee/job
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['job_assignment_id'])) {

            $assignment = JobAssignment::query()
                ->whereKey($validated['job_assignment_id'])
                ->firstOrFail();

            if (
                $assignment->job_id != $validated['job_id']
                ||
                $assignment->employee_id != $validated['employee_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'job_assignment_id' =>
                            'The selected job assignment does not match the selected job and employee.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Employee conflict
        |--------------------------------------------------------------------------
        */

        if ($this->hasConflict(
            $validated['employee_id'],
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'The employee already has another schedule during this time.',
                ]);
        }

        JobSchedule::create($validated);

        return redirect()
            ->route('crm.job-schedules.index')
            ->with(
                'success',
                'Job schedule created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(JobSchedule $jobSchedule): View
    {
        $jobSchedule->load([
            'job',
            'employee',
            'jobAssignment',
        ]);

        return view(
            'crm.job-schedules.show',
            compact('jobSchedule')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(JobSchedule $jobSchedule): View
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $jobs = Job::query()
            ->orderBy('id', 'desc')
            ->get();

        $assignments = JobAssignment::query()
            ->with([
                'employee',
                'job',
            ])
            ->whereIn('status', [
                'assigned',
                'accepted',
                'in_progress',
            ])
            ->orWhere('id', $jobSchedule->job_assignment_id)
            ->get();

        return view('crm.job-schedules.edit', [
            'jobSchedule' => $jobSchedule,
            'employees' => $employees,
            'jobs' => $jobs,
            'assignments' => $assignments,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        JobSchedule $jobSchedule
    ): RedirectResponse {

        $validated = $request->validate([
            'job_id' => [
                'required',
                'exists:jobs,id',
            ],

            'job_assignment_id' => [
                'nullable',
                'exists:job_assignments,id',
            ],

            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'scheduled_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'status' => [
                'required',
                'in:scheduled,confirmed,in_progress,completed,cancelled',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate assignment
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['job_assignment_id'])) {

            $assignment = JobAssignment::findOrFail(
                $validated['job_assignment_id']
            );

            if (
                $assignment->job_id != $validated['job_id']
                ||
                $assignment->employee_id != $validated['employee_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'job_assignment_id' =>
                            'The selected assignment does not match the selected job and employee.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Conflict check
        |--------------------------------------------------------------------------
        */

        if ($this->hasConflict(
            $validated['employee_id'],
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time'],
            $jobSchedule->id
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'The employee already has another schedule during this time.',
                ]);
        }

        $jobSchedule->update($validated);

        return redirect()
            ->route('crm.job-schedules.index')
            ->with(
                'success',
                'Job schedule updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        JobSchedule $jobSchedule
    ): RedirectResponse {

        $jobSchedule->delete();

        return redirect()
            ->route('crm.job-schedules.index')
            ->with(
                'success',
                'Job schedule deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment Employees
    |--------------------------------------------------------------------------
    */

    public function assignmentEmployees(
        Request $request
    ): JsonResponse {

        $request->validate([
            'job_id' => [
                'required',
                'exists:jobs,id',
            ],
        ]);

        $assignments = JobAssignment::query()
            ->with('employee')
            ->where('job_id', $request->job_id)
            ->whereIn('status', [
                'assigned',
                'accepted',
                'in_progress',
            ])
            ->get();

        return response()->json(
            $assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'employee_id' => $assignment->employee_id,
                    'employee' => $assignment->employee
                        ? $assignment->employee->full_name
                        : null,
                    'role' => $assignment->assignment_role,
                    'status' => $assignment->status,
                ];
            })
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function hasConflict(
        int $employeeId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): bool {

        return JobSchedule::query()
            ->where('employee_id', $employeeId)
            ->whereDate('scheduled_date', $date)
            ->whereIn('status', [
                'scheduled',
                'confirmed',
                'in_progress',
            ])
            ->when(
                $ignoreId,
                fn ($query) =>
                    $query->where('id', '!=', $ignoreId)
            )
            ->where(function ($query) use (
                $startTime,
                $endTime
            ) {
                $query
                    ->where(
                        'start_time',
                        '<',
                        $endTime
                    )
                    ->where(
                        'end_time',
                        '>',
                        $startTime
                    );
            })
            ->exists();
    }

    protected function jobName($job): string
    {
        if (!$job) {
            return 'Job';
        }

        return $job->title
            ?? $job->name
            ?? $job->job_name
            ?? ('Job #' . $job->id);
    }
}