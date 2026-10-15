
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Leads</h1>
            <p class="text-muted mb-0">Manage prospective customers.</p>
        </div>
        <a href="{{ route('crm.leads.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Lead
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="search" name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search name, company, email, phone">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach(['new','contacted','qualified','unqualified'] as $status)
                            <option value="{{ $status }}"
                                @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('crm.leads.index') }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Lead</th>
                            <th>Company</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Assigned To</th>
                            <th>Follow-up</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>
                                <a href="{{ route('crm.leads.show', $lead) }}"
                                   class="fw-semibold text-decoration-none">
                                    {{ $lead->full_name }}
                                </a>
                                <div class="small text-muted">
                                    {{ $lead->lead_number }}
                                </div>
                                <div class="small text-muted">
                                    {{ $lead->email }}
                                </div>
                            </td>
                            <td>{{ $lead->company_name ?: '—' }}</td>
                            <td>{{ str($lead->source)->replace('_', ' ')->title() }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($lead->rating) }}</td>
                            <td>{{ $lead->assignee?->name ?? 'Unassigned' }}</td>
                            <td>
                                {{ $lead->follow_up_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('crm.leads.edit', $lead) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        </div>
    </div>
</div>
@endsection
