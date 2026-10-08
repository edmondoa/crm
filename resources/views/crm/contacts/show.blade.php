@extends('layouts.admin')

@section('title', $contact->display_name)

@section('page-title', 'Contact Details')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Contact Details
            </h1>

            <p class="page-subtitle">
                View customer contact and relationship information.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.contacts.index') }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Contacts
            </a>

            <a
                href="{{ route('crm.contacts.edit', $contact) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit Contact
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


<div class="row g-4">

    {{-- PROFILE --}}
    <div class="col-lg-4">

        <div class="card h-100">

            <div class="card-body">

                <div class="customer-profile">

                    <div class="customer-profile-avatar">
                        {{ strtoupper(substr($contact->first_name, 0, 1)) }}
                    </div>

                    <h2 class="customer-profile-name">
                        {{ $contact->display_name }}
                    </h2>

                    @if($contact->job_title)

                        <div class="contact-profile-title">
                            {{ $contact->job_title }}
                        </div>

                    @endif

                    <div class="customer-profile-code">
                        {{ ucfirst($contact->contact_type) }} Contact
                    </div>

                </div>


                <div class="customer-detail-list">

                    <div class="customer-detail-item">

                        <span>
                            Customer
                        </span>

                        <strong>

                            <a
                                href="{{ route('crm.customers.show', $contact->customer) }}"
                            >
                                {{ $contact->customer->display_name }}
                            </a>

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Status
                        </span>

                        <strong>

                            @if($contact->status === 'active')

                                <span class="status-badge status-active">
                                    Active
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Inactive
                                </span>

                            @endif

                        </strong>

                    </div>


                    <div class="customer-detail-item">

                        <span>
                            Primary
                        </span>

                        <strong>

                            @if($contact->is_primary)

                                <span class="contact-primary-badge">
                                    <i class="bi bi-star-fill"></i>
                                    Yes
                                </span>

                            @else

                                No

                            @endif

                        </strong>

                    </div>


                    @if($contact->department)

                        <div class="customer-detail-item">

                            <span>
                                Department
                            </span>

                            <strong>
                                {{ $contact->department }}
                            </strong>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- DETAILS --}}
    <div class="col-lg-8">

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Contact Information
                </div>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Email Address
                        </div>

                        <div class="detail-value">

                            @if($contact->email)

                                <a href="mailto:{{ $contact->email }}">
                                    {{ $contact->email }}
                                </a>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Phone
                        </div>

                        <div class="detail-value">
                            {{ $contact->phone ?: '—' }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="detail-label">
                            Mobile
                        </div>

                        <div class="detail-value">
                            {{ $contact->mobile ?: '—' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Preferred Contact Method
                        </div>

                        <div class="detail-value">

                            {{ $contact->preferred_contact_method
                                ? ucfirst($contact->preferred_contact_method)
                                : '—' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ADDRESS --}}
        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    Address
                </div>

            </div>

            <div class="card-body">

                @if($contact->full_address)

                    <div class="customer-address">

                        <i class="bi bi-geo-alt me-2"></i>

                        <div>

                            @if($contact->address_line_1)
                                <div>
                                    {{ $contact->address_line_1 }}
                                </div>
                            @endif

                            @if($contact->address_line_2)
                                <div>
                                    {{ $contact->address_line_2 }}
                                </div>
                            @endif

                            @if($contact->city || $contact->state || $contact->zip_code)

                                <div>

                                    {{ $contact->city }}

                                    @if($contact->city && $contact->state)
                                        ,
                                    @endif

                                    {{ $contact->state }}

                                    {{ $contact->zip_code }}

                                </div>

                            @endif

                            @if($contact->country)

                                <div>
                                    {{ $contact->country }}
                                </div>

                            @endif

                        </div>

                    </div>

                @else

                    <span class="text-muted">
                        No address provided.
                    </span>

                @endif

            </div>

        </div>


        {{-- NOTES --}}
        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Notes
                </div>

            </div>

            <div class="card-body">

                @if($contact->notes)

                    <div class="customer-notes">
                        {{ $contact->notes }}
                    </div>

                @else

                    <span class="text-muted">
                        No notes available.
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- DELETE --}}
<div class="card mt-4">

    <div class="card-body">

        <div class="danger-zone">

            <div>

                <div class="danger-zone-title">
                    Delete Contact
                </div>

                <div class="danger-zone-description">
                    Permanently remove this contact from the CRM.
                </div>

            </div>

            <form
                method="POST"
                action="{{ route('crm.contacts.destroy', $contact) }}"
                onsubmit="return confirm('Are you sure you want to delete this contact?');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Delete Contact
                </button>

            </form>

        </div>

    </div>

</div>

@endsection