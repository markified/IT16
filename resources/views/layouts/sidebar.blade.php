@php
    // Helper to check active routes
    $isActive = fn($routes) => collect((array) $routes)->contains(fn($route) => request()->routeIs($route));
    $isReportsActive = $isActive(['inventory-reports.*']);
    $isSecurityActive = $isActive(['security.*']);
    $isDatabaseActive = $isActive(['database.*']);
@endphp

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ Auth::user()->isSecurity() ? route('security.index') : route('dashboard') }}">
        <img src="{{ asset('image/logo.png') }}" alt="PC Parts Inventory" class="sidebar-brand-icon">
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard - Hidden for Security role -->
    @if(!Auth::user()->isSecurity())
    <li class="nav-item {{ $isActive('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @endif

    <!-- Inventory Section - Only for Superadmin and Inventory roles -->
    @if(Auth::user()->canManageInventory())
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Inventory</div>

    <li class="nav-item {{ $isActive('categories.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('categories.index') }}">
            <i class="fas fa-tags"></i>
            <span>Categories</span>
        </a>
    </li>

    <li class="nav-item {{ $isActive(['products', 'products.*']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('products') }}">
            <i class="fas fa-microchip"></i>
            <span>PC Parts</span>
        </a>
    </li>

    <li class="nav-item {{ $isActive(['suppliers', 'suppliers.*']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('suppliers') }}">
            <i class="fas fa-truck"></i>
            <span>Suppliers</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Transactions</div>

    <li class="nav-item {{ $isActive('stock-in.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('stock-in.index') }}">
            <i class="fas fa-arrow-down text-success"></i>
            <span>Stock In</span>
        </a>
    </li>

    <li class="nav-item {{ $isActive(['stock-out-orders.*']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('stock-out-orders.index') }}">
            <i class="fas fa-file-export text-danger"></i>
            <span>Stock Out Orders</span>
        </a>
    </li>

    <li class="nav-item {{ $isActive('stock-adjustments.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('stock-adjustments.index') }}">
            <i class="fas fa-balance-scale text-warning"></i>
            <span>Adjustments</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Reports & Analytics</div>

    <li class="nav-item {{ $isReportsActive ? 'active' : '' }}">
        <a class="nav-link {{ $isReportsActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" 
           data-target="#collapseReports" aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}" 
           aria-controls="collapseReports">
            <i class="fas fa-chart-bar"></i>
            <span>Inventory Reports</span>
        </a>
        <div id="collapseReports" class="collapse {{ $isReportsActive ? 'show' : '' }}" 
             aria-labelledby="headingReports" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded sidebar-collapse-bg">
                <h6 class="collapse-header">Report Types:</h6>
                <a class="collapse-item {{ $isActive('inventory-reports.index') ? 'active' : '' }}" 
                   href="{{ route('inventory-reports.index') }}">All Reports</a>
                <a class="collapse-item {{ $isActive('inventory-reports.valuation') ? 'active' : '' }}" 
                   href="{{ route('inventory-reports.valuation') }}">Valuation Report</a>
                <a class="collapse-item {{ $isActive('inventory-reports.movement') ? 'active' : '' }}" 
                   href="{{ route('inventory-reports.movement') }}">Stock Movement</a>
                <a class="collapse-item {{ $isActive('inventory-reports.low-stock') ? 'active' : '' }}" 
                   href="{{ route('inventory-reports.low-stock') }}">Low Stock Alert</a>
                <a class="collapse-item {{ $isActive('inventory-reports.category') ? 'active' : '' }}" 
                   href="{{ route('inventory-reports.category') }}">Category Summary</a>
            </div>
        </div>
    </li>
    @endif

    <!-- Administration Section - For Superadmin, Admin, and Security roles -->
    @if(Auth::user()->canAccessAdminFeatures() || Auth::user()->canManageSecurity())
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Administration</div>

    @if(Auth::user()->canAccessAdminFeatures())
    <li class="nav-item {{ $isActive('users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-user-cog"></i>
            <span>Manage Users</span>
        </a>
    </li>
    @endif

    @if(Auth::user()->canAccessAdminFeatures())
    <li class="nav-item {{ $isActive('audit-logs.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('audit-logs.index') }}">
            <i class="fas fa-history"></i>
            <span>Audit Trail</span>
        </a>
    </li>
    @endif
    
    @if(Auth::user()->canAccessAdminFeatures())
    <li class="nav-item {{ $isActive('reports.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>Custom Reports</span>
        </a>
    </li>
    @endif
    @endif

    @if(Auth::user()->isAdmin() || Auth::user()->isSecurity())
    <hr class="sidebar-divider">
    <div class="sidebar-heading">System</div>

    <li class="nav-item {{ $isSecurityActive ? 'active' : '' }}">
        <a class="nav-link {{ $isSecurityActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" 
           data-target="#collapseSecurity" aria-expanded="{{ $isSecurityActive ? 'true' : 'false' }}" 
           aria-controls="collapseSecurity">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <div id="collapseSecurity" class="collapse {{ $isSecurityActive ? 'show' : '' }}" 
             aria-labelledby="headingSecurity" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded sidebar-collapse-bg">
                <h6 class="collapse-header">Security Management:</h6>
                <a class="collapse-item {{ $isActive('security.index') ? 'active' : '' }}" 
                   href="{{ route('security.index') }}">Dashboard</a>
                <a class="collapse-item {{ $isActive('security.login-history') ? 'active' : '' }}" 
                   href="{{ route('security.login-history') }}">Login History</a>
                <a class="collapse-item {{ $isActive('security.active-sessions') ? 'active' : '' }}" 
                   href="{{ route('security.active-sessions') }}">Active Sessions</a>
                <a class="collapse-item {{ $isActive('security.settings') ? 'active' : '' }}" 
                   href="{{ route('security.settings') }}">Settings</a>
            </div>
        </div>
    </li>

    <li class="nav-item {{ $isDatabaseActive ? 'active' : '' }}">
        <a class="nav-link {{ $isDatabaseActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" 
           data-target="#collapseDatabase" aria-expanded="{{ $isDatabaseActive ? 'true' : 'false' }}" 
           aria-controls="collapseDatabase">
            <i class="fas fa-database"></i>
            <span>Database</span>
        </a>
        <div id="collapseDatabase" class="collapse {{ $isDatabaseActive ? 'show' : '' }}" 
             aria-labelledby="headingDatabase" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded sidebar-collapse-bg">
                <h6 class="collapse-header">Database Management:</h6>
                <a class="collapse-item {{ $isActive('database.index') ? 'active' : '' }}" 
                   href="{{ route('database.index') }}">Overview</a>
                <a class="collapse-item {{ $isActive('database.backups') ? 'active' : '' }}" 
                   href="{{ route('database.backups') }}">Backups</a>
            </div>
        </div>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<style>
/* Sidebar Optimizations */
.sidebar .nav-item.active > .nav-link {
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
    border-left: 4px solid #fff;
}
.sidebar .nav-item .nav-link {
    transition: all 0.2s ease-in-out;
    border-left: 4px solid transparent;
}
.sidebar .nav-item .nav-link:hover {
    background: rgba(255, 255, 255, 0.08);
    padding-left: 1.2rem;
}
/* Sidebar collapse submenu background - light mode default */
.sidebar-collapse-bg {
    background-color: #fff;
    transition: background-color 0.3s ease;
}
/* Dark mode sidebar collapse */
body.dark-mode .sidebar-collapse-bg {
    background-color: #1a1a2e !important;
    border: 1px solid #2a2a4a;
}
body.dark-mode .sidebar .collapse-item {
    color: #c8c8d8 !important;
}
body.dark-mode .sidebar .collapse-item:hover {
    background-color: #0f3460 !important;
    color: #fff !important;
}
body.dark-mode .sidebar .collapse-item.active {
    background-color: #0f3460 !important;
    color: #4e9fff !important;
}
body.dark-mode .sidebar .collapse-header {
    color: #8888aa !important;
}
.sidebar .collapse-item.active {
    font-weight: 600;
    color: #4e73df !important;
    background-color: #eaecf4;
}
.sidebar .collapse-item {
    transition: all 0.15s ease-in-out;
}
.sidebar .collapse-item:hover {
    background-color: #eaecf4;
}
.sidebar-brand-icon {
    max-height: 70px;
    transition: transform 0.3s ease;
    object-fit: contain;
}
.sidebar-brand:hover .sidebar-brand-icon {
    transform: scale(1.05);
}
.sidebar .sidebar-heading {
    text-transform: uppercase;
    letter-spacing: 0.05rem;
}
</style>

<script>
// Preserve sidebar scroll position across page loads
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('accordionSidebar');
    if (!sidebar) return;

    // Restore scroll position
    const savedScrollTop = localStorage.getItem('sidebarScrollPos');
    if (savedScrollTop) {
        sidebar.scrollTop = parseInt(savedScrollTop);
    }

    // Save scroll position before navigating away
    window.addEventListener('beforeunload', function() {
        localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
    });

    // Also save on sidebar scroll (for catching quick navigations)
    sidebar.addEventListener('scroll', function() {
        localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
    });

    // Scroll active sidebar item into view if not visible
    const activeItem = sidebar.querySelector('.nav-item.active');
    if (activeItem && !savedScrollTop) {
        activeItem.scrollIntoView({ block: 'center', behavior: 'instant' });
    }
});
</script>