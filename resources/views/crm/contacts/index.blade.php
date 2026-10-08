@extends('layouts.admin')

@section('title', 'Contacts')

@section('page-title', 'Contacts')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Contacts
            </h1>

            <p class="page-subtitle">
                Manage customer contacts and relationship information.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.contacts.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-person-plus me-1"></i>
                Add Contact
            </a>

        </div>

    </div>

</div>


{{-- SUCCESS MESSAGE --}}
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
            <i class="bi bi-person-lines-fill"></i>
        </div>

        <div>
            <div class="stat-card-label">
                Total Contacts
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['total']) }}
            </div>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-person-check"></i>
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
            <i class="bi bi-star"></i>
        </div>

        <div>
            <div class="stat-card-label">
                Primary Contacts
            </div>

            <div class="stat-card-value">
                {{ number_format($stats['primary']) }}
            </div>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-card-icon">
            <i class="bi bi-person-x"></i>
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
    action="{{ route('crm.contacts.index') }}"
    class="customer-filters contact-filters"
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
            placeholder="Name, email, phone, customer..."
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
                    ({{ $customer->customer_code }})
                </option>

            @endforeach

        </select>

    </div>


    <div>

        <label
            for="type"
            class="form-label"
        >
            Type
        </label>

        <select
            name="type"
            id="type"
            class="form-select"
        >

            <option value="">
                All Types
            </option>

            <option value="primary" {{ request('type') === 'primary' ? 'selected' : '' }}>
                Primary
            </option>

            <option value="billing" {{ request('type') === 'billing' ? 'selected' : '' }}>
                Billing
            </option>

            <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>
                Project
            </option>

            <option value="site" {{ request('type') === 'site' ? 'selected' : '' }}>
                Site
            </option>

            <option value="technical" {{ request('type') === 'technical' ? 'selected' : '' }}>
                Technical
            </option>

            <option value="emergency" {{ request('type') === 'emergency' ? 'selected' : '' }}>
                Emergency
            </option>

            <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>
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

            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
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
            href="{{ route('crm.contacts.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </div>

</form>


{{-- CONTACT TABLE --}}
<div class="card">

    <div class="card-header">

        <div>

            <div class="card-title">
                Customer Contacts
            </div>

            <div class="card-subtitle">
                {{ $contacts->total() }} contact(s)
            </div>

        </div>

    </div>


    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>
                        Contact
                    </th>

                    <th>
                        Customer
                    </th>

                    <th>
                        Role
                    </th>

                    <th>
                        Contact Information
                    </th>

                    <th>
                        Type
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

                @forelse($contacts as $contact)

                    <tr>

                        <td>

                            <div class="contact-table-name">

                                <a
                                    href="{{ route('crm.contacts.show', $contact) }}"
                                    class="customer-name-link"
                                >
                                    {{ $contact->display_name }}
                                </a>

                                @if($contact->is_primary)

                                    <span class="contact-primary-badge">
                                        <i class="bi bi-star-fill"></i>
                                        Primary
                                    </span>

                                @endif

                            </div>

                        </td>


                        <td>

                            <a
                                href="{{ route('crm.customers.show', $contact->customer) }}"
                                class="customer-name-link"
                            >
                                {{ $contact->customer->display_name }}
                            </a>

                            <div class="customer-code">
                                {{ $contact->customer->customer_code }}
                            </div>

                        </td>


                        <td>

                            @if($contact->job_title)

                                <div class="contact-job-title">
                                    {{ $contact->job_title }}
                                </div>

                            @endif

                            @if($contact->department)

                                <div class="contact-department">
                                    {{ $contact->department }}
                                </div>

                            @endif

                            @if(!$contact->job_title && !$contact->department)
                                <span class="text-muted">
                                    —
                                </span>
                            @endif

                        </td>


                        <td>

                            @if($contact->email)

                                <div class="contact-info-line">
                                    <i class="bi bi-envelope"></i>
                                    {{ $contact->email }}
                                </div>

                            @endif

                            @if($contact->phone)

                                <div class="contact-info-line">
                                    <i class="bi bi-telephone"></i>
                                    {{ $contact->phone }}
                                </div>

                            @elseif($contact->mobile)

                                <div class="contact-info-line">
                                    <i class="bi bi-phone"></i>
                                    {{ $contact->mobile }}
                                </div>

                            @endif

                        </td>


                        <td>

                            <span class="crm-badge">
                                {{ ucfirst($contact->contact_type) }}
                            </span>

                        </td>


                        <td>

                            @if($contact->status === 'active')

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
                                    href="{{ route('crm.contacts.show', $contact) }}"
                                    class="btn btn-sm btn-light"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route('crm.contacts.edit', $contact) }}"
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
                            colspan="7"
                            class="text-center py-5"
                        >

                            <div class="empty-state">

                                <i class="bi bi-person-lines-fill"></i>

                                <h5>
                                    No contacts found
                                </h5>

                                <p>
                                    Create your first customer contact.
                                </p>

                                <a
                                    href="{{ route('crm.contacts.create') }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-person-plus me-1"></i>
                                    Add Contact
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($contacts->hasPages())

        <div class="card-footer">

            {{ $contacts->links() }}

        </div>

    @endif

</div>

@endsection