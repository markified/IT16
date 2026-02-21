<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa-light fa-computer"></i>
        </div>
        <div class="sidebar-brand-text mx-3">DayTech <sup>computer parts</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('products') }}">
            <i class="fas fa-box-open"></i>
            <span>Product</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('suppliers') }}">
            <i class="fas fa-truck"></i>
            <span>Supplier</span></a>
    </li>

    <!-- Nav Item - Purchase Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('purchase-orders.index') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Purchase Orders</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('stock-in.index') }}">
            <i class="fas fa-arrow-down"></i>
            <span>Stock In</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('inventory-issues') }}">
            <i class="fas fa-arrow-down"></i>
            <span>Stock Out</span></a>
    </li>
    @if(Auth::user()->role == 'admin')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('employees') }}">
            <i class="fas fa-users"></i>
            <span>Employees</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('departments') }}">
            <i class="fas fa-building"></i>
            <span>Departments</span></a>

    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('reports.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span></a>

        @endif

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

        <!-- Logo -->
        <div class="text-center mt-5">
            <img src="{{ asset('image/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 80%;">
        </div>

</ul>