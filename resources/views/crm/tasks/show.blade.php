@extends('layouts.admin')

@section('title', 'Task Details')

@section('page-title', 'Task Details')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                {{ $task->subject }}
            </h1>

            <p class="page-subtitle">
                {{ $task->task_code }}
            </p>

        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('crm.tasks.edit', $task) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route('crm.tasks.index') }}"
                class="btn btn-light"
            >
                Back
            </a>

        </div>

    </div>

</div>


<div class="row g-4">

    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <div class="col-lg-8">

        <div class="card mb-4">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Task Information
                    </div>

                    <div class="card-subtitle">
                        Details and task status.
                    </div>
                </div>

            </div>

            <div class="card-body">

                <div class="task-profile">

                    <div class="task-profile-icon">
                        <i class="bi {{ $task->task_type_icon }}"></i>
                    </div>

                    <div>

                        <h2 class="task-profile-name">
                            {{ $task->subject }}
                        </h2>

                        <div class="task-profile-code">
                            {{ $task->task_code }}
                        </div>

                    </div>

                </div>


                <div class="row g-4 mt-1">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Task Type
                        </div>

                        <div class="detail-value">
                            {{ $task->task_type_label }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Priority
                        </div>

                        <div class="detail-value">
                            {{ $task->priority_label }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Status
                        </div>

                        <div class="detail-value">

                            {{ $task->status_label }}

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Due Date
                        </div>

                        <div class="detail-value">

                            @if($task->due_at)

                                {{ $task->due_at->format(
                                    'M d, Y h:i A'
                                ) }}

                            @else

                                <span class="text-muted">
                                    No due date
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Completed
                        </div>

                        <div class="detail-value">

                            @if($task->completed_at)

                                {{ $task->completed_at->format(
                                    'M d, Y h:i A'
                                ) }}

                            @else

                                <span class="text-muted">
                                    Not completed
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Outcome
                        </div>

                        <div class="detail-value">

                            {{ $task->outcome ?: '—' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        @if($task->description)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Description
                    </div>

                </div>

                <div class="card-body">

                    <div class="task-description">
                        {!! nl2br(e($task->description)) !!}
                    </div>

                </div>

            </div>

        @endif


        @if($task->notes)

            <div class="card mb-4">

                <div class="card-header">

                    <div class="card-title">
                        Internal Notes
                    </div>

                </div>

                <div class="card-body">

                    <div class="task-description">
                        {!! nl2br(e($task->notes)) !!}
                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- =====================================================
         CRM CONTEXT
    ====================================================== --}}

    <div class="col-lg-4">

        <div class="card mb-4">

            <div class="card-header">

                <div class="card-title">
                    CRM Context
                </div>

            </div>

            <div class="card-body">

                {{-- Customer --}}

                <div class="task-context-item">

                    <div class="task-context-icon">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Customer
                        </div>

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

                            <div class="customer-code">
                                {{ $task->customer->customer_code }}
                            </div>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Contact --}}

                @if($task->contact)

                    <div class="task-context-item">

                        <div class="task-context-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Contact
                            </div>

                            <a
                                href="{{ route(
                                    'crm.contacts.show',
                                    $task->contact
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $task->contact->display_name }}
                            </a>

                            @if($task->contact->job_title)

                                <div class="customer-code">
                                    {{ $task->contact->job_title }}
                                </div>

                            @endif

                        </div>

                    </div>

                @endif


                {{-- Property --}}

                @if($task->property)

                    <div class="task-context-item">

                        <div class="task-context-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Property
                            </div>

                            <a
                                href="{{ route(
                                    'crm.properties.show',
                                    $task->property
                                ) }}"
                                class="customer-name-link"
                            >
                                {{ $task->property->name }}
                            </a>

                            <div class="customer-code">
                                {{ $task->property->property_code }}
                            </div>

                        </div>

                    </div>

                @endif


                {{-- Location --}}

                @if($task->propertyLocation)

                    <div class="task-context-item">

                        <div class="task-context-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>

                            <div class="detail-label">
                                Property Location
                            </div>

                            <div class="detail-value">
                                {{ $task->propertyLocation->location_name }}
                            </div>

                            <div class="customer-code">

                                {{
                                    implode(', ', array_filter([
                                        $task->propertyLocation->city,
                                        $task->propertyLocation->state,
                                        $task->propertyLocation->zip_code,
                                    ]))
                                }}

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>


        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Actions
                </div>

            </div>

            <div class="card-body">

                <a
                    href="{{ route(
                        'crm.tasks.edit',
                        $task
                    ) }}"
                    class="btn btn-primary w-100 mb-2"
                >
                    <i class="bi bi-pencil me-1"></i>
                    Edit Task
                </a>
                @if(
                    !in_array($task->status, [
                        'cancelled'
                    ])
                )

                    <a
                        href="{{ route(
                            'crm.jobs.create',
                            [
                                'customer_id' =>
                                    $task->customer_id,

                                'property_id' =>
                                    $task->property_id,
                            ]
                        ) }}"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-briefcase me-1"></i>
                        Create Work Order
                    </a>

                @endif


                <form
                    method="POST"
                    action="{{ route(
                        'crm.tasks.destroy',
                        $task
                    ) }}"
                    onsubmit="return confirm(
                        'Are you sure you want to delete this task?'
                    );"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-outline-danger w-100"
                    >
                        <i class="bi bi-trash me-1"></i>
                        Delete Task
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection