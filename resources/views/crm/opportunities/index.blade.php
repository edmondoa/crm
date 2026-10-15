
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Opportunities</h1>
            <p class="text-muted mb-0">Track construction sales opportunities.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('crm.opportunities.pipeline') }}"
               class="btn btn-outline-primary">Pipeline</a>
            <a href="{{ route('crm.opportunities.create') }}"
               class="btn btn-primary">New Opportunity</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input name="search" class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Search opportunity">
                </div>
                <div class="col-md-4">
                    <select name="stage" class="form-select">
                        <option value="">All stages</option>
                        @foreach([
                            'qualification','needs_analysis','proposal',
                            'negotiation','won','lost'
                        ] as $stage)
                            <option value="{{ $stage }}"
                                @selected(request('stage') === $stage)>
                                {{ str($stage)->replace('_', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Opportunity</th>
                            <th>Lead / Customer</th>
                            <th>Stage</th>
                            <th class="text-end">Value</th>
                            <th class="text-end">Probability</th>
                            <th class="text-end">Weighted Value</th>
                            <th>Expected Close</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td>
                                <a href="{{ route('crm.opportunities.show', $opportunity) }}">
                                    {{ $opportunity->name }}
                                </a>
                                <div class="small text-muted">
                                    {{ $opportunity->opportunity_number }}
                                </div>
                            </td>
                            <td>
                                {{ $opportunity->customer?->name
                                    ?? $opportunity->lead?->full_name
                                    ?? '—' }}
                            </td>
                            <td>
                                {{ str($opportunity->stage)->replace('_', ' ')->title() }}
                            </td>
                            <td class="text-end">
                                {{ number_format($opportunity->estimated_value, 2) }}
                            </td>
                            <td class="text-end">
                                {{ $opportunity->probability }}%
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($opportunity->weighted_value, 2) }}
                            </td>
                            <td>
                                {{ $opportunity->expected_close_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('crm.opportunities.edit', $opportunity) }}"
                                   class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No opportunities found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $opportunities->links() }}
        </div>
    </div>
</div>
@endsection
