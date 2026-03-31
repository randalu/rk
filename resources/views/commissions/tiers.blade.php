@extends('layouts.app')
@section('title', 'Commission Tier Settings')

@section('content')
<div class="row g-4">

    {{-- Value Based Tiers --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-currency-dollar me-2"></i>Value Based Tiers</span>
                <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addValueModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Tier
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Min Value</th>
                            <th>Max Value</th>
                            <th>Rate</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($valueTiers as $tier)
                        <tr>
                            <td>{{ config('app.currency') }} {{ number_format($tier->min_threshold, 2) }}</td>
                            <td>
                                @if($tier->max_threshold)
                                    {{ config('app.currency') }} {{ number_format($tier->max_threshold, 2) }}
                                @else
                                    <span class="text-muted">No limit</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-success">{{ $tier->rate }}%</td>
                            <td>
                                <span class="badge bg-{{ $tier->is_active ? 'success' : 'secondary' }}">
                                    {{ $tier->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTier{{ $tier->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('commission-tiers.destroy', $tier) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this tier?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editTier{{ $tier->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Tier</h5>
                                        <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('commission-tiers.update', $tier) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Min Value</label>
                                                    <input type="number" name="min_threshold"
                                                           class="form-control"
                                                           value="{{ $tier->min_threshold }}"
                                                           step="0.01" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Max Value</label>
                                                    <input type="number" name="max_threshold"
                                                           class="form-control"
                                                           value="{{ $tier->max_threshold }}"
                                                           step="0.01"
                                                           placeholder="Leave blank for no limit">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Rate (%)</label>
                                                    <input type="number" name="rate"
                                                           class="form-control"
                                                           value="{{ $tier->rate }}"
                                                           step="0.01" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Active</label>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="is_active"
                                                               value="1"
                                                               {{ $tier->is_active ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No value-based tiers yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Qty Based Tiers --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-boxes me-2"></i>Quantity Based Tiers</span>
                <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addQtyModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Tier
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Min Qty</th>
                            <th>Max Qty</th>
                            <th>Rate</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qtyTiers as $tier)
                        <tr>
                            <td>{{ number_format($tier->min_threshold) }} pcs</td>
                            <td>
                                @if($tier->max_threshold)
                                    {{ number_format($tier->max_threshold) }} pcs
                                @else
                                    <span class="text-muted">No limit</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-success">{{ $tier->rate }}%</td>
                            <td>
                                <span class="badge bg-{{ $tier->is_active ? 'success' : 'secondary' }}">
                                    {{ $tier->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTier{{ $tier->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('commission-tiers.destroy', $tier) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this tier?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editTier{{ $tier->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Tier</h5>
                                        <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('commission-tiers.update', $tier) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Min Qty (pcs)</label>
                                                    <input type="number" name="min_threshold"
                                                           class="form-control"
                                                           value="{{ $tier->min_threshold }}"
                                                           required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Max Qty (pcs)</label>
                                                    <input type="number" name="max_threshold"
                                                           class="form-control"
                                                           value="{{ $tier->max_threshold }}"
                                                           placeholder="Leave blank for no limit">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Rate (%)</label>
                                                    <input type="number" name="rate"
                                                           class="form-control"
                                                           value="{{ $tier->rate }}"
                                                           step="0.01" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Active</label>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="is_active"
                                                               value="1"
                                                               {{ $tier->is_active ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No quantity-based tiers yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Value Tier Modal --}}
<div class="modal fade" id="addValueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Value Based Tier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('commission-tiers.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="value_based">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Min Value ({{ config('app.currency') }}) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="min_threshold"
                                   class="form-control" step="0.01"
                                   placeholder="e.g. 10000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Max Value ({{ config('app.currency') }})
                            </label>
                            <input type="number" name="max_threshold"
                                   class="form-control" step="0.01"
                                   placeholder="Leave blank for no limit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Rate (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rate"
                                   class="form-control" step="0.01"
                                   placeholder="e.g. 10" required>
                        </div>
                    </div>
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Example: Min 10,000 / Max 25,000 / Rate 10%
                        means 10% commission on bills between Rs. 10,000 and Rs. 25,000.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tier</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Qty Tier Modal --}}
<div class="modal fade" id="addQtyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Quantity Based Tier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('commission-tiers.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="qty_based">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Min Qty (pcs) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="min_threshold"
                                   class="form-control"
                                   placeholder="e.g. 1000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Max Qty (pcs)</label>
                            <input type="number" name="max_threshold"
                                   class="form-control"
                                   placeholder="Leave blank for no limit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Rate (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rate"
                                   class="form-control" step="0.01"
                                   placeholder="e.g. 15" required>
                        </div>
                    </div>
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Example: Min 1000 / Max blank / Rate 15%
                        means 15% commission when 1000+ units are sold.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('commissions.index') }}"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Commissions
    </a>
</div>
@endsection