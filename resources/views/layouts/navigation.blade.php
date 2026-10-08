<nav class="topbar">
<!--
    {{-- Left --}}
    <div class="topbar-left">

        <button
            type="button"
            class="sidebar-toggle"
            onclick="document.querySelector('.sidebar').classList.toggle('show')"
            aria-label="Toggle sidebar"
        >
            <i class="bi bi-list"></i>
        </button>

        <div>
            <h1 class="topbar-title">
                @yield('page-title', 'Dashboard')
            </h1>

            <div class="topbar-subtitle">
                Construction ERP Management System
            </div>
        </div>

    </div>


    {{-- Right --}}
    <div class="topbar-right">

        {{-- Notifications --}}
        <div class="dropdown">

            <button
                class="topbar-button notification-button"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Notifications"
            >

                <i class="bi bi-bell"></i>

                @if(isset($notificationCount) && $notificationCount > 0)

                    <span class="notification-badge">
                        {{ $notificationCount }}
                    </span>

                @endif

            </button>


            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <h6 class="dropdown-header">
                        Notifications
                    </h6>
                </li>

                <li>
                    <span class="dropdown-item-text text-muted small">
                        No new notifications
                    </span>
                </li>

            </ul>

        </div>


        {{-- User --}}
        <div class="user-menu">

            <div class="dropdown">

                <button
                    class="user-button"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >

                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>


                    <div class="user-info">

                        <span class="user-name">
                            {{ auth()->user()->name ?? 'User' }}
                        </span>

                        <span class="user-role">
                            Administrator
                        </span>

                    </div>


                    <i class="bi bi-chevron-down user-chevron"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end user-dropdown">

                    <li>
                        <a
                            class="dropdown-item"
                            href="#"
                        >
                            <i class="bi bi-person"></i>
                            Profile
                        </a>
                    </li>


                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('crm.dashboard') }}"
                        >
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <form
                            method="POST"
                            action="#"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item logout-button"
                            >
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </div>
-->
</nav>