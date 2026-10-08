@extends('layouts.admin')

@section('title', $propertyLocation->location_name)

@section('page-title', $propertyLocation->location_name)

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                {{ $propertyLocation->location_name }}
            </h1>

            <p class="page-subtitle">
                {{ $propertyLocation->location_code }}
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route(
                    'crm.property-locations.edit',
                    $propertyLocation
                ) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route(
                    'crm.properties.show',
                    $propertyLocation->property
                ) }}"
                class="btn btn-light"
            >
                <i class="bi bi-building me-1"></i>
                Property
            </a>

        </div>

    </div>

</div>


<div class="row g-4">

    {{-- =====================================================
         LEFT COLUMN
    ====================================================== --}}

    <div class="col-lg-4">

        <div class="card">

            <div class="card-body">

                <div class="property-location-profile">

                    <div class="property-location-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <h2 class="property-location-profile-name">
                        {{ $propertyLocation->location_name }}
                    </h2>

                    <div class="property-location-profile-code">
                        {{ $propertyLocation->location_code }}
                    </div>

                    <div class="mt-3">

                        <span class="property-type-badge">
                            {{ $propertyLocation->location_type_label }}
                        </span>

                    </div>

                    <div class="mt-3">

                        @if($propertyLocation->is_primary)

                            <span class="location-primary-badge">
                                <i class="bi bi-star-fill me-1"></i>
                                Primary Location
                            </span>

                        @endif

                        @if($propertyLocation->status === 'active')

                            <span class="status-badge status-active">
                                Active
                            </span>

                        @else

                            <span class="status-badge status-inactive">
                                Inactive
                            </span>

                        @endif

                    </div>

                </div>


                <div class="customer-detail-list">

                    <div class="customer-detail-item">

                        <span>
                            Property
                        </span>

                        <strong>

                            <a
                                href="{{ route(
                                    'crm.properties.show',
                                    $propertyLocation->property
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $propertyLocation->property->name }}
                            </a>

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Property Code
                        </span>

                        <strong>
                            {{ $propertyLocation->property->property_code }}
                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Customer
                        </span>

                        <strong>

                            @if($propertyLocation->property->customer)

                                <a
                                    href="{{ route(
                                        'crm.customers.show',
                                        $propertyLocation->property->customer
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $propertyLocation->property->customer->display_name }}
                                </a>

                            @else

                                —

                            @endif

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         RIGHT COLUMN
    ====================================================== --}}

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Address
                    </div>

                    <div class="card-subtitle">
                        Physical location information.
                    </div>

                </div>

            </div>

            <div class="card-body">

                <div class="property-location-address-card">

                    <div class="property-location-address-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div>

                        <div class="property-location-address-line">
                            {{ $propertyLocation->address_line_1 }}
                        </div>

                        @if($propertyLocation->address_line_2)

                            <div class="property-location-address-line">
                                {{ $propertyLocation->address_line_2 }}
                            </div>

                        @endif

                        <div class="property-location-address-line">
                            {{ $propertyLocation->city }},
                            {{ $propertyLocation->state }}
                            {{ $propertyLocation->zip_code }}
                        </div>

                        @if($propertyLocation->county)

                            <div class="property-location-address-county">
                                {{ $propertyLocation->county }} County
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
             COORDINATES
        ================================================== --}}

        @if(
            $propertyLocation->latitude !== null &&
            $propertyLocation->longitude !== null
        )

            <div class="card mt-4">

                <div class="card-header">

                    <div>

                        <div class="card-title">
                            Geographic Coordinates
                        </div>

                        <div class="card-subtitle">
                            Location coordinates for mapping integrations.
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="detail-label">
                                Latitude
                            </div>

                            <div class="detail-value">
                                {{ $propertyLocation->latitude }}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="detail-label">
                                Longitude
                            </div>

                            <div class="detail-value">
                                {{ $propertyLocation->longitude }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- =================================================
             NOTES
        ================================================== --}}

        @if($propertyLocation->notes)

            <div class="card mt-4">

                <div class="card-header">

                    <div>

                        <div class="card-title">
                            Notes
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="customer-notes">
                        {!! nl2br(e($propertyLocation->notes)) !!}
                    </div>

                </div>

            </div>

        @endif


        {{-- =================================================
             DELETE
        ================================================== --}}

        <div class="card mt-4 border-danger">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center gap-3">

                    <div>

                        <strong class="text-danger">
                            Delete Location
                        </strong>

                        <p class="text-muted mb-0 mt-1 small">
                            This will permanently remove this property location.
                        </p>

                    </div>

                    <form
                        method="POST"
                        action="{{ route(
                            'crm.property-locations.destroy',
                            $propertyLocation
                        ) }}"
                        onsubmit="return confirm(
                            'Are you sure you want to delete this location?'
                        );"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-outline-danger"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Delete
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection