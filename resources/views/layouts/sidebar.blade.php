{{-- CRM --}}
<div class="sidebar-section">

    <div class="sidebar-section-title">
        CRM
    </div>

    <a
        href="{{ route('crm.dashboard') }}"
        class="sidebar-link {{ request()->routeIs('crm.dashboard') ? 'active' : '' }}"
    >
        <i class="bi bi-grid-1x2"></i>
        <span>Dashboard</span>
    </a>


    {{-- Sales --}}

    <div class="sidebar-group-title">
        Sales
    </div>

    <a href="#" class="sidebar-link disabled">
        <i class="bi bi-person-plus"></i>
        <span>Leads</span>
    </a>

    <a href="#" class="sidebar-link disabled">
        <i class="bi bi-funnel"></i>
        <span>Opportunities</span>
    </a>

    <a href="#" class="sidebar-link disabled">
        <i class="bi bi-file-earmark-text"></i>
        <span>Quotations</span>
    </a>


    {{-- Customers --}}

    <div class="sidebar-group-title">
        Customers
    </div>

    <a
        href="{{ route('crm.customers.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.customers.*') ? 'active' : '' }}"
    >
        <i class="bi bi-people"></i>

        <span>
            Customers
        </span>
    </a>

    <a
        href="{{ route('crm.contacts.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.contacts.*') ? 'active' : '' }}"
    >
        <i class="bi bi-person-lines-fill"></i>
        <span>Contacts</span>
    </a>

    <a
        href="{{ route('crm.properties.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.properties.*') ? 'active' : '' }}"
    >
        <i class="bi bi-buildings"></i>
        <span>Properties</span>
    </a>
    <a
        href="{{ route('crm.property-locations.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.property-locations.*') ? 'active' : '' }}"
    >
        <i class="bi bi-geo-alt"></i>
        <span>Property Locations</span>
    </a>
    <a
        href="{{ route('crm.activities.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.activities.*') ? 'active' : '' }}"
    >
        <i class="bi bi-activity"></i>
        <span>Activities</span>
    </a>
    {{-- Operations --}}

    <div class="sidebar-group-title">
        Operations
    </div>
   

    <a
        href="{{ route('crm.employees.index') }}"
        class="nav-link {{ request()->routeIs('crm.employees.*') ? 'active' : '' }} sidebar-link d"
    >

        <i class="bi bi-people"></i>

        <span>
            Employees
        </span>

    </a>

    <a
        href="{{ route('crm.tasks.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.tasks.*') ? 'active' : '' }}"
    >
        <i class="bi bi-check2-square"></i>
        <span>Tasks</span>
    </a>

    <a
        href="{{ route('crm.jobs.index') }}"
        class="sidebar-link {{ request()->routeIs('crm.jobs.*') ? 'active' : '' }}"
    >
        <i class="bi bi-briefcase"></i>
        <span>Jobs / Work Orders</span>
    </a>

    <a href="#" class="sidebar-link disabled">
        <i class="bi bi-clock-history"></i>
        <span>Activities</span>
    </a>

    <a
        href="{{ route('crm.estimates.index') }}"
        class="nav-link
            {{ request()->routeIs('crm.estimates.*')
                ? 'active'
                : '' }} sidebar-link" 
    >

        <i class="bi bi-file-earmark-text"></i>

        <span>
            Estimates
        </span>

    </a>
    <a
        href="{{ route('crm.job-schedules.calendar') }}"
        class="nav-link
            {{ request()->routeIs('crm.job-schedules.*')
                ? 'active'
                : '' }} sidebar-link "
    >

        <i class="bi bi-calendar3"></i>

        <span>
            Job Scheduling
        </span>

    </a>

    
    <a
        href="{{ route('crm.invoices.index') }}"
        class="nav-link sidebar-link"
    >
        <i class="bi bi-receipt"></i>
        <span>Invoices</span>
    </a>


</div>