@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('content')
    <div class="mb-4">
        <a href="{{ route('login') }}"
            class="text-decoration-none small fw-semibold text-muted d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke halaman login
        </a>
        <h3 class="auth-heading">Lupa Password?</h3>
        <p class="text-muted small mb-0">Masukkan alamat email akun terdaftar Anda untuk membuat tautan pengaturan ulang
            password.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 small text-break">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label fw-semibold small">Alamat Email Terdaftar</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" placeholder="admin@percetakan.test" required
                autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-inkora-lime w-100">
            Buat Tautan Reset Password <i class="bi bi-key ms-1"></i>
        </button>
    </form>
@endsection
