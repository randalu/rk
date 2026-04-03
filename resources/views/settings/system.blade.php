@extends('layouts.app')
@section('title', 'System Settings')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-stars me-2"></i>Brand Preview
            </div>
            <div class="card-body text-center">
                @if(systemLogoUrl())
                <img src="{{ systemLogoUrl() }}"
                     alt="System logo"
                     class="img-fluid rounded border p-2 bg-white mb-3"
                     style="max-height:120px;">
                @endif
                <h5 class="mb-1">{{ $settings->system_name }}</h5>
                <div class="text-muted small">{{ $settings->company_name }}</div>
                @if($settings->company_tagline)
                <div class="text-muted small mt-1">{{ $settings->company_tagline }}</div>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>What This Controls
            </div>
            <div class="card-body">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">Sidebar branding and system name</li>
                    <li class="mb-2">Invoice/PDF header logo and company details</li>
                    <li class="mb-2">Editable footer heading and notes</li>
                    <li>Business contact information shown on invoices</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Main Settings
            </div>
            <div class="card-body">
                <form action="{{ route('system-settings.update') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-semibold border-bottom pb-2 mb-3">Branding</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">System Name <span class="text-danger">*</span></label>
                            <input type="text" name="system_name"
                                   class="form-control @error('system_name') is-invalid @enderror"
                                   value="{{ old('system_name', $settings->system_name) }}">
                            @error('system_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">System Logo</label>
                            <input type="file" name="logo"
                                   class="form-control @error('logo') is-invalid @enderror"
                                   accept=".png,.jpg,.jpeg,.webp">
                            <div class="form-text">Default logo is loaded from <code>public/RK_logo.PNG</code>.</div>
                            @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-semibold border-bottom pb-2 mb-3">Business Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name"
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   value="{{ old('company_name', $settings->company_name) }}">
                            @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tagline</label>
                            <input type="text" name="company_tagline"
                                   class="form-control @error('company_tagline') is-invalid @enderror"
                                   value="{{ old('company_tagline', $settings->company_tagline) }}"
                                   placeholder="e.g. Medical Sales & Distribution">
                            @error('company_tagline')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="company_phone"
                                   class="form-control @error('company_phone') is-invalid @enderror"
                                   value="{{ old('company_phone', $settings->company_phone) }}">
                            @error('company_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="company_email"
                                   class="form-control @error('company_email') is-invalid @enderror"
                                   value="{{ old('company_email', $settings->company_email) }}">
                            @error('company_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="text" name="company_website"
                                   class="form-control @error('company_website') is-invalid @enderror"
                                   value="{{ old('company_website', $settings->company_website) }}"
                                   placeholder="e.g. www.example.com">
                            @error('company_website')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Registration No.</label>
                            <input type="text" name="company_registration_no"
                                   class="form-control @error('company_registration_no') is-invalid @enderror"
                                   value="{{ old('company_registration_no', $settings->company_registration_no) }}">
                            @error('company_registration_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="company_address"
                                      rows="3"
                                      class="form-control @error('company_address') is-invalid @enderror"
                                      placeholder="Company address shown on invoices">{{ old('company_address', $settings->company_address) }}</textarea>
                            @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-semibold border-bottom pb-2 mb-3">Invoice Footer</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Footer Heading</label>
                            <input type="text" name="invoice_footer_heading"
                                   class="form-control @error('invoice_footer_heading') is-invalid @enderror"
                                   value="{{ old('invoice_footer_heading', $settings->invoice_footer_heading) }}"
                                   placeholder="Thank you for your business">
                            @error('invoice_footer_heading')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Footer Notes</label>
                            <textarea name="invoice_footer_notes"
                                      rows="4"
                                      class="form-control @error('invoice_footer_notes') is-invalid @enderror"
                                      placeholder="Bank details, payment instructions, return policy, etc.">{{ old('invoice_footer_notes', $settings->invoice_footer_notes) }}</textarea>
                            <div class="form-text">The outstanding balance line will appear before these notes.</div>
                            @error('invoice_footer_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
