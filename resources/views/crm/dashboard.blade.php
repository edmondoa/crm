@extends('layouts.admin')

@section('title', 'CRM Dashboard')

@section('page-title', 'CRM Dashboard')

@section('content')

<div class="erp-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    <div class="page-header mb-4">

        <div class="page-header-row">

            <div>

                <h1 class="page-title">
                    CRM Dashboard
                </h1>

                <p class="page-subtitle">
                    Manage customers, properties, tasks, jobs and
                    customer activities.
                </p>

            </div>

            <div class="page-actions">

                <button
                    type="button"
                    class="btn btn-primary"
                    disabled
                    title="Available in Phase 2"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    New Customer
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         KPI CARDS
    ========================================================== --}}

    <div class="row g-3 mb-4">

        {{-- Customers --}}
        <div class="col-xl-3 col-md-6">

            <div class="erp-stat-card">
                <a
                    href="{{ route('crm.customers.index') }}"
                    class="crm-module-card"
                >
                    <div class="erp-stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div class="erp-stat-content">

                        <div class="erp-stat-label">
                            Customers
                        </div>

                        <div class="erp-stat-value">
                            {{ number_format($stats['customers']) }}
                        </div>

                        <div class="erp-stat-meta">
                            Total customers
                        </div>

                    </div>
                </a>

            </div>

        </div>


        {{-- Properties --}}
        <div class="col-xl-3 col-md-6">

            <div class="erp-stat-card">

                <div class="erp-stat-icon">
                    <i class="bi bi-buildings"></i>
                </div>

                <div class="erp-stat-content">

                    <div class="erp-stat-label">
                        Properties
                    </div>

                    <div class="erp-stat-value">
                        {{ number_format($stats['properties']) }}
                    </div>

                    <div class="erp-stat-meta">
                        Customer properties
                    </div>

                </div>

            </div>

        </div>


        {{-- Jobs --}}
        <div class="col-xl-3 col-md-6">

            <div class="erp-stat-card">

                <div class="erp-stat-icon">
                    <i class="bi bi-briefcase"></i>
                </div>

                <div class="erp-stat-content">

                    <div class="erp-stat-label">
                        Open Jobs
                    </div>

                    <div class="erp-stat-value">
                        {{ number_format($stats['open_jobs']) }}
                    </div>

                    <div class="erp-stat-meta">
                        Active jobs
                    </div>

                </div>

            </div>

        </div>


        {{-- Tasks --}}
        <div class="col-xl-3 col-md-6">

            <div class="erp-stat-card">

                <div class="erp-stat-icon">
                    <i class="bi bi-check2-square"></i>
                </div>

                <div class="erp-stat-content">

                    <div class="erp-stat-label">
                        Pending Tasks
                    </div>

                    <div class="erp-stat-value">
                        {{ number_format($stats['pending_tasks']) }}
                    </div>

                    <div class="erp-stat-meta">
                        Tasks requiring attention
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         DASHBOARD CONTENT
    ========================================================== --}}

    <div class="row g-3">

        {{-- =====================================================
             RECENT CUSTOMERS
        ====================================================== --}}

        <div class="col-xl-8">

            <div class="card erp-card h-100">

                <div class="card-header erp-card-header">

                    <div>

                        <h5 class="card-title mb-1">
                            Recent Customers
                        </h5>

                        <div class="text-muted small">
                            Recently added customers
                        </div>

                    </div>

                    <a
                        href="#"
                        class="btn btn-sm btn-outline-secondary disabled"
                    >
                        View All
                    </a>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table erp-table mb-0">

                            <thead>

                                <tr>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Properties</th>
                                    <th>Status</th>
                                    <th class="text-end">
                                        Action
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td colspan="5">

                                        <div class="erp-empty-state">

                                            <div class="erp-empty-icon">
                                                <i class="bi bi-people"></i>
                                            </div>

                                            <h6>
                                                No customers yet
                                            </h6>

                                            <p>
                                                Customers will appear
                                                here once they are added.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             UPCOMING TASKS
        ====================================================== --}}

        <div class="col-xl-4">

            <div class="card erp-card h-100">

                <div class="card-header erp-card-header">

                    <div>

                        <h5 class="card-title mb-1">
                            Upcoming Tasks
                        </h5>

                        <div class="text-muted small">
                            Tasks requiring attention
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        disabled
                    >
                        <i class="bi bi-plus-lg"></i>
                    </button>

                </div>


                <div class="card-body">

                    <div class="erp-empty-state compact">

                        <div class="erp-empty-icon">
                            <i class="bi bi-check2-square"></i>
                        </div>

                        <h6>
                            No upcoming tasks
                        </h6>

                        <p>
                            Upcoming CRM tasks will appear here.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CRM MODULES
    ========================================================== --}}

    <div class="mt-4">

        <div class="mb-3">

            <h5 class="fw-semibold mb-1">
                CRM Modules
            </h5>

            <p class="text-muted small mb-0">
                Manage your customer relationship workflow.
            </p>

        </div>


        <div class="row g-3">

            {{-- Customers --}}
            <div class="col-xl-3 col-md-6">

                <div class="crm-module-card">

                    <div class="crm-module-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div class="crm-module-content">

                        <h6>
                            Customers
                        </h6>

                        <p>
                            Manage customer records and information.
                        </p>

                        <span class="badge text-bg-light">
                            Phase 2
                        </span>

                    </div>

                </div>

            </div>


            {{-- Properties --}}
            <div class="col-xl-3 col-md-6">

                <div class="crm-module-card">

                    <div class="crm-module-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <div class="crm-module-content">

                        <h6>
                            Properties
                        </h6>

                        <p>
                            Manage customer properties and locations.
                        </p>

                        <span class="badge text-bg-light">
                            Phase 4
                        </span>

                    </div>

                </div>

            </div>


            {{-- Tasks --}}
            <div class="col-xl-3 col-md-6">

                <div class="crm-module-card">

                    <div class="crm-module-icon">
                        <i class="bi bi-check2-square"></i>
                    </div>

                    <div class="crm-module-content">

                        <h6>
                            Tasks
                        </h6>

                        <p>
                            Track customer-related activities and tasks.
                        </p>

                        <span class="badge text-bg-light">
                            Phase 7
                        </span>

                    </div>

                </div>

            </div>


            {{-- Jobs --}}
            <div class="col-xl-3 col-md-6">

                <div class="crm-module-card">

                    <div class="crm-module-icon">
                        <i class="bi bi-briefcase"></i>
                    </div>

                    <div class="crm-module-content">

                        <h6>
                            Jobs
                        </h6>

                        <p>
                            Manage customer jobs and work orders.
                        </p>

                        <span class="badge text-bg-light">
                            Phase 8
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection