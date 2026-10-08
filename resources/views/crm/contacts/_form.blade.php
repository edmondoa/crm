@php
    $contact = $contact ?? null;

    $selectedCustomerId = old(
        'customer_id',
        $contact->customer_id ?? request('customer_id')
    );

    $states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];
@endphp


{{-- CUSTOMER --}}
<div class="form-section">

    <div class="form-section-title">
        Customer Relationship
    </div>

    <div class="form-section-description">
        Select the customer this contact belongs to.
    </div>

    <div class="row g-3">

        <div class="col-md-8">

            <label
                for="customer_id"
                class="form-label"
            >
                Customer
                <span class="text-danger">*</span>
            </label>

            <select
                name="customer_id"
                id="customer_id"
                class="form-select @error('customer_id') is-invalid @enderror"
                required
            >

                <option value="">
                    Select Customer
                </option>

                @foreach($customers as $customer)

                    <option
                        value="{{ $customer->id }}"
                        {{ (string) $selectedCustomerId === (string) $customer->id ? 'selected' : '' }}
                    >
                        {{ $customer->display_name }}
                        — {{ $customer->customer_code }}
                    </option>

                @endforeach

            </select>

            @error('customer_id')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-4">

            <label
                for="contact_type"
                class="form-label"
            >
                Contact Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="contact_type"
                id="contact_type"
                class="form-select @error('contact_type') is-invalid @enderror"
                required
            >

                @foreach([
                    'primary' => 'Primary',
                    'billing' => 'Billing',
                    'project' => 'Project',
                    'site' => 'Site',
                    'technical' => 'Technical',
                    'emergency' => 'Emergency',
                    'other' => 'Other',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        {{ old('contact_type', $contact->contact_type ?? 'primary') === $value ? 'selected' : '' }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

            @error('contact_type')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>

    </div>

</div>


{{-- PERSONAL INFORMATION --}}
<div class="form-section">

    <div class="form-section-title">
        Personal Information
    </div>

    <div class="form-section-description">
        Enter the contact's name and business role.
    </div>

    <div class="row g-3">

        <div class="col-md-4">

            <label
                for="first_name"
                class="form-label"
            >
                First Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="first_name"
                id="first_name"
                class="form-control @error('first_name') is-invalid @enderror"
                value="{{ old('first_name', $contact->first_name ?? '') }}"
                placeholder="John"
                required
            >

            @error('first_name')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-4">

            <label
                for="middle_name"
                class="form-label"
            >
                Middle Name
            </label>

            <input
                type="text"
                name="middle_name"
                id="middle_name"
                class="form-control"
                value="{{ old('middle_name', $contact->middle_name ?? '') }}"
                placeholder="Michael"
            >

        </div>


        <div class="col-md-4">

            <label
                for="last_name"
                class="form-label"
            >
                Last Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="last_name"
                id="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $contact->last_name ?? '') }}"
                placeholder="Smith"
                required
            >

            @error('last_name')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-6">

            <label
                for="job_title"
                class="form-label"
            >
                Job Title
            </label>

            <input
                type="text"
                name="job_title"
                id="job_title"
                class="form-control"
                value="{{ old('job_title', $contact->job_title ?? '') }}"
                placeholder="Project Manager"
            >

        </div>


        <div class="col-md-6">

            <label
                for="department"
                class="form-label"
            >
                Department
            </label>

            <input
                type="text"
                name="department"
                id="department"
                class="form-control"
                value="{{ old('department', $contact->department ?? '') }}"
                placeholder="Operations"
            >

        </div>

    </div>

</div>


{{-- CONTACT INFORMATION --}}
<div class="form-section">

    <div class="form-section-title">
        Contact Information
    </div>

    <div class="form-section-description">
        Enter the contact's communication details.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label
                for="email"
                class="form-label"
            >
                Email Address
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $contact->email ?? '') }}"
                placeholder="john.smith@example.com"
            >

            @error('email')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="col-md-3">

            <label
                for="phone"
                class="form-label"
            >
                Phone
            </label>

            <input
                type="text"
                name="phone"
                id="phone"
                class="form-control"
                value="{{ old('phone', $contact->phone ?? '') }}"
                placeholder="(512) 555-0100"
            >

        </div>


        <div class="col-md-3">

            <label
                for="mobile"
                class="form-label"
            >
                Mobile
            </label>

            <input
                type="text"
                name="mobile"
                id="mobile"
                class="form-control"
                value="{{ old('mobile', $contact->mobile ?? '') }}"
                placeholder="(512) 555-0101"
            >

        </div>


        <div class="col-md-6">

            <label
                for="preferred_contact_method"
                class="form-label"
            >
                Preferred Contact Method
            </label>

            <select
                name="preferred_contact_method"
                id="preferred_contact_method"
                class="form-select"
            >

                <option value="">
                    Not Specified
                </option>

                <option
                    value="email"
                    {{ old('preferred_contact_method', $contact->preferred_contact_method ?? '') === 'email' ? 'selected' : '' }}
                >
                    Email
                </option>

                <option
                    value="phone"
                    {{ old('preferred_contact_method', $contact->preferred_contact_method ?? '') === 'phone' ? 'selected' : '' }}
                >
                    Phone
                </option>

                <option
                    value="mobile"
                    {{ old('preferred_contact_method', $contact->preferred_contact_method ?? '') === 'mobile' ? 'selected' : '' }}
                >
                    Mobile
                </option>

            </select>

        </div>

    </div>

