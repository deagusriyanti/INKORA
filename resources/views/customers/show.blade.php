@extends('layouts.app')
@section('title', 'Detail Customer')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div
                            class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-4 avatar-circle-lg">
                            {{ strtoupper(substr($customer->customer_name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $customer->customer_name }}</h5>
                            <small class="text-muted">Terdaftar
                                {{ $customer->created_at->translatedFormat('d M Y') }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Nomor Telepon / WA</div>
                        <div class="fw-semibold">{{ $customer->phone }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Email</div>
                        <div class="fw-semibold">{{ $customer->email ?? '-' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="small text-muted">Alamat</div>
                        <div class="fw-semibold">{{ $customer->address ?? '-' }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-lime flex-grow-1">
                            <i class="bi bi-pencil me-1"></i> Edit Data
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Riwayat Pesanan Customer ({{ $customer->orders->count() }})</h6>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Status Pembayaran</th>
                                    <th class="text-end">Total Tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->orders as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_number }}</td>
                                        <td>{{ $order->order_date->translatedFormat('d M Y') }}</td>
                                        <td>
                                            @php $payStatus = $order->latestInvoice?->payment_status ?? 'unpaid'; @endphp
                                            <span
                                                class="badge rounded-pill {{ $payStatus === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }}">
                                                {{ $order->latestInvoice?->status_label ?? 'Belum Dibayar' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">Rp
                                            {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Customer ini belum memiliki
                                            riwayat pesanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
