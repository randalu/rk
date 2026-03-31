@extends('layouts.app')
@section('title', 'Access Denied')

@section('content')
<div class="card text-center py-5">
    <div class="card-body">
        <i class="bi bi-lock fs-1 text-danger mb-3"></i>
        <h3 class="fw-bold">Access Denied</h3>
        <p class="text-muted">
            You don't have permission to access this page.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection