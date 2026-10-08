@csrf

{{-- =========================================================
     ASSIGNMENT
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Employee Assignment
    </div>

    <div class="erp-form-section-subtitle">
        Select the employee and define their responsibility.
    </div>

    <div class="row g-3">

        {{-- Employee --}}

        <div class="col-md-6">

            <label class="form-label">
                Employee
                <span class="text-danger">*</span>
            </label>

            <select
                name="employee_id"
                class="form-select @error('employee_id') is-invalid @enderror"
                required
            >

                <option value="">
                    Select employee
                </option>

                @foreach($employees as $employee)

                    <option
                        value="{{ $employee->id }}"
                        @selected(
                            old(
                                'employee_id',
                                $assignment->employee_id ?? ''
                            ) == $employee->id
                        )
                    >

                        {{ $employee->full_name }}

                        — {{ $employee->employee_no }}

                    </option>

                @endforeach

            </select>

            @error('employee_id')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- Role --}}

        <div class="col-md-6">

            <label class="form-label">
                Assignment Role
            </label>

            <input
                type="text"
                name="assignment_role"
                value="{{ old(
                    'assignment_role',
                    $assignment->assignment_role ?? ''
                ) }}"
                class="form-control @error('assignment_role') is-invalid @enderror"
                placeholder="Lead Technician"
            >

            @error('assignment_role')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- Status --}}

        <div class="col-md-4">

            <label class="form-label">
                Assignment Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                class="form-select"
                required
            >

                @foreach([
                    'assigned' => 'Assigned',
                    'accepted' => 'Accepted',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'status',
                                $assignment->status ?? 'assigned'
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Assigned At --}}

        <div class="col-md-4">

            <label class="form-label">
                Assigned At
            </label>

            <input
                type="datetime-local"
                name="assigned_at"
                value="{{ old(
                    'assigned_at',
                    isset($assignment) &&
                    $assignment?->assigned_at
                        ? $assignment->assigned_at->format(
                            'Y-m-d\TH:i'
                        )
                        : now()->format('Y-m-d\TH:i')
                ) }}"
                class="form-control"
            >

        </div>


        {{-- Primary --}}

        <div class="col-md-4">

            <label class="form-label d-block">
                Assignment Priority
            </label>

            <div class="form-check erp-checkbox-card">

                <input
                    type="checkbox"
                    name="is_primary"
                    value="1"
                    id="is_primary"
                    class="form-check-input"
                    @checked(
                        old(
                            'is_primary',
                            $assignment->is_primary ?? false
                        )
                    )
                >

                <label
                    for="is_primary"
                    class="form-check-label"
                >

                    <strong>
                        Primary Assignee
                    </strong>

                    <span>
                        Main employee responsible for the job.
                    </span>

                </label>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     SCHEDULE
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Assignment Schedule
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label class="form-label">
                Started At
            </label>

            <input
                type="datetime-local"
                name="started_at"
                value="{{ old(
                    'started_at',
                    isset($assignment) &&
                    $assignment?->started_at
                        ? $assignment->started_at->format(
                            'Y-m-d\TH:i'
                        )
                        : ''
                ) }}"
                class="form-control"
            >

        </div>


        <div class="col-md-6">

            <label class="form-label">
                Completed At
            </label>

            <input
                type="datetime-local"
                name="completed_at"
                value="{{ old(
                    'completed_at',
                    isset($assignment) &&
                    $assignment?->completed_at
                        ? $assignment->completed_at->format(
                            'Y-m-d\TH:i'
                        )
                        : ''
                ) }}"
                class="form-control"
            >

        </div>

    </div>

</div>


{{-- =========================================================
     LABOR
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Labor
    </div>

    <div class="erp-form-section-subtitle">
        Track estimated and actual employee labor.
    </div>

    <div class="row g-3">

        <div class="col-md-4">

            <label class="form-label">
                Estimated Hours
            </label>

            <input
                type="number"
                name="estimated_hours"
                min="0"
                step="0.01"
                value="{{ old(
                    'estimated_hours',
                    $assignment->estimated_hours ?? ''
                ) }}"
                class="form-control"
                placeholder="0.00"
            >

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Actual Hours
            </label>

            <input
                type="number"
                name="actual_hours"
                min="0"
                step="0.01"
                value="{{ old(
                    'actual_hours',
                    $assignment->actual_hours ?? ''
                ) }}"
                class="form-control"
                placeholder="0.00"
            >

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Hourly Rate
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    ₱
                </span>

                <input
                    type="number"
                    name="hourly_rate"
                    min="0"
                    step="0.01"
                    value="{{ old(
                        'hourly_rate',
                        $assignment->hourly_rate
                            ?? $assignment->employee->hourly_rate
                            ?? ''
                    ) }}"
                    class="form-control"
                    placeholder="0.00"
                >

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     NOTES
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Notes
    </div>

    <textarea
        name="notes"
        rows="5"
        class="form-control"
        placeholder="Assignment instructions, responsibilities, special requirements..."
    >{{ old(
        'notes',
        $assignment->notes ?? ''
    ) }}</textarea>

</div>