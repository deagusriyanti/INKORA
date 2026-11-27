@extends('layouts.app')
@section('title', 'Produk & Jasa Percetakan')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0">Katalog Produk & Jasa Percetakan</h5>
                <small class="text-muted">Kelola daftar layanan cetak, satuan ukur, dan harga dasar</small>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-lime">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk / Jasa
            </a>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ $search }}" class="form-control rounded-3"
                        placeholder="Cari nama produk/jasa atau satuan...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Nama Produk / Jasa</th>
                            <th>Satuan</th>
                            <th>Harga Dasar</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $product) }}"
                                        class="fw-semibold text-decoration-none text-dark">
                                        {{ $product->product_name }}
                                    </a>
                                    @if ($product->description)
                                        <div class="small text-muted">
                                            {{ \Illuminate\Support\Str::limit($product->description, 55) }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $product->unit }}</span></td>
                                <td class="fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="btn btn-sm btn-light border rounded-3" title="Detail Produk">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="btn btn-sm btn-light border rounded-3" title="Edit Produk">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus produk/jasa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"
                                            title="Hapus Produk">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data produk atau jasa
                                    percetakan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
