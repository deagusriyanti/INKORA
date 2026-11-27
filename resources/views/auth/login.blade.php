@extends('layouts.guest')
@section('title', 'Masuk Sistem')

@section('content')
    <div class="d-flex align-items-center gap-2 d-md-none mb-4">
        <span class="auth-brand-icon"><i class="bi bi-layers-half"></i></span>
        <span class="inkora-brand-title">INKORA</span>
    </div>

    <div class="mb-4">
        <h3 class="auth-heading">Selamat Datang Kembali</h3>
        <p class="text-muted small mb-0">Masuk ke akun Anda untuk mengakses dashboard operasional.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 small py-2 px-3">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 small py-2 px-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold small">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" placeholder="admin@percetakan.test" required
                autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-semibold small mb-0">Password</label>
                <a href="{{ route('password.request') }}" class="small auth-link-teal">
                    Lupa password?
                </a>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="loginPassword"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password Anda"
                    required>
                <button type="button" class="btn btn-toggle-pass" onclick="togglePassword('loginPassword', 'eyeIcon')"
                    title="Tampilkan/Sembunyikan Password">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small text-muted" for="remember">Ingat sesi login saya</label>
        </div>

        <button type="submit" class="btn btn-inkora-lime w-100">
            Masuk ke Dashboard <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    @push('scripts')
        <script>
            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            }
        </script>
    @endpush
@endsection
