@extends('layouts.app')
@section('title', 'Data Customer')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0">Daftar Customer</h5>
                <small class="text-muted">Kelola data pelanggan percetakan Anda</small>
            </div>
            <a href="{{ route('customers.create') }}" class="btn btn-lime">
                <i class="bi bi-plus-lg me-1"></i> Tambah Customer
            </a>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-2 mb-4">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ $search }}" class="form-control rounded-3"
                        placeholder="Cari nama customer, nomor HP, atau email...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Nama Customer</th>
                            <th>No. Telepon / WA</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th class="text-center">Total Pesanan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="fw-semibold">
                                    <a href="{{ route('customers.show', $customer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $customer->customer_name }}
                                    </a>
                                </td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->email ?? '-' }}</td>
                                <td class="small text-muted">
                                    {{ \Illuminate\Support\Str::limit($customer->address, 40) ?? '-' }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-light text-dark border px-3">{{ $customer->orders_count }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('customers.show', $customer) }}"
                                        class="btn btn-sm btn-light border rounded-3" title="Detail Customer">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                        class="btn btn-sm btn-light border rounded-3" title="Edit Customer">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus data customer ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"
                                            title="Hapus Customer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection
