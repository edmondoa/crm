@csrf

{{-- =========================================================
     PERSONAL INFORMATION
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Personal Information
    </div>

    <div class="erp-form-section-subtitle">
        Basic employee identification and personal information.
    </div>

    <div class="row g-3">

        {{-- Employee No --}}

        <div class="col-md-4">

            <label class="form-label">
                Employee No.
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="employee_no"
                value="{{ old(
                    'employee_no',
                    $employee->employee_no ?? ''
                ) }}"
                class="form-control @error('employee_no') is-invalid @enderror"
                placeholder="EMP-0001"
                required
            >

            @error('employee_no')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- First Name --}}

        <div class="col-md-4">

            <label class="form-label">
                First Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="first_name"
                value="{{ old(
                    'first_name',
                    $employee->first_name ?? ''
                ) }}"
                class="form-control @error('first_name') is-invalid @enderror"
                required
            >

            @error('first_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Middle Name --}}

        <div class="col-md-4">

            <label class="form-label">
                Middle Name
            </label>

            <input
                type="text"
                name="middle_name"
                value="{{ old(
                    'middle_name',
                    $employee->middle_name ?? ''
                ) }}"
                class="form-control @error('middle_name') is-invalid @enderror"
            >

            @error('middle_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Last Name --}}

        <div class="col-md-8">

            <label class="form-label">
                Last Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="last_name"
                value="{{ old(
                    'last_name',
                    $employee->last_name ?? ''
                ) }}"
                class="form-control @error('last_name') is-invalid @enderror"
                required
            >

            @error('last_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Suffix --}}

        <div class="col-md-4">

            <label class="form-label">
                Suffix
            </label>

            <input
                type="text"
                name="suffix"
                value="{{ old(
                    'suffix',
                    $employee->suffix ?? ''
                ) }}"
                class="form-control"
                placeholder="Jr., Sr., III"
            >

        </div>

    </div>

</div>


{{-- =========================================================
     CONTACT INFORMATION
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Contact Information
    </div>

    <div class="erp-form-section-subtitle">
        Employee contact details.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label class="form-label">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old(
                    'email',
                    $employee->email ?? ''
                ) }}"
                class="form-control @error('email') is-invalid @enderror"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-md-6">

            <label class="form-label">
                Phone
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old(
                    'phone',
                    $employee->phone ?? ''
                ) }}"
                class="form-control @error('phone') is-invalid @enderror"
            >

            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="col-12">

            <label class="form-label">
                Address
            </label>

            <textarea
                name="address"
                rows="3"
                class="form-control @error('address') is-invalid @enderror"
            >{{ old(
                'address',
                $employee->address ?? ''
            ) }}</textarea>

            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     EMPLOYMENT
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Employment Information
    </div>

    <div class="erp-form-section-subtitle">
        Position, department, employment type and compensation.
    </div>

    <div class="row g-3">

        {{-- Position --}}

        <div class="col-md-6">

            <label class="form-label">
                Position
            </label>

            <input
                type="text"
                name="position"
                value="{{ old(
                    'position',
                    $employee->position ?? ''
                ) }}"
                class="form-control"
                placeholder="e.g. Lead Technician"
            >

        </div>


        {{-- Department --}}

        <div class="col-md-6">

            <label class="form-label">
                Department
            </label>

            <input
                type="text"
                name="department"
                value="{{ old(
                    'department',
                    $employee->department ?? ''
                ) }}"
                class="form-control"
                placeholder="e.g. Operations"
            >

        </div>


        {{-- Employment Type --}}

        <div class="col-md-4">

            <label class="form-label">
                Employment Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="employment_type"
                class="form-select"
                required
            >

                @foreach([
                    'full_time' => 'Full Time',
                    'part_time' => 'Part Time',
                    'contract' => 'Contract',
                    'temporary' => 'Temporary',
                    'casual' => 'Casual',
                    'project_based' => 'Project Based',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'employment_type',
                                $employee->employment_type ?? 'full_time'
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Hire Date --}}

        <div class="col-md-4">

            <label class="form-label">
                Hire Date
            </label>

            <input
                type="date"
                name="hire_date"
                value="{{ old(
                    'hire_date',
                    isset($employee) && $employee->hire_date
                        ? $employee->hire_date->format('Y-m-d')
                        : ''
                ) }}"
                class="form-control"
            >

        </div>


        {{-- Hourly Rate --}}

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
                    value="{{ old(
                        'hourly_rate',
                        $employee->hourly_rate ?? ''
                    ) }}"
                    class="form-control"
                    min="0"
                    step="0.01"
                >

            </div>

        </div>


        {{-- Status --}}

        <div class="col-md-4">

            <label class="form-label">
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                class="form-select"
                required
            >

                @foreach([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'on_leave' => 'On Leave',
                    'terminated' => 'Terminated',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'status',
                                $employee->status ?? 'active'
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>

    </div>

</div>


{{-- =========================================================
     EMERGENCY CONTACT
     ========================================================= --}}

<div class="erp-form-section">

    <div class="erp-form-section-title">
        Emergency Contact
    </div>

    <div class="erp-form-section-subtitle">
        Person to contact in case of emergency.
    </div>

    <div class="row g-3">

        <div class="col-md-5">

            <label class="form-label">
                Contact Name
            </label>

            <input
                type="text"
                name="emergency_contact_name"
                value="{{ old(
                    'emergency_contact_name',
                    $employee->emergency_contact_name ?? ''
                ) }}"
                class="form-control"
            >

        </div>


        <div class="col-md-3">

            <label class="form-label">
                Relationship
            </label>

            <input
                type="text"
                name="emergency_contact_relationship"
                value="{{ old(
                    'emergency_contact_relationship',
                    $employee->emergency_contact_relationship ?? ''
                ) }}"
                class="form-control"
                placeholder="Spouse"
            >

        </div>


        <div class="col-md-4">

            <label class="form-label">
                Phone
            </label>

            <input
                type="text"
                name="emergency_contact_phone"
                value="{{ old(
                    'emergency_contact_phone',
                    $employee->emergency_contact_phone ?? ''
                ) }}"
                class="form-control"
            >

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
        rows="4"
        class="form-control"
        placeholder="Additional employee information..."
    >{{ old(
        'notes',
        $employee->notes ?? ''
    ) }}</textarea>

</div>