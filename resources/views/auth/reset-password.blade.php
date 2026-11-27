@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
    <div class="mb-4">
        <h3 class="auth-heading">Atur Ulang Password</h3>
        <p class="text-muted small mb-0">Silakan buat password baru minimal 8 karakter untuk akun Anda.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}"
                class="form-control @error('email') is-invalid @enderror" required readonly>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Password Baru</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Minimal 8 karakter" required autofocus>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru"
                required>
        </div>

        <button type="submit" class="btn btn-inkora-lime w-100">
            Simpan Password Baru <i class="bi bi-check-lg ms-1"></i>
        </button>
    </form>
@endsection
