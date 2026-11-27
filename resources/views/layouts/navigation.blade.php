<nav class="topbar">
    <div class="topbar-left">
        <button
            type="button"
            class="sidebar-toggle"
            onclick="const sidebar = document.querySelector('.admin-sidebar'); const isOpen = sidebar.classList.toggle('open'); this.setAttribute('aria-expanded', isOpen)"
            aria-label="Toggle navigation"
            aria-expanded="false"
        >
            <i class="bi bi-list"></i>
        </button>
        <span class="topbar-title">CRM</span>
    </div>
</nav>