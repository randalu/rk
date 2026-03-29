<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Raphakallos') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-heart-pulse-fill text-danger"></i>
        Raphakallos
    </div>
    <nav class="nav flex-column mt-3">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-link text-uppercase" style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
            Sales
        </div>
        <a href="{{ route('bills.index') }}"
           class="nav-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Bills
        </a>
        <a href="{{ route('customers.index') }}"
           class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
        <a href="{{ route('returns.index') }}"
           class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-return-left"></i> Returns
        </a>
        <a href="{{ route('payments.index') }}"
           class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i> Payments
        </a>

        <div class="nav-link text-uppercase" style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
            Inventory
        </div>
        <a href="{{ route('inventory.index') }}"
           class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Inventory
        </a>
        <a href="{{ route('purchases.index') }}"
           class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Purchases
        </a>
        <a href="{{ route('suppliers.index') }}"
           class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Suppliers
        </a>

        <div class="nav-link text-uppercase" style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
            Finance
        </div>
        <a href="{{ route('expenses.index') }}"
           class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> Expenses
        </a>
        <a href="{{ route('commissions.index') }}"
           class="nav-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Commissions
        </a>

        <div class="nav-link text-uppercase" style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
            Settings
        </div>
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Users
        </a>
        <a href="{{ route('sms-recipients.index') }}"
           class="nav-link {{ request()->routeIs('sms-recipients.*') ? 'active' : '' }}">
            <i class="bi bi-phone"></i> SMS Recipients
        </a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="fw-semibold text-muted">@yield('title', 'Dashboard')</div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">{{ auth()->user()->name }}</span>
            <span class="badge bg-primary">{{ auth()->user()->role->name ?? 'No Role' }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

</body>
</html>