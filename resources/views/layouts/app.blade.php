<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ systemSetting('system_name', config('app.name', 'Raphakallos')) }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="toggleSidebar()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99;"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand d-flex justify-content-between align-items-center">
        <span class="d-flex align-items-center gap-2">
            @if(systemLogoUrl())
            <img src="{{ systemLogoUrl() }}"
                 alt="Logo"
                 style="height:28px; width:auto; border-radius:6px; background:#fff; padding:2px;">
            @else
            <i class="bi bi-heart-pulse-fill text-danger"></i>
            @endif
            <span>{{ systemSetting('system_name', 'Raphakallos') }}</span>
        </span>
        <button onclick="toggleSidebar()"
                class="d-md-none btn btn-sm text-white border-0 p-0">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <nav class="nav flex-column mt-3">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-link text-uppercase"
             style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
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

        <div class="nav-link text-uppercase"
             style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
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

        <div class="nav-link text-uppercase"
             style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
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
        @if(userCan('view_reports'))
        <a href="{{ route('reports.index') }}"
           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Reports
        </a>
        @endif

        <div class="nav-link text-uppercase"
             style="font-size:10px;letter-spacing:1px;color:#4a5568;padding-top:16px">
            Settings
        </div>
        @if(userCan('manage_users'))
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Users
        </a>
        <a href="{{ route('system-settings.edit') }}"
           class="nav-link {{ request()->routeIs('system-settings.*') ? 'active' : '' }}">
            <i class="bi bi-building-gear"></i> System Settings
        </a>
        @endif
        <a href="{{ route('salespeople.index') }}"
           class="nav-link {{ request()->routeIs('salespeople.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Salespeople
        </a>
        @if(userCan('edit_commission_tiers'))
        <a href="{{ route('commission-tiers.index') }}"
           class="nav-link {{ request()->routeIs('commission-tiers.*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> Commission Tiers
        </a>
        @endif
        @if(userCan('manage_sms_recipients'))
        <a href="{{ route('sms-recipients.index') }}"
           class="nav-link {{ request()->routeIs('sms-recipients.*') ? 'active' : '' }}">
            <i class="bi bi-phone"></i> SMS Recipients
        </a>
        @endif
        @if(userCan('view_action_log'))
        <a href="{{ route('action-log.index') }}"
           class="nav-link {{ request()->routeIs('action-log.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Action Log
        </a>
        @endif
    </nav>
</div>

<div class="main-content" id="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="d-md-none btn btn-sm btn-outline-secondary"
                    onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="fw-semibold text-muted d-none d-md-block">
                @yield('title', 'Dashboard')
            </span>
            <span class="fw-semibold text-muted d-md-none">
                {{ systemSetting('system_name', 'Raphakallos') }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-md-inline">
                {{ auth()->user()->name }}
            </span>
            <span class="badge bg-primary">
                {{ auth()->user()->role->name ?? 'No Role' }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-md-inline">Logout</span>
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

<script>
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const isOpen   = sidebar.classList.contains('sidebar-open');

    if (isOpen) {
        sidebar.classList.remove('sidebar-open');
        overlay.style.display = 'none';
    } else {
        sidebar.classList.add('sidebar-open');
        overlay.style.display = 'block';
    }
}
</script>

</body>
</html>
