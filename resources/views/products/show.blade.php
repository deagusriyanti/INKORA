@extends('layouts.app')
@section('title', 'Detail Produk & Jasa')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Detail Produk / Jasa</h5>
                    <span
                        class="badge rounded-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="small text-muted">Nama Produk / Jasa</div>
                        <h5 class="fw-bold mb-0">{{ $product->product_name }}</h5>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Satuan Ukur</div>
                            <div class="fw-semibold">{{ $product->unit }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Harga Dasar</div>
                            <div class="fw-bold fs-5 text-dark">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="small text-muted">Deskripsi / Spesifikasi</div>
                        <div class="fw-medium">{{ $product->description ?? 'Tidak ada keterangan tambahan.' }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-lime px-4">
                            <i class="bi bi-pencil me-1"></i> Edit Produk
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
