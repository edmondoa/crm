@extends('layouts.admin')

@section('title', 'Activities')

@section('page-title', 'Activities')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Activities
            </h1>

            <p class="page-subtitle">
                Track customer interactions, meetings, calls, visits, and CRM history.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.activities.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Activity
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
     STATS
========================================================= --}}

<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-activity"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Total Activities
            </div>

            <div class="stat-value">
                {{ number_format($stats['total']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-calendar-event"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Planned
            </div>

            <div class="stat-value">
                {{ number_format($stats['planned']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                Completed
            </div>

            <div class="stat-value">
                {{ number_format($stats['completed']) }}
            </div>

        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            <i class="bi bi-exclamation-circle"></i>
        </div>

        <div class="stat-content">

            <div class="stat-label">
                High Priority
            </div>

            <div class="stat-value">
                {{ number_format($stats['high_priority']) }}
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================= --}}

<form
    method="GET"
    action="{{ route('crm.activities.index') }}"
    class="activity-filters"
>

    <div class="filter-search">

        <label class="form-label">
            Search
        </label>

        <input
            type="search"
            name="search"
            class="form-control"
            value="{{ request('search') }}"
            placeholder="Subject, customer, contact, property..."
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

            <option value="call" @selected(request('type') === 'call')>
                Call
            </option>

            <option value="email" @selected(request('type') === 'email')>
                Email
            </option>

            <option value="meeting" @selected(request('type') === 'meeting')>
                Meeting
            </option>

            <option value="site_visit" @selected(request('type') === 'site_visit')>
                Site Visit
            </option>

            <option value="note" @selected(request('type') === 'note')>
                Note
            </option>

            <option value="sms" @selected(request('type') === 'sms')>
                SMS
            </option>

            <option value="other" @selected(request('type') === 'other')>
                Other
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

            <option value="planned" @selected(request('status') === 'planned')>
                Planned
            </option>

            <option value="completed" @selected(request('status') === 'completed')>
                Completed
            </option>

            <option value="cancelled" @selected(request('status') === 'cancelled')>
                Cancelled
            </option>

        </select>

    </div>


    <div>

        <label class="form-label">
            Priority
        </label>

        <select
            name="priority"
            class="form-select"
        >

            <option value="">
                All Priority
            </option>

            <option value="low" @selected(request('priority') === 'low')>
                Low
            </option>

            <option value="normal" @selected(request('priority') === 'normal')>
                Normal
            </option>

            <option value="high" @selected(request('priority') === 'high')>
                High
            </option>

        </select>

    </div>


    <div class="filter-actions">

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-search"></i>
        </button>

        <a
            href="{{ route('crm.activities.index') }}"
            class="btn btn-light"
        >
            Reset
        </a>

    </div>

</form>


{{-- =========================================================
     TABLE
========================================================= --}}

<div class="card">

    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Activity</th>

                    <th>Customer</th>

                    <th>Property</th>

                    <th>Scheduled</th>

                    <th>Status</th>

                    <th>Priority</th>

                    <th class="text-end">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($activities as $activity)

                    <tr>

                        <td>

                            <div class="activity-table-title">

                                <span class="activity-type-icon activity-type-{{ $activity->activity_type }}">
                                    <i class="bi bi-{{ match($activity->activity_type) {
                                        'call' => 'telephone',
                                        'email' => 'envelope',
                                        'meeting' => 'people',
                                        'site_visit' => 'geo-alt',
                                        'note' => 'journal-text',
                                        'sms' => 'chat',
                                        default => 'activity',
                                    } }}"></i>
                                </span>

                                <div>

                                    <a
                                        href="{{ route(
                                            'crm.activities.show',
                                            $activity
                                        ) }}"
                                        class="customer-name-link"
                                    >
                                        {{ $activity->subject }}
                                    </a>

                                    <div class="customer-code">
                                        {{ $activity->activity_type_label }}
                                    </div>

                                </div>

                            </div>

                        </td>


                        <td>

                            @if($activity->customer)

                                <a
                                    href="{{ route(
                                        'crm.customers.show',
                                        $activity->customer
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $activity->customer->display_name }}
                                </a>

                                <div class="customer-code">
                                    {{ $activity->customer->customer_code }}
                                </div>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($activity->property)

                                <a
                                    href="{{ route(
                                        'crm.properties.show',
                                        $activity->property
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $activity->property->name }}
                                </a>

                                @if($activity->propertyLocation)

                                    <div class="activity-location">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ $activity->propertyLocation->location_name }}
                                    </div>

                                @endif

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($activity->scheduled_at)

                                <div class="activity-date">
                                    {{ $activity->scheduled_at->format('M d, Y') }}
                                </div>

                                <div class="activity-time">
                                    {{ $activity->scheduled_at->format('g:i A') }}
                                </div>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($activity->status === 'planned')

                                <span class="status-badge status-planned">
                                    Planned
                                </span>

                            @elseif($activity->status === 'completed')

                                <span class="status-badge status-active">
                                    Completed
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Cancelled
                                </span>

                            @endif

                        </td>


                        <td>

                            <span class="activity-priority activity-priority-{{ $activity->priority }}">
                                {{ $activity->priority_label }}
                            </span>

                        </td>


                        <td class="text-end">

                            <div class="btn-group">

                                <a
                                    href="{{ route(
                                        'crm.activities.show',
                                        $activity
                                    ) }}"
                                    class="btn btn-sm btn-light"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route(
                                        'crm.activities.edit',
                                        $activity
                                    ) }}"
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

                                <div class="empty-state-icon">
                                    <i class="bi bi-activity"></i>
                                </div>

                                <h5>
                                    No activities found
                                </h5>

                                <p>
                                    Record your first CRM activity.
                                </p>

                                <a
                                    href="{{ route(
                                        'crm.activities.create'
                                    ) }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add Activity
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($activities->hasPages())

        <div class="card-footer">

            {{ $activities->links() }}

        </div>

    @endif

</div>

@endsection