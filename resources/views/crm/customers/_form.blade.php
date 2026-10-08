@php
    $customer = $customer ?? null;
@endphp

<div class="form-section">

    <div class="form-section-title">
        Customer Type
    </div>

    <div class="form-section-description">
        Select whether this customer is an individual or a company.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label for="customer_type" class="form-label">
                Customer Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="customer_type"
                id="customer_type"
                class="form-select @error('customer_type') is-invalid @enderror"
                required
            >
                <option value="individual"
                    {{ old('customer_type', $customer->customer_type ?? 'individual') === 'individual' ? 'selected' : '' }}>
                    Individual
                </option>

                <option value="company"
                    {{ old('customer_type', $customer->customer_type ?? '') === 'company' ? 'selected' : '' }}>
                    Company
                </option>
            </select>

            @error('customer_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-6">

            <label for="status" class="form-label">
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                id="status"
                class="form-select @error('status') is-invalid @enderror"
                required
            >
                <option value="active"
                    {{ old('status', $customer->status ?? 'active') === 'active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="inactive"
                    {{ old('status', $customer->status ?? '') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>

            @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- COMPANY INFORMATION --}}
<div
    class="form-section"
    id="company-section"
>

    <div class="form-section-title">
        Company Information
    </div>

    <div class="form-section-description">
        Enter the legal or business information for the company.
    </div>

    <div class="row g-3">

        <div class="col-md-8">

            <label for="company_name" class="form-label">
                Company Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="company_name"
                id="company_name"
                class="form-control @error('company_name') is-invalid @enderror"
                value="{{ old('company_name', $customer->company_name ?? '') }}"
                placeholder="ABC Construction LLC"
            >

            @error('company_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label for="tax_id" class="form-label">
                Tax ID / EIN
            </label>

            <input
                type="text"
                name="tax_id"
                id="tax_id"
                class="form-control @error('tax_id') is-invalid @enderror"
                value="{{ old('tax_id', $customer->tax_id ?? '') }}"
                placeholder="12-3456789"
            >

            @error('tax_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- INDIVIDUAL INFORMATION --}}
<div
    class="form-section"
    id="individual-section"
>

    <div class="form-section-title">
        Personal Information
    </div>

    <div class="form-section-description">
        Enter the customer's personal information.
    </div>

    <div class="row g-3">

        <div class="col-md-4">

            <label for="first_name" class="form-label">
                First Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="first_name"
                id="first_name"
                class="form-control @error('first_name') is-invalid @enderror"
                value="{{ old('first_name', $customer->first_name ?? '') }}"
                placeholder="John"
            >

            @error('first_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label for="middle_name" class="form-label">
                Middle Name
            </label>

            <input
                type="text"
                name="middle_name"
                id="middle_name"
                class="form-control @error('middle_name') is-invalid @enderror"
                value="{{ old('middle_name', $customer->middle_name ?? '') }}"
                placeholder="Michael"
            >

            @error('middle_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label for="last_name" class="form-label">
                Last Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="last_name"
                id="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $customer->last_name ?? '') }}"
                placeholder="Smith"
            >

            @error('last_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- CONTACT INFORMATION --}}
<div class="form-section">

    <div class="form-section-title">
        Contact Information
    </div>

    <div class="form-section-description">
        Provide the customer's primary contact details.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label for="email" class="form-label">
                Email Address
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $customer->email ?? '') }}"
                placeholder="john@example.com"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-3">

            <label for="phone" class="form-label">
                Phone
            </label>

            <input
                type="text"
                name="phone"
                id="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $customer->phone ?? '') }}"
                placeholder="(512) 555-0100"
            >

            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-3">

            <label for="mobile" class="form-label">
                Mobile
            </label>

            <input
                type="text"
                name="mobile"
                id="mobile"
                class="form-control @error('mobile') is-invalid @enderror"
                value="{{ old('mobile', $customer->mobile ?? '') }}"
                placeholder="(512) 555-0101"
            >

            @error('mobile')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- US ADDRESS --}}
<div class="form-section">

    <div class="form-section-title">
        Address
    </div>

    <div class="form-section-description">
        Enter the customer's United States mailing or service address.
    </div>

    <div class="row g-3">

        <div class="col-md-8">

            <label for="address_line_1" class="form-label">
                Address Line 1
            </label>

            <input
                type="text"
                name="address_line_1"
                id="address_line_1"
                class="form-control @error('address_line_1') is-invalid @enderror"
                value="{{ old('address_line_1', $customer->address_line_1 ?? '') }}"
                placeholder="123 Main Street"
            >

            @error('address_line_1')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label for="address_line_2" class="form-label">
                Address Line 2
            </label>

            <input
                type="text"
                name="address_line_2"
                id="address_line_2"
                class="form-control @error('address_line_2') is-invalid @enderror"
                value="{{ old('address_line_2', $customer->address_line_2 ?? '') }}"
                placeholder="Apt, Suite, Unit"
            >

            @error('address_line_2')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-5">

            <label for="city" class="form-label">
                City
            </label>

            <input
                type="text"
                name="city"
                id="city"
                class="form-control @error('city') is-invalid @enderror"
                value="{{ old('city', $customer->city ?? '') }}"
                placeholder="Austin"
            >

            @error('city')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label for="state" class="form-label">
                State
            </label>

            <select
                name="state"
                id="state"
                class="form-select @error('state') is-invalid @enderror"
            >
                <option value="">Select State</option>

                @php
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

                @foreach ($states as $code => $name)
                    <option
                        value="{{ $code }}"
                        {{ old('state', $customer->state ?? '') === $code ? 'selected' : '' }}
                    >
                        {{ $code }} - {{ $name }}
                    </option>
                @endforeach

            </select>

            @error('state')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-3">

            <label for="zip_code" class="form-label">
                ZIP Code
            </label>

            <input
                type="text"
                name="zip_code"
                id="zip_code"
                class="form-control @error('zip_code') is-invalid @enderror"
                value="{{ old('zip_code', $customer->zip_code ?? '') }}"
                placeholder="78701"
                maxlength="10"
            >

            @error('zip_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-6">

            <label for="country" class="form-label">
                Country
            </label>

            <input
                type="text"
                name="country"
                id="country"
                class="form-control"
                value="{{ old('country', $customer->country ?? 'United States') }}"
            >

        </div>

    </div>

</div>


{{-- NOTES --}}
<div class="form-section">

    <div class="form-section-title">
        Notes
    </div>

    <div class="form-section-description">
        Add any additional information about the customer.
    </div>

    <textarea
        name="notes"
        id="notes"
        rows="4"
        class="form-control @error('notes') is-invalid @enderror"
        placeholder="Additional customer notes..."
    >{{ old('notes', $customer->notes ?? '') }}</textarea>

    @error('notes')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>





<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerType = document.getElementById('customer_type');
    const companySection = document.getElementById('company-section');
    const individualSection = document.getElementById('individual-section');

    function updateCustomerType() {

        if (!customerType) {
            return;
        }

        if (customerType.value === 'company') {

            companySection.style.display = 'block';
            individualSection.style.display = 'none';

        } else {

            companySection.style.display = 'none';
            individualSection.style.display = 'block';

        }
    }

    customerType.addEventListener(
        'change',
        updateCustomerType
    );

    updateCustomerType();

});
</script>