@extends('layouts.admin')

@section('title', 'Estimates')

@section('page-title', 'Estimates')

@section('content')

<div class="page-header">
    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Estimates
            </h1>

            <p class="page-subtitle">
                Manage job estimates and quotations.
            </p>
        </div>

        <div>
            <a
                href="{{ route('crm.estimates.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                New Estimate
            </a>
        </div>

    </div>
</div>


@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif


<div class="erp-card">

    <div class="table-responsive">

        <table class="table erp-table align-middle">

            <thead>
                <tr>
                    <th>Estimate #</th>
                    <th>Customer</th>
                    <th>Job</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($estimates as $estimate)

                    <tr>

                        <td>
                            <a
                                href="{{ route('crm.estimates.show', $estimate) }}"
                                class="fw-semibold"
                            >
                                {{ $estimate->estimate_number }}
                            </a>
                        </td>

                        <td>
                            {{ $estimate->customer->display_name ?? '—' }}
                        </td>

                        <td>
                            {{ $estimate->job->job_code ?? '—' }}
                        </td>

                        <td>
                            {{ $estimate->estimate_date?->format('M d, Y') }}
                        </td>

                        <td>
                            <span class="badge
                                @if($estimate->status === 'accepted')
                                    bg-success
                                @elseif($estimate->status === 'rejected')
                                    bg-danger
                                @elseif($estimate->status === 'sent')
                                    bg-primary
                                @elseif($estimate->status === 'converted')
                                    bg-dark
                                @else
                                    bg-secondary
                                @endif
                            ">
                                {{ ucfirst($estimate->status) }}
                            </span>
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($estimate->grand_total, 2) }}
                        </td>

                        <td class="text-end">

                            <a
                                href="{{ route('crm.estimates.show', $estimate) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                View
                            </a>

                            <a
                                href="{{ route('crm.estimates.edit', $estimate) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                Edit
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="7"
                            class="text-center py-5 text-muted"
                        >
                            No estimates found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">
        {{ $estimates->links() }}
    </div>

</div>

@endsection