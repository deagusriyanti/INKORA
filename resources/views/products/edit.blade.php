@extends('layouts.app')
@section('title', 'Edit Produk & Jasa')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Edit Produk / Jasa</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border rounded-3">Kembali</a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Produk / Jasa Cetak <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="product_name"
                                value="{{ old('product_name', $product->product_name) }}" class="form-control rounded-3"
                                required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="unit" value="{{ old('unit', $product->unit) }}"
                                    class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Harga Dasar (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}"
                                    min="0" step="100" class="form-control rounded-3" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Spesifikasi Bahan (Opsional)</label>
                            <textarea name="description" rows="3" class="form-control rounded-3">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="is_active">Status Aktif</label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3">Batal</a>
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
