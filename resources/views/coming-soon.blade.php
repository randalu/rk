@extends('layouts.app')
@section('title', $module)

@section('content')
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-tools fs-1 text-muted"></i>
        <h4 class="mt-3">{{ $module }}</h4>
        <p class="text-muted">This module is coming soon.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection