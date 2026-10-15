
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $opportunity->name }}</h1>
            <p class="text-muted mb-0">
                {{ $opportunity->opportunity_number }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('crm.opportunities.index') }}"
               class="btn btn-outline-secondary">Back</a>
            <a href="{{ route('crm.opportunities.edit', $opportunity) }}"
               class="btn btn-primary">Edit Opportunity</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5>Opportunity Information</h5>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted small">Stage</div>
                            <div class="fw-semibold">
                                {{ str($opportunity->stage)->replace('_', ' ')->title() }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Assigned To</div>
                            <div>{{ $opportunity->assignee?->name ?? 'Unassigned' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Lead</div>
                            <div>
                                @if($opportunity->lead)
                                    <a href="{{ route('crm.leads.show', $opportunity->lead) }}">
                                        {{ $opportunity->lead->full_name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Customer</div>
                            <div>
                                {{ $opportunity->customer?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Expected Close</div>
                            <div>
                                {{ $opportunity->expected_close_date?->format('M d, Y') ?? 'Not set' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Closed Date</div>
                            <div>
                                {{ $opportunity->closed_at?->format('M d, Y') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small">Description</div>
                            <p class="mb-0">
                                {{ $opportunity->description ?: 'No description provided.' }}
                            </p>
                        </div>

                        @if($opportunity->loss_reason)
                            <div class="col-12">
                                <div class="text-muted small">Reason for Loss</div>
                                {{ $opportunity->loss_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="text-muted">Estimated Value</div>
                    <div class="fs-3 fw-bold">
                        {{ number_format($opportunity->estimated_value, 2) }}
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="text-muted">Win Probability</div>
                    <div class="fs-3 fw-bold">
                        {{ $opportunity->probability }}%
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Weighted Forecast</div>
                    <div class="fs-3 fw-bold text-primary">
                        {{ number_format($opportunity->weighted_value, 2) }}
                    </div>
                    <div class="small text-muted">
                        Estimated value × probability
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