</div>


{{-- ADDRESS --}}
<div class="form-section">

    <div class="form-section-title">
        Address
    </div>

    <div class="form-section-description">
        Enter the contact's United States mailing address.
    </div>

    <div class="row g-3">

        <div class="col-md-8">

            <label
                for="address_line_1"
                class="form-label"
            >
                Address Line 1
            </label>

            <input
                type="text"
                name="address_line_1"
                id="address_line_1"
                class="form-control"
                value="{{ old('address_line_1', $contact->address_line_1 ?? '') }}"
                placeholder="123 Main Street"
            >

        </div>


        <div class="col-md-4">

            <label
                for="address_line_2"
                class="form-label"
            >
                Address Line 2
            </label>

            <input
                type="text"
                name="address_line_2"
                id="address_line_2"
                class="form-control"
                value="{{ old('address_line_2', $contact->address_line_2 ?? '') }}"
                placeholder="Suite 200"
            >

        </div>


        <div class="col-md-5">

            <label
                for="city"
                class="form-label"
            >
                City
            </label>

            <input
                type="text"
                name="city"
                id="city"
                class="form-control"
                value="{{ old('city', $contact->city ?? '') }}"
                placeholder="Austin"
            >

        </div>


        <div class="col-md-4">

            <label
                for="state"
                class="form-label"
            >
                State
            </label>

            <select
                name="state"
                id="state"
                class="form-select"
            >

                <option value="">
                    Select State
                </option>

                @foreach($states as $code => $name)

                    <option
                        value="{{ $code }}"
                        {{ old('state', $contact->state ?? '') === $code ? 'selected' : '' }}
                    >
                        {{ $code }} - {{ $name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-3">

            <label
                for="zip_code"
                class="form-label"
            >
                ZIP Code
            </label>

            <input
                type="text"
                name="zip_code"
                id="zip_code"
                class="form-control"
                value="{{ old('zip_code', $contact->zip_code ?? '') }}"
                placeholder="78701"
                maxlength="10"
            >

        </div>


        <div class="col-md-6">

            <label
                for="country"
                class="form-label"
            >
                Country
            </label>

            <input
                type="text"
                name="country"
                id="country"
                class="form-control"
                value="{{ old('country', $contact->country ?? 'United States') }}"
            >

        </div>

    </div>

</div>


{{-- CRM SETTINGS --}}
<div class="form-section">

    <div class="form-section-title">
        CRM Settings
    </div>

    <div class="form-section-description">
        Configure the contact's CRM status and primary-contact designation.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label
                for="status"
                class="form-label"
            >
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                id="status"
                class="form-select"
                required
            >

                <option
                    value="active"
                    {{ old('status', $contact->status ?? 'active') === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="inactive"
                    {{ old('status', $contact->status ?? '') === 'inactive' ? 'selected' : '' }}
                >
                    Inactive
                </option>

            </select>

        </div>


        <div class="col-md-6">

            <div class="form-check contact-primary-option">

                <input
                    type="hidden"
                    name="is_primary"
                    value="0"
                >

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="is_primary"
                    id="is_primary"
                    value="1"
                    {{ old('is_primary', $contact->is_primary ?? false) ? 'checked' : '' }}
                >

                <label
                    class="form-check-label"
                    for="is_primary"
                >
                    <strong>Primary Contact</strong>

                    <span>
                        Designate this person as the customer's primary contact.
                    </span>
                </label>

            </div>

        </div>

    </div>

</div>


{{-- NOTES --}}
<div class="form-section">

    <div class="form-section-title">
        Notes
    </div>

    <div class="form-section-description">
        Add additional information about this contact.
    </div>

    <textarea
        name="notes"
        id="notes"
        rows="4"
        class="form-control"
        placeholder="Additional contact notes..."
    >{{ old('notes', $contact->notes ?? '') }}</textarea>

</div>


{{-- ACTIONS --}}
<div class="form-actions">

    <a
        href="{{ $contact?->exists
            ? route('crm.contacts.show', $contact)
            : route('crm.contacts.index') }}"
        class="btn btn-light"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >

        <i class="bi bi-check-lg me-1"></i>

        {{ $contact?->exists
            ? 'Update Contact'
            : 'Create Contact' }}

    </button>

</div>