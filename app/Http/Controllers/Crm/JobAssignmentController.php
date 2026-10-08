<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Job;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobAssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Job $job)
    {
        $assignments = $job->assignments()
            ->with('employee')
            ->orderByDesc('is_primary')
            ->orderBy('assigned_at')
            ->get();

        return view(
            'crm.job-assignments.index',
            compact(
                'job',
                'assignments'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(Job $job)
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'crm.job-assignments.create',
            compact(
                'job',
                'employees'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Job $job
    ) {
        $validated = $this->validateAssignment(
            $request,
            $job
        );

        DB::transaction(function () use (
            $job,
            &$validated
        ) {

            if (!empty($validated['is_primary'])) {

                $job->assignments()
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $validated['job_id'] = $job->id;

            if (empty($validated['assigned_at'])) {
                $validated['assigned_at'] = now();
            }

            JobAssignment::create(
                $validated
            );
        });

        return redirect()
            ->route(
                'crm.jobs.assignments.index',
                $job
            )
            ->with(
                'success',
                'Employee assigned to the job successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Job $job,
        JobAssignment $assignment
    ) {
        $this->ensureAssignmentBelongsToJob(
            $job,
            $assignment
        );

        $employees = Employee::query()
            ->where(function ($query) use ($assignment) {
                $query
                    ->where('status', 'active')
                    ->orWhere('id', $assignment->employee_id);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'crm.job-assignments.edit',
            compact(
                'job',
                'assignment',
                'employees'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Job $job,
        JobAssignment $assignment
    ) {
        $this->ensureAssignmentBelongsToJob(
            $job,
            $assignment
        );

        $validated = $this->validateAssignment(
            $request,
            $job,
            $assignment
        );

        DB::transaction(function () use (
            $job,
            $assignment,
            &$validated
        ) {

            if (!empty($validated['is_primary'])) {

                $job->assignments()
                    ->where(
                        'id',
                        '!=',
                        $assignment->id
                    )
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $assignment->update(
                $validated
            );
        });

        return redirect()
            ->route(
                'crm.jobs.assignments.index',
                $job
            )
            ->with(
                'success',
                'Job assignment updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Job $job,
        JobAssignment $assignment
    ) {
        $this->ensureAssignmentBelongsToJob(
            $job,
            $assignment
        );

        $assignment->delete();

        return redirect()
            ->route(
                'crm.jobs.assignments.index',
                $job
            )
            ->with(
                'success',
                'Job assignment removed successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Make Primary
    |--------------------------------------------------------------------------
    */

    public function makePrimary(
        Job $job,
        JobAssignment $assignment
    ) {
        $this->ensureAssignmentBelongsToJob(
            $job,
            $assignment
        );

        DB::transaction(function () use (
            $job,
            $assignment
        ) {

            $job->assignments()
                ->update([
                    'is_primary' => false,
                ]);

            $assignment->update([
                'is_primary' => true,
            ]);
        });

        return back()->with(
            'success',
            'Primary assignee updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateAssignment(
        Request $request,
        Job $job,
        ?JobAssignment $assignment = null
    ): array {

        $employeeUniqueRule = Rule::unique(
            'job_assignments',
            'employee_id'
        )
            ->where(function ($query) use ($job) {

                $query
                    ->where(
                        'job_id',
                        $job->id
                    )
                    ->whereIn(
                        'status',
                        [
                            'assigned',
                            'accepted',
                            'in_progress',
                        ]
                    );
            });

        if ($assignment) {
            $employeeUniqueRule->ignore(
                $assignment
            );
        }

        return $request->validate([

            'employee_id' => [
                'required',
                'exists:employees,id',
                $employeeUniqueRule,
            ],

            'assignment_role' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_primary' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'required',
                Rule::in([
                    'assigned',
                    'accepted',
                    'in_progress',
                    'completed',
                    'cancelled',
                ]),
            ],

            'assigned_at' => [
                'nullable',
                'date',
            ],

            'started_at' => [
                'nullable',
                'date',
            ],

            'completed_at' => [
                'nullable',
                'date',
            ],

            'estimated_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'actual_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'hourly_rate' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization / Ownership Guard
    |--------------------------------------------------------------------------
    */

    private function ensureAssignmentBelongsToJob(
        Job $job,
        JobAssignment $assignment
    ): void {
        abort_unless(
            $assignment->job_id === $job->id,
            404
        );
    }
}