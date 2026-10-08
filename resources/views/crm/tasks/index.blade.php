@extends('layouts.admin')

@section('title', 'Tasks')

@section('page-title', 'Tasks')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Tasks
            </h1>

            <p class="page-subtitle">
                Manage customer follow-ups,
                appointments, site visits and
                other CRM work.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.tasks.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Task
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
     STATISTICS
========================================================== --}}

<div class="row g-3 mb-4">

    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-check2-square"></i>
            </div>

            <div>
                <div class="crm-stat-label">
                    Total Tasks
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['total']) }}
                </div>
            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <div class="crm-stat-label">
                    Pending
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['pending']) }}
                </div>
            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>

            <div>
                <div class="crm-stat-label">
                    In Progress
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['in_progress']) }}
                </div>
            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <div class="crm-stat-label">
                    Completed
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['completed']) }}
                </div>
            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl">

        <div class="crm-stat-card">

            <div class="crm-stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <div class="crm-stat-label">
                    Overdue
                </div>

                <div class="crm-stat-value">
                    {{ number_format($stats['overdue']) }}
                </div>
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================== --}}

<div class="card mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="task-filters">

                <div class="filter-search">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search task, customer or contact..."
                    >

                </div>


                <div>

                    <label class="form-label">
                        Customer
                    </label>

                    <select
                        name="customer_id"
                        class="form-select"
                    >

                        <option value="">
                            All Customers
                        </option>

                        @foreach($customers as $customer)

                            <option
                                value="{{ $customer->id }}"
                                @selected(
                                    request('customer_id') == $customer->id
                                )
                            >
                                {{ $customer->display_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label class="form-label">
                        Type
                    </label>

                    <select
                        name="task_type"
                        class="form-select"
                    >

                        <option value="">
                            All Types
                        </option>

                        @foreach([
                            'follow_up' => 'Follow Up',
                            'call' => 'Call',
                            'email' => 'Email',
                            'meeting' => 'Meeting',
                            'site_visit' => 'Site Visit',
                            'estimate' => 'Estimate',
                            'proposal' => 'Proposal',
                            'document' => 'Document',
                            'other' => 'Other',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('task_type') === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

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
                            All Statuses
                        </option>

                        <option
                            value="pending"
                            @selected(request('status') === 'pending')
                        >
                            Pending
                        </option>

                        <option
                            value="in_progress"
                            @selected(request('status') === 'in_progress')
                        >
                            In Progress
                        </option>

                        <option
                            value="completed"
                            @selected(request('status') === 'completed')
                        >
                            Completed
                        </option>

                        <option
                            value="cancelled"
                            @selected(request('status') === 'cancelled')
                        >
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
                            All Priorities
                        </option>

                        @foreach([
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                            'urgent' => 'Urgent',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('priority') === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route('crm.tasks.index') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     TABLE
========================================================== --}}

<div class="card">

    <div class="table-responsive">

        <table class="table crm-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Task</th>

                    <th>Customer</th>

                    <th>Property</th>

                    <th>Type</th>

                    <th>Due</th>

                    <th>Priority</th>

                    <th>Status</th>

                    <th class="text-end">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($tasks as $task)

                    <tr>

                        <td>

                            <div class="task-table-name">

                                <a
                                    href="{{ route(
                                        'crm.tasks.show',
                                        $task
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $task->subject }}
                                </a>

                                <div class="customer-code">
                                    {{ $task->task_code }}
                                </div>

                            </div>

                        </td>


                        <td>

                            @if($task->customer)

                                <a
                                    href="{{ route(
                                        'crm.customers.show',
                                        $task->customer
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $task->customer->display_name }}
                                </a>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($task->property)

                                <a
                                    href="{{ route(
                                        'crm.properties.show',
                                        $task->property
                                    ) }}"
                                    class="customer-name-link"
                                >
                                    {{ $task->property->name }}
                                </a>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            <span class="task-type-badge">

                                <i class="bi {{ $task->task_type_icon }}"></i>

                                {{ $task->task_type_label }}

                            </span>

                        </td>


                        <td>

                            @if($task->due_at)

                                <div
                                    class="{{ $task->is_overdue
                                        ? 'task-overdue'
                                        : 'task-date' }}"
                                >
                                    {{ $task->due_at->format('M d, Y') }}
                                </div>

                                <div class="task-time">
                                    {{ $task->due_at->format('h:i A') }}
                                </div>

                                @if($task->is_overdue)

                                    <span class="task-overdue-label">
                                        Overdue
                                    </span>

                                @endif

                            @else

                                <span class="text-muted">
                                    No due date
                                </span>

                            @endif

                        </td>


                        <td>

                            <span
                                class="task-priority task-priority-{{ $task->priority }}"
                            >
                                {{ $task->priority_label }}
                            </span>

                        </td>


                        <td>

                            @switch($task->status)

                                @case('pending')

                                    <span class="status-badge status-pending">
                                        Pending
                                    </span>

                                    @break

                                @case('in_progress')

                                    <span class="status-badge status-in-progress">
                                        In Progress
                                    </span>

                                    @break

                                @case('completed')

                                    <span class="status-badge status-completed">
                                        Completed
                                    </span>

                                    @break

                                @case('cancelled')

                                    <span class="status-badge status-cancelled">
                                        Cancelled
                                    </span>

                                    @break

                            @endswitch

                        </td>


                        <td class="text-end">

                            <a
                                href="{{ route(
                                    'crm.tasks.show',
                                    $task
                                ) }}"
                                class="btn btn-sm btn-light"
                                title="View"
                            >
                                <i class="bi bi-eye"></i>
                            </a>

                            <a
                                href="{{ route(
                                    'crm.tasks.edit',
                                    $task
                                ) }}"
                                class="btn btn-sm btn-light"
                                title="Edit"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <div class="text-muted">

                                <i
                                    class="bi bi-check2-square fs-3 d-block mb-2"
                                ></i>

                                No tasks found.

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($tasks->hasPages())

        <div class="card-footer">

            {{ $tasks->links() }}

        </div>

    @endif

</div>

@endsection