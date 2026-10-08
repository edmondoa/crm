@extends('layouts.admin')

@section('title', 'Properties')

@section('page-title', 'Properties')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Properties
            </h1>

            <p class="page-subtitle">
                Manage customer properties and property information.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.properties.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-building-add me-1"></i>
                Add Property
            </a>

        </div>

    </div>

</div>


@if(session('success'))

    <div class="alert alert-success crm-alert">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

    </div>

@endif


{{-- STATISTICS --}}
<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-buildings"></i>
        </div>

        <div>

            <div class="stat-card-label">
                Total Properties
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['total']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-building-check"></i>
        </div>

        <div>

            <div class="stat-card-label">
                Active
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['active']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-shop"></i>
        </div>

        <div>

            <div class="stat-card-label">
                Commercial
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['commercial']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-building-dash"></i>
        </div>

        <div>

            <div class="stat-card-label">
                Inactive
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['inactive']) }}
            </div>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<form
    method="GET"
    action="{{ route('crm.properties.index') }}"
    class="property-filters"
>

    <div class="filter-search">

        <label
            for="search"
            class="form-label"
        >
            Search
        </label>

        <input
            type="text"
            name="search"
            id="search"
            class="form-control"
            value="{{ request('search') }}"
            placeholder="Property, customer, code..."
        >

    </div>


    <div>

        <label
            for="customer_id"
            class="form-label"
        >
            Customer
        </label>

        <select
            name="customer_id"
            id="customer_id"
            class="form-select"
        >

            <option value="">
                All Customers
            </option>

            @foreach($customers as $customer)

                <option
                    value="{{ $customer->id }}"
                    {{ (string) request('customer_id') === (string) $customer->id ? 'selected' : '' }}
                >
                    {{ $customer->display_name }}
                    — {{ $customer->customer_code }}
                </option>

            @endforeach

        </select>

    </div>


    <div>

        <label
            for="type"
            class="form-label"
        >
            Property Type
        </label>

        <select
            name="type"
            id="type"
            class="form-select"
        >

            <option value="">
                All Types
            </option>

            <option value="residential"
                {{ request('type') === 'residential' ? 'selected' : '' }}>
                Residential
            </option>

            <option value="commercial"
                {{ request('type') === 'commercial' ? 'selected' : '' }}>
                Commercial
            </option>

            <option value="industrial"
                {{ request('type') === 'industrial' ? 'selected' : '' }}>
                Industrial
            </option>

            <option value="office"
                {{ request('type') === 'office' ? 'selected' : '' }}>
                Office
            </option>

            <option value="retail"
                {{ request('type') === 'retail' ? 'selected' : '' }}>
                Retail
            </option>

            <option value="warehouse"
                {{ request('type') === 'warehouse' ? 'selected' : '' }}>
                Warehouse
            </option>

            <option value="multi_family"
                {{ request('type') === 'multi_family' ? 'selected' : '' }}>
                Multi-Family
            </option>

            <option value="land"
                {{ request('type') === 'land' ? 'selected' : '' }}>
                Land
            </option>

            <option value="other"
                {{ request('type') === 'other' ? 'selected' : '' }}>
                Other
            </option>

        </select>

    </div>


    <div>

        <label
            for="status"
            class="form-label"
        >
            Status
        </label>

        <select
            name="status"
            id="status"
            class="form-select"
        >

            <option value="">
                All Status
            </option>

            <option value="active"
                {{ request('status') === 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive"
                {{ request('status') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

    </div>


    <div class="filter-actions">

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-search me-1"></i>
            Search
        </button>

        <a
            href="{{ route('crm.properties.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </div>

</form>


{{-- PROPERTY TABLE --}}
<div class="card">

    <div class="card-header">

        <div>

            <div class="card-title">
                Customer Properties
            </div>

            <div class="card-subtitle">
                {{ $properties->total() }} property(ies)
            </div>

        </div>

    </div>


    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>
                        Property
                    </th>

                    <th>
                        Customer
                    </th>

                    <th>
                        Type
                    </th>

                    <th>
                        Primary Contact
                    </th>

                    <th>
                        Status
                    </th>

                    <th class="text-end">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($properties as $property)

                    <tr>

                        <td>

                            <div class="property-table-name">

                                <a
                                    href="{{ route('crm.properties.show', $property) }}"
                                    class="customer-name-link"
                                >
                                    {{ $property->name }}
                                </a>

                                <div class="customer-code">
                                    {{ $property->property_code }}
                                </div>

                            </div>

                        </td>


                        <td>

                            <a
                                href="{{ route('crm.customers.show', $property->customer) }}"
                                class="customer-name-link"
                            >
                                {{ $property->customer->display_name }}
                            </a>

                            <div class="customer-code">
                                {{ $property->customer->customer_code }}
                            </div>

                        </td>


                        <td>

                            <span class="property-type-badge">
                                {{ $property->property_type_label }}
                            </span>

                        </td>


                        <td>

                            @if($property->primaryContact)

                                <a
                                    href="{{ route('crm.contacts.show', $property->primaryContact) }}"
                                    class="customer-name-link"
                                >
                                    {{ $property->primaryContact->display_name }}
                                </a>

                                @if($property->primaryContact->job_title)

                                    <div class="property-contact-role">
                                        {{ $property->primaryContact->job_title }}
                                    </div>

                                @endif

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($property->status === 'active')

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

                            <div class="table-actions">

                                <a
                                    href="{{ route('crm.properties.show', $property) }}"
                                    class="btn btn-sm btn-light"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route('crm.properties.edit', $property) }}"
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

                                <i class="bi bi-buildings"></i>

                                <h5>
                                    No properties found
                                </h5>

                                <p>
                                    Create your first customer property.
                                </p>

                                <a
                                    href="{{ route('crm.properties.create') }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-building-add me-1"></i>
                                    Add Property
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($properties->hasPages())

        <div class="card-footer">

            {{ $properties->links() }}

        </div>

    @endif

</div>

@endsection