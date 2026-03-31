<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raphakallos — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#f5f6fa; min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div style="width:100%; max-width:420px; padding:16px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">

            {{-- Logo --}}
            <div class="text-center mb-4">
                <div style="font-size:28px; font-weight:700; color:#4f46e5;">
                    <i class="bi bi-heart-pulse-fill text-danger me-2"></i>
                    Raphakallos
                </div>
                <div class="text-muted small mt-1">Medical Sales Management</div>
            </div>

            {{-- Session errors --}}
            @if($errors->any())
            <div class="alert alert-danger small py-2">
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('status'))
            <div class="alert alert-success small py-2">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="admin@raphakallos.com"
                           autofocus required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="remember" id="remember">
                        <label class="form-check-label small" for="remember">
                            Remember me
                        </label>
                    </div>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="small text-decoration-none">
                        Forgot password?
                    </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
            </form>
        </div>

        <div class="card-footer text-center text-muted small py-2">
            {{ config('app.name') }} &copy; {{ date('Y') }}
        </div>
    </div>
</div>

</body>
</html>