<div class="row g-4">

    {{-- Job --}}
    <div class="col-md-6">

        <label
            for="job_id"
            class="form-label required"
        >
            Job
        </label>

        <select
            name="job_id"
            id="job_id"
            class="form-select @error('job_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Job
            </option>

            @foreach($jobs as $job)

                <option
                    value="{{ $job->id }}"
                    @selected(
                        old(
                            'job_id',
                            $jobSchedule->job_id ?? ''
                        ) == $job->id
                    )
                >
                    {{
                        $job->title
                        ?? $job->name
                        ?? $job->job_name
                        ?? 'Job #' . $job->id
                    }}
                </option>

            @endforeach

        </select>

        @error('job_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Employee --}}
    <div class="col-md-6">

        <label
            for="employee_id"
            class="form-label required"
        >
            Employee
        </label>

        <select
            name="employee_id"
            id="employee_id"
            class="form-select @error('employee_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Employee
            </option>

            @foreach($employees as $employee)

                <option
                    value="{{ $employee->id }}"
                    @selected(
                        old(
                            'employee_id',
                            $jobSchedule->employee_id ?? ''
                        ) == $employee->id
                    )
                >
                    {{ $employee->full_name }}
                </option>

            @endforeach

        </select>

        @error('employee_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Job Assignment --}}
    <div class="col-md-6">

        <label
            for="job_assignment_id"
            class="form-label"
        >
            Job Assignment
        </label>

        <select
            name="job_assignment_id"
            id="job_assignment_id"
            class="form-select @error('job_assignment_id') is-invalid @enderror"
        >

            <option value="">
                Select Assignment
            </option>

            @foreach($assignments as $assignment)

                <option
                    value="{{ $assignment->id }}"
                    data-job="{{ $assignment->job_id }}"
                    data-employee="{{ $assignment->employee_id }}"
                    @selected(
                        old(
                            'job_assignment_id',
                            $jobSchedule->job_assignment_id ?? ''
                        ) == $assignment->id
                    )
                >

                    {{ $assignment->employee->full_name ?? 'Employee' }}

                    —

                    {{ $assignment->assignment_role }}

                </option>

            @endforeach

        </select>

        @error('job_assignment_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Date --}}
    <div class="col-md-6">

        <label
            for="scheduled_date"
            class="form-label required"
        >
            Scheduled Date
        </label>

        <input
            type="date"
            name="scheduled_date"
            id="scheduled_date"
            value="{{ old(
                'scheduled_date',
                isset($jobSchedule)
                    ? $jobSchedule->scheduled_date->format('Y-m-d')
                    : ($selectedDate ?? now()->format('Y-m-d'))
            ) }}"
            class="form-control @error('scheduled_date') is-invalid @enderror"
            required
        >

        @error('scheduled_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Start Time --}}
    <div class="col-md-6">

        <label
            for="start_time"
            class="form-label required"
        >
            Start Time
        </label>

        <input
            type="time"
            name="start_time"
            id="start_time"
            value="{{ old(
                'start_time',
                $jobSchedule->start_time ?? '08:00'
            ) }}"
            class="form-control @error('start_time') is-invalid @enderror"
            required
        >

        @error('start_time')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- End Time --}}
    <div class="col-md-6">

        <label
            for="end_time"
            class="form-label required"
        >
            End Time
        </label>

        <input
            type="time"
            name="end_time"
            id="end_time"
            value="{{ old(
                'end_time',
                $jobSchedule->end_time ?? '17:00'
            ) }}"
            class="form-control @error('end_time') is-invalid @enderror"
            required
        >

        @error('end_time')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Status --}}
    <div class="col-md-6">

        <label
            for="status"
            class="form-label required"
        >
            Status
        </label>

        <select
            name="status"
            id="status"
            class="form-select"
            required
        >

            @foreach([
                'scheduled' => 'Scheduled',
                'confirmed' => 'Confirmed',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'status',
                            $jobSchedule->status ?? 'scheduled'
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>


    {{-- Location --}}
    <div class="col-md-6">

        <label
            for="location"
            class="form-label"
        >
            Location
        </label>

        <input
            type="text"
            name="location"
            id="location"
            value="{{ old(
                'location',
                $jobSchedule->location ?? ''
            ) }}"
            class="form-control"
            placeholder="Job site / service location"
        >

    </div>


    {{-- Notes --}}
    <div class="col-12">

        <label
            for="notes"
            class="form-label"
        >
            Notes
        </label>

        <textarea
            name="notes"
            id="notes"
            rows="4"
            class="form-control"
            placeholder="Schedule notes, instructions, or special requirements"
        >{{ old(
            'notes',
            $jobSchedule->notes ?? ''
        ) }}</textarea>

    </div>

</div>