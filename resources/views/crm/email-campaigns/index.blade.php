@extends('layouts.admin')

@section('title', 'Email Campaigns')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Email Campaigns</h1>
            <p class="text-muted mb-0">Plan campaign content, audiences, and dates.</p>
        </div>
        <a href="{{ route('crm.email-campaigns.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New campaign
        </a>
    </div>

    @include('crm.email-templates.partials.alerts')

    <div class="alert alert-info d-flex gap-2 align-items-start" role="note">
        <i class="bi bi-info-circle mt-1"></i>
        <div>Campaigns are planning records only. A scheduled date does not send email automatically.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="small text-muted">All campaigns</div>
                <div class="fs-3 fw-semibold">{{ $counts['all'] }}</div>
            </div></div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="small text-muted">Drafts</div>
                <div class="fs-3 fw-semibold">{{ $counts['draft'] }}</div>
            </div></div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="small text-muted">Scheduled plans</div>
                <div class="fs-3 fw-semibold">{{ $counts['scheduled'] }}</div>
            </div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="visually-hidden" for="campaign-search">Search campaigns</label>
                    <input id="campaign-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search campaigns or audiences">
                </div>
                <div class="col-md-3">
                    <label class="visually-hidden" for="campaign-status">Filter by status</label>
                    <select id="campaign-status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="scheduled" @selected(request('status') === 'scheduled')>Scheduled</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary">Filter</button>
                    <a href="{{ route('crm.email-campaigns.index') }}" class="btn btn-link">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Campaign</th><th>Template</th><th>Target</th><th>Status</th><th>Planned date</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $campaign->name }}</div>
                                <small class="text-muted">{{ $campaign->audience_description ?: 'Audience not specified' }}</small>
                            </td>
                            <td>{{ $campaign->emailTemplate?->name ?? 'Template removed' }}</td>
                            <td><span class="badge text-bg-light">{{ $campaign->target_label }}</span></td>
                            <td>
                                @php
                                    $statusClass = match ($campaign->status) {
                                        'scheduled' => 'text-bg-primary',
                                        'cancelled' => 'text-bg-secondary',
                                        default => 'text-bg-light',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($campaign->status) }}</span>
                            </td>
                            <td>{{ $campaign->scheduled_at?->format('M j, Y g:i A') ?? 'Not set' }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('crm.email-campaigns.edit', $campaign) }}" aria-label="Edit {{ $campaign->name }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('crm.email-campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign plan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" aria-label="Delete {{ $campaign->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-megaphone fs-2 text-muted"></i>
                                <p class="fw-semibold mt-2 mb-1">No campaigns yet</p>
                                <p class="text-muted">Create a campaign plan and connect it to an email template.</p>
                                <a class="btn btn-primary" href="{{ route('crm.email-campaigns.create') }}">Create campaign</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection
