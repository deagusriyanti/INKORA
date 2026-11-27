@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Edit Data Customer</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light border rounded-3">Kembali</a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Customer / Instansi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="customer_name"
                                value="{{ old('customer_name', $customer->customer_name) }}" class="form-control rounded-3"
                                required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor Telepon / WhatsApp <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                                    class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email (Opsional)</label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                                    class="form-control rounded-3">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="form-control rounded-3">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3">Batal</a>
                            <button type="submit" class="btn btn-lime px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
