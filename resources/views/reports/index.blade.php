@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <a href="{{ route('reports.sales') }}" class="text-decoration-none">
            <div class="card text-center p-4 h-100"
                 style="border-top:4px solid #4f46e5;transition:transform 0.2s"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <i class="bi bi-receipt fs-1 mb-3" style="color:#4f46e5"></i>
                <h5 class="fw-semibold">Sales Report</h5>
                <p class="text-muted small mb-0">
                    Bills, revenue, payments by date range and salesperson
                </p>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.commissions') }}" class="text-decoration-none">
            <div class="card text-center p-4 h-100"
                 style="border-top:4px solid #10b981;transition:transform 0.2s"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <i class="bi bi-graph-up-arrow fs-1 mb-3" style="color:#10b981"></i>
                <h5 class="fw-semibold">Commission Report</h5>
                <p class="text-muted small mb-0">
                    Commission earned, deductions, and payouts by salesperson
                </p>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.inventory') }}" class="text-decoration-none">
            <div class="card text-center p-4 h-100"
                 style="border-top:4px solid #f59e0b;transition:transform 0.2s"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <i class="bi bi-box-seam fs-1 mb-3" style="color:#f59e0b"></i>
                <h5 class="fw-semibold">Inventory Report</h5>
                <p class="text-muted small mb-0">
                    Stock levels, values, expiry and low stock status
                </p>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
            <div class="card text-center p-4 h-100"
                 style="border-top:4px solid #ef4444;transition:transform 0.2s"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <i class="bi bi-wallet2 fs-1 mb-3" style="color:#ef4444"></i>
                <h5 class="fw-semibold">Expense Report</h5>
                <p class="text-muted small mb-0">
                    Expenses by category and date range
                </p>
            </div>
        </a>
    </div>
</div>
@endsection