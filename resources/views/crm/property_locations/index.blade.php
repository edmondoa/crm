@extends('layouts.admin')

@section('title', 'Property Locations')

@section('page-title', 'Property Locations')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Property Locations
            </h1>

            <p class="page-subtitle">
                Manage physical addresses associated with properties.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.property-locations.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-geo-alt me-1"></i>
                Add Location
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
     STATISTICS
========================================================= --}}

<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-geo-alt"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Total Locations
            </div>

            <div class="stat-value">
                {{ number_format($stats['total']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Active
            </div>

            <div class="stat-value">
                {{ number_format($stats['active']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-star"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Primary
            </div>

            <div class="stat-value">
                {{ number_format($stats['primary']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-pause-circle"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Inactive
            </div>

            <div class="stat-value">
                {{ number_format($stats['inactive']) }}
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================= --}}

<form
    method="GET"
    action="{{ route('crm.property-locations.index') }}"
    class="property-location-filters"
>

    <div class="filter-search">

        <label class="form-label">
            Search
        </label>

        <input
            type="search"
            name="search"
            class="form-control"
            value="{{ request('search') }}"
            placeholder="Location, address, city, ZIP, property..."
        >

    </div>


    <div>

        <label class="form-label">
            Property
        </label>

        <select
            name="property_id"
            class="form-select"
        >

            <option value="">
                All Properties
            </option>

            @foreach($properties as $property)

                <option
                    value="{{ $property->id }}"
                    @selected(
                        request('property_id') == $property->id
                    )
                >
                    {{ $property->property_code }}
                    —
                    {{ $property->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div>

        <label class="form-label">
            Type
        </label>

        <select
            name="type"
            class="form-select"
        >

            <option value="">
                All Types
            </option>

            <option
                value="office"
                @selected(request('type') === 'office')
            >
                Office
            </option>

            <option
                value="warehouse"
                @selected(request('type') === 'warehouse')
            >
                Warehouse
            </option>

            <option
                value="job_site"
                @selected(request('type') === 'job_site')
            >
                Job Site
            </option>

            <option
                value="billing"
                @selected(request('type') === 'billing')
            >
                Billing
            </option>

            <option
                value="mailing"
                @selected(request('type') === 'mailing')
            >
                Mailing
            </option>

            <option
                value="residential"
                @selected(request('type') === 'residential')
            >
                Residential
            </option>

            <option
                value="retail"
                @selected(request('type') === 'retail')
            >
                Retail
            </option>

            <option
                value="other"
                @selected(request('type') === 'other')
            >
                Other
            </option>

        </select>

    </div>


    <div>

        <label class="form-label">
            Status
        </label>

        <select
            name="status"
            class="form-select"
        >

            <option value="">
                All Status
            </option>

            <option
                value="active"
                @selected(request('status') === 'active')
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(request('status') === 'inactive')
            >
                Inactive
            </option>

        </select>

    </div>


    <div class="filter-actions">

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-search"></i>
        </button>

        <a
            href="{{ route('crm.property-locations.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </div>

</form>


{{-- =========================================================
     TABLE
========================================================= --}}

<div class="card">

    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Location</th>

                    <th>Property</th>

                    <th>Address</th>

                    <th>Type</th>

                    <th>Status</th>

                    <th class="text-end">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($locations as $location)

                    <tr>

                        <td>

                            <a
                                href="{{ route(
                                    'crm.property-locations.show',
                                    $location
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $location->location_name }}
                            </a>

                            <div class="customer-code">
                                {{ $location->location_code }}
                            </div>

                            @if($location->is_primary)

                                <span class="location-primary-badge">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Primary
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($location->property)

                                <a
                                    href="{{ route(
                                        'crm.properties.show',
                                        $location->property
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $location->property->name }}
                                </a>

                                <div class="customer-code">
                                    {{ $location->property->property_code }}
                                </div>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="location-address">

                                <div>
                                    {{ $location->address_line_1 }}
                                </div>

                                @if($location->address_line_2)

                                    <div>
                                        {{ $location->address_line_2 }}
                                    </div>

                                @endif

                                <div>
                                    {{ $location->city }},
                                    {{ $location->state }}
                                    {{ $location->zip_code }}
                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="property-type-badge">
                                {{ $location->location_type_label }}
                            </span>

                        </td>


                        <td>

                            @if($location->status === 'active')

                                <span class="status-badge status-active">
                                    Active
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td class="text-end">

                            <div class="btn-group">

                                <a
                                    href="{{ route(
                                        'crm.property-locations.show',
                                        $location
                                    ) }}"
                                    class="btn btn-sm btn-light"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route(
                                        'crm.property-locations.edit',
                                        $location
                                    ) }}"
                                    class="btn btn-sm btn-light"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5"
                        >

                            <div class="empty-state">

                                <div class="empty-state-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>

                                <h5>
                                    No property locations found
                                </h5>

                                <p>
                                    Add a physical location to a property.
                                </p>

                                <a
                                    href="{{ route(
                                        'crm.property-locations.create'
                                    ) }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add Location
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($locations->hasPages())

        <div class="card-footer">

            {{ $locations->links() }}

        </div>

    @endif

</div>

@endsection