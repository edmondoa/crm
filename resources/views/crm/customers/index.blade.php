@extends('layouts.admin')

@section('title', 'Customers')

@section('page-title', 'Customers')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Customers
            </h1>

            <p class="page-subtitle">
                Manage CRM customers and customer relationships.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.customers.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Customer
            </a>

        </div>

    </div>

</div>


{{-- Alerts --}}
@if(session('success'))

    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}
    </div>

@endif


{{-- Statistics --}}
<div class="stats-grid mb-4">

    <div class="erp-stat-card">

        <div class="erp-stat-card-icon">
            <i class="bi bi-people"></i>
        </div>

        <div>
            <div class="erp-stat-card-label">
                Total Customers
            </div>

            <div class="erp-stat-card-value">
                {{ number_format($stats['total']) }}
            </div>
        </div>

    </div>


    <div class="erp-stat-card">

        <div class="erp-stat-card-icon">
            <i class="bi bi-person-check"></i>
        </div>

        <div>
            <div class="erp-stat-card-label">
                Active
            </div>

            <div class="erp-stat-card-value">
                {{ number_format($stats['active']) }}
            </div>
        </div>

    </div>


    <div class="erp-stat-card">

        <div class="erp-stat-card-icon">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <div class="erp-stat-card-label">
                Companies
            </div>

            <div class="erp-stat-card-value">
                {{ number_format($stats['companies']) }}
            </div>
        </div>

    </div>


    <div class="erp-stat-card">

        <div class="erp-stat-card-icon">
            <i class="bi bi-person-dash"></i>
        </div>

        <div>
            <div class="erp-stat-card-label">
                Inactive
            </div>

            <div class="erp-stat-card-value">
                {{ number_format($stats['inactive']) }}
            </div>
        </div>

    </div>

</div>


{{-- Customer Table --}}
<div class="card">

    <div class="card-header">

        <div>
            <div class="card-title">
                Customer Directory
            </div>

            <div class="card-subtitle">
                Search and manage customer records.
            </div>
        </div>

    </div>


    <div class="card-body">

        {{-- Filters --}}
        <form
            method="GET"
            action="{{ route('crm.customers.index') }}"
            class="customer-filters"
        >

            <div class="filter-search">

                <label class="form-label">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Customer name, code, email or phone..."
                >

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
                        value="individual"
                        @selected(request('type') === 'individual')
                    >
                        Individual
                    </option>

                    <option
                        value="company"
                        @selected(request('type') === 'company')
                    >
                        Company
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
                    <i class="bi bi-search me-1"></i>
                    Search
                </button>

                <a
                    href="{{ route('crm.customers.index') }}"
                    class="btn btn-light"
                >
                    Reset
                </a>

            </div>

        </form>


        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>
                            Customer
                        </th>

                        <th>
                            Type
                        </th>

                        <th>
                            Contact
                        </th>

                        <th>
                            Location
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

                    @forelse($customers as $customer)

                        <tr>

                            <td>

                                <div class="customer-table-name">

                                    <a
                                        href="{{ route('crm.customers.show', $customer) }}"
                                        class="customer-name-link"
                                    >
                                        {{ $customer->display_name }}
                                    </a>

                                    <div class="customer-code">
                                        {{ $customer->customer_code }}
                                    </div>

                                </div>

                            </td>


                            <td>

                                @if($customer->customer_type === 'company')

                                    <span class="badge bg-primary-subtle text-primary">
                                        Company
                                    </span>

                                @else

                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Individual
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($customer->email)
                                    <div>
                                        <i class="bi bi-envelope me-1 text-muted"></i>
                                        {{ $customer->email }}
                                    </div>
                                @endif

                                @if($customer->mobile ?: $customer->phone)

                                    <div class="text-muted small">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $customer->mobile ?: $customer->phone }}
                                    </div>

                                @endif

                            </td>


                            <td>

                                @if($customer->city || $customer->province)

                                    {{ implode(', ', array_filter([
                                        $customer->city,
                                        $customer->province
                                    ])) }}

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($customer->status === 'active')

                                    <span class="badge bg-success-subtle text-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td class="text-end">

                                <div class="btn-group">

                                    <a
                                        href="{{ route('crm.customers.show', $customer) }}"
                                        class="btn btn-sm btn-light"
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a
                                        href="{{ route('crm.customers.edit', $customer) }}"
                                        class="btn btn-sm btn-light"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('crm.customers.destroy', $customer) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this customer?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-light text-danger"
                                            title="Delete"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

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
                                        <i class="bi bi-people"></i>
                                    </div>

                                    <h5>
                                        No customers found
                                    </h5>

                                    <p>
                                        Start by adding your first customer.
                                    </p>

                                    <a
                                        href="{{ route('crm.customers.create') }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="bi bi-plus-lg me-1"></i>
                                        Add Customer
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($customers->hasPages())

            <div class="mt-4">

                {{ $customers->links() }}

            </div>

        @endif

    </div>

</div>

@endsection