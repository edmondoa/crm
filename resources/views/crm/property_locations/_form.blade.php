{{-- =========================================================
     PROPERTY RELATIONSHIP
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Property
    </div>

    <div class="form-section-description">
        Associate this physical location with a property.
    </div>

    <div class="row g-3">

        <div class="col-md-12">

            <label class="form-label">
                Property
                <span class="text-danger">*</span>
            </label>

            <select
                name="property_id"
                class="form-select @error('property_id') is-invalid @enderror"
                required
            >

                <option value="">
                    Select property
                </option>

                @foreach($properties as $property)

                    <option
                        value="{{ $property->id }}"
                        @selected(
                            old(
                                'property_id',
                                $selectedPropertyId
                                ?? $propertyLocation->property_id
                                ?? ''
                            ) == $property->id
                        )
                    >
                        {{ $property->property_code }}
                        —
                        {{ $property->name }}

                        @if($property->customer)
                            — {{ $property->customer->display_name }}
                        @endif
                    </option>

                @endforeach

            </select>

            @error('property_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     LOCATION INFORMATION
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Location Information
    </div>

    <div class="form-section-description">
        Define how this location is used within the property.
    </div>

    <div class="row g-3">

        <div class="col-md-8">

            <label class="form-label">
                Location Name
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="location_name"
                class="form-control @error('location_name') is-invalid @enderror"
                value="{{ old('location_name', $propertyLocation->location_name ?? '') }}"
                placeholder="e.g. Main Office, Warehouse, Job Site"
                required
            >

            @error('location_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Location Type
                <span class="text-danger">*</span>
            </label>

            <select
                name="location_type"
                class="form-select @error('location_type') is-invalid @enderror"
                required
            >

                <option value="">
                    Select type
                </option>

                @php
                    $locationTypes = [
                        'office' => 'Office',
                        'warehouse' => 'Warehouse',
                        'job_site' => 'Job Site',
                        'billing' => 'Billing',
                        'mailing' => 'Mailing',
                        'residential' => 'Residential',
                        'retail' => 'Retail',
                        'other' => 'Other',
                    ];
                @endphp

                @foreach($locationTypes as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'location_type',
                                $propertyLocation->location_type ?? 'job_site'
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

            @error('location_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     US ADDRESS
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        US Address
    </div>

    <div class="form-section-description">
        Enter the physical address for this property location.
    </div>

    <div class="row g-3">

        <div class="col-md-12">

            <label class="form-label">
                Address Line 1
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="address_line_1"
                class="form-control @error('address_line_1') is-invalid @enderror"
                value="{{ old('address_line_1', $propertyLocation->address_line_1 ?? '') }}"
                placeholder="123 Main Street"
                required
            >

            @error('address_line_1')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-12">

            <label class="form-label">
                Address Line 2
            </label>

            <input
                type="text"
                name="address_line_2"
                class="form-control @error('address_line_2') is-invalid @enderror"
                value="{{ old('address_line_2', $propertyLocation->address_line_2 ?? '') }}"
                placeholder="Suite 200, Building A, Unit 5"
            >

            @error('address_line_2')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-5">

            <label class="form-label">
                City
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="city"
                class="form-control @error('city') is-invalid @enderror"
                value="{{ old('city', $propertyLocation->city ?? '') }}"
                placeholder="Dallas"
                required
            >

            @error('city')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-3">

            <label class="form-label">
                State
                <span class="text-danger">*</span>
            </label>

            <select
                name="state"
                class="form-select @error('state') is-invalid @enderror"
                required
            >

                <option value="">
                    State
                </option>

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

                @foreach($states as $code => $name)

                    <option
                        value="{{ $code }}"
                        @selected(
                            old(
                                'state',
                                $propertyLocation->state ?? ''
                            ) === $code
                        )
                    >
                        {{ $code }} — {{ $name }}
                    </option>

                @endforeach

            </select>

            @error('state')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-4">

            <label class="form-label">
                ZIP Code
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="zip_code"
                class="form-control @error('zip_code') is-invalid @enderror"
                value="{{ old('zip_code', $propertyLocation->zip_code ?? '') }}"
                placeholder="75201"
                maxlength="10"
                required
            >

            @error('zip_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-6">

            <label class="form-label">
                County
            </label>

            <input
                type="text"
                name="county"
                class="form-control @error('county') is-invalid @enderror"
                value="{{ old('county', $propertyLocation->county ?? '') }}"
                placeholder="Dallas County"
            >

            @error('county')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     GEOLOCATION
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Geographic Coordinates
    </div>

    <div class="form-section-description">
        Optional latitude and longitude for mapping and future integrations.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label class="form-label">
                Latitude
            </label>

            <input
                type="number"
                step="0.0000001"
                name="latitude"
                class="form-control @error('latitude') is-invalid @enderror"
                value="{{ old('latitude', $propertyLocation->latitude ?? '') }}"
                placeholder="32.7767000"
            >

            @error('latitude')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-6">

            <label class="form-label">
                Longitude
            </label>

            <input
                type="number"
                step="0.0000001"
                name="longitude"
                class="form-control @error('longitude') is-invalid @enderror"
                value="{{ old('longitude', $propertyLocation->longitude ?? '') }}"
                placeholder="-96.7970000"
            >

            @error('longitude')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


{{-- =========================================================
     STATUS
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Status & Primary Location
    </div>

    <div class="form-section-description">
        Define whether this location is active and whether it is the property's primary location.
    </div>

    <div class="row g-3">

        <div class="col-md-6">

            <label class="form-label">
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                class="form-select @error('status') is-invalid @enderror"
                required
            >

                <option
                    value="active"
                    @selected(
                        old(
                            'status',
                            $propertyLocation->status ?? 'active'
                        ) === 'active'
                    )
                >
                    Active
                </option>

                <option
                    value="inactive"
                    @selected(
                        old(
                            'status',
                            $propertyLocation->status ?? ''
                        ) === 'inactive'
                    )
                >
                    Inactive
                </option>

            </select>

            @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="col-md-6">

            <label class="form-label d-block">
                Primary Location
            </label>

            <div class="form-check form-switch mt-2">

                <input
                    type="checkbox"
                    name="is_primary"
                    value="1"
                    class="form-check-input"
                    id="is_primary"
                    @checked(
                        old(
                            'is_primary',
                            $propertyLocation->is_primary ?? false
                        )
                    )
                >

                <label
                    class="form-check-label"
                    for="is_primary"
                >
                    Set as primary location
                </label>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     NOTES
========================================================= --}}

<div class="form-section">

    <div class="form-section-title">
        Notes
    </div>

    <div class="form-section-description">
        Add additional information about this location.
    </div>

    <textarea
        name="notes"
        rows="4"
        class="form-control @error('notes') is-invalid @enderror"
        placeholder="Additional location information..."
    >{{ old('notes', $propertyLocation->notes ?? '') }}</textarea>

    @error('notes')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>


{{-- =========================================================
     ACTIONS
========================================================= --}}

<div class="form-actions">

    <a
        href="{{ isset($propertyLocation)
            ? route('crm.property-locations.show', $propertyLocation)
            : route('crm.property-locations.index')
        }}"
        class="btn btn-light"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        <i class="bi bi-check-lg me-1"></i>

        {{ isset($propertyLocation)
            ? 'Update Location'
            : 'Create Location'
        }}
    </button>

</div>