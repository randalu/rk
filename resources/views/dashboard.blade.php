@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4f46e5, #7c3aed)">
            <div class="small mb-1 opacity-75">Total Revenue</div>
            <div class="fs-3 fw-bold">Rs. {{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-cash"></i> All time payments</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7)">
            <div class="small mb-1 opacity-75">Total Bills</div>
            <div class="fs-3 fw-bold">{{ $stats['total_bills'] }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-receipt"></i> {{ $stats['unpaid_bills'] }} unpaid</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669)">
            <div class="small mb-1 opacity-75">Customers</div>
            <div class="fs-3 fw-bold">{{ $stats['total_customers'] }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-people"></i> Active accounts</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
            <div class="small mb-1 opacity-75">Low Stock Items</div>
            <div class="fs-3 fw-bold">{{ $stats['low_stock_items'] }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-exclamation-triangle"></i> Need restocking</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
            <div class="small mb-1 opacity-75">Pending Returns</div>
            <div class="fs-3 fw-bold">{{ $stats['pending_returns'] }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-arrow-return-left"></i> Awaiting approval</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)">
            <div class="small mb-1 opacity-75">Unpaid Bills</div>
            <div class="fs-3 fw-bold">{{ $stats['unpaid_bills'] }}</div>
            <div class="small opacity-75 mt-1"><i class="bi bi-clock"></i> Outstanding</div>
        </div>
    </div>
</div>
@endsection