
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $lead->full_name }}</h1>
            <p class="text-muted mb-0">
                {{ $lead->lead_number }} ·
                {{ $lead->company_name ?: 'Individual prospect' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('crm.leads.edit', $lead) }}"
               class="btn btn-outline-secondary">Edit</a>

            @if($lead->status === 'qualified' && !$lead->converted_customer_id)
                <form method="POST"
                      action="{{ route('crm.leads.convert', $lead) }}"
                      onsubmit="return confirm('Convert this lead into a customer?')">
                    @csrf
                    <button class="btn btn-primary">Convert to Customer</button>
                </form>
            @elseif($lead->convertedCustomer)
                <a href="{{ route('customers.show', $lead->convertedCustomer) }}"
                   class="btn btn-success">View Customer</a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            'Email' => $lead->email,
            'Phone' => $lead->phone,
            'Source' => str($lead->source)->replace('_', ' ')->title(),
            'Status' => ucfirst($lead->status),
            'Rating' => ucfirst($lead->rating),
            'Assigned To' => $lead->assignee?->name ?? 'Unassigned',
            'Follow-up' => $lead->follow_up_at?->format('M d, Y') ?? '—',
        ] as $label => $value)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fw-semibold">{{ $value ?: '—' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5>Description / Requirements</h5>
            <p class="mb-0">{{ $lead->description ?: 'No description provided.' }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Opportunities</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Opportunity</th>
                        <th>Stage</th>
                        <th class="text-end">Estimated Value</th>
                        <th>Expected Close</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($lead->opportunities as $opportunity)
                    <tr>
                        <td>{{ $opportunity->name }}</td>
                        <td>{{ str($opportunity->stage)->replace('_', ' ')->title() }}</td>
                        <td class="text-end">
                            {{ number_format($opportunity->estimated_value, 2) }}
                        </td>
                        <td>{{ $opportunity->expected_close_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('opportunities.show', $opportunity) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            No opportunities linked to this lead.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Activity History</h5>
        </div>
        <div class="card-body">
            @forelse($lead->activities->sortByDesc('created_at') as $activity)
                <div class="border-bottom pb-3 mb-3">
                    <div class="fw-semibold">{{ $activity->subject }}</div>
                    <div class="small text-muted">
                        {{ str($activity->type)->title() }} ·
                        {{ $activity->user?->name ?? 'Unknown user' }} ·
                        {{ $activity->created_at->format('M d, Y h:i A') }}
                    </div>
                    <p class="mb-0 mt-2">{{ $activity->description }}</p>
                    @if($activity->due_at)
                        <div class="small text-muted">
                            Due: {{ $activity->due_at->format('M d, Y h:i A') }}
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">No activities recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
