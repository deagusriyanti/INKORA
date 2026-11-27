@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <!-- KOLOM UTAMA KIRI (8 Kolom) -->
        <div class="col-xl-8">
            <!-- 3 Kartu Statistik Pastel Interaktif -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-pastel-card bg-pastel-lavender h-100">
                        <div class="stat-icon-badge">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="small fw-medium text-dark">Pembayaran Masuk</div>
                        <h4 class="stat-value">
                            Rp <span class="count-up"
                                data-target="{{ (int) $stats['total_paid'] }}">{{ number_format($stats['total_paid'], 0, ',', '.') }}</span>
                        </h4>
                        <div class="small text-dark opacity-75">Total kas masuk valid</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-pastel-card bg-pastel-blue h-100">
                        <div class="stat-icon-badge">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="small fw-medium text-dark">Total Pesanan</div>
                        <h4 class="stat-value">
                            <span class="count-up"
                                data-target="{{ (int) $stats['total_orders'] }}">{{ number_format($stats['total_orders']) }}</span>
                        </h4>
                        <div class="small text-dark opacity-75">{{ $stats['unpaid_invoices_count'] }} tagihan belum lunas
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-pastel-card bg-pastel-mint h-100">
                        <div class="stat-icon-badge">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <div class="small fw-medium text-dark">Rasio Pelunasan</div>
                        <h4 class="stat-value">
                            <span class="count-up"
                                data-target="{{ (int) $stats['collection_rate'] }}">{{ $stats['collection_rate'] }}</span>%
                        </h4>
                        <div class="small text-dark opacity-75">Tagihan Rp
                            {{ number_format($stats['total_billed'], 0, ',', '.') }}</div>
                        <div class="collection-progress-track">
                            <div class="collection-progress-fill" id="collectionProgressFill"
                                data-rate="{{ min(100, max(0, (int) $stats['collection_rate'])) }}"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Tengah: Grafik Transaksi Interaktif + Analisis Cepat -->
            <div class="row g-3 mb-4">
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                <div>
                                    <h5 class="fw-bold mb-0">Grafik Transaksi</h5>
                                    <small class="text-muted">7 hari terakhir (ribuan Rp)</small>
                                </div>
                                <div class="inkora-filter-group" id="chartFilterGroup">
                                    <button type="button" class="inkora-filter-btn active" data-series="all">Semua</button>
                                    <button type="button" class="inkora-filter-btn" data-series="billed">Tagihan</button>
                                    <button type="button" class="inkora-filter-btn" data-series="paid">Pembayaran</button>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="inkoraBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="fw-bold mb-1">Analisis Cepat</h5>
                                <p class="text-muted small mb-3">Akses ringkasan operasional harian</p>

                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('invoices.index') ? route('invoices.index') : '#' }}"
                                        class="analysis-pill">
                                        <span><i class="bi bi-exclamation-circle me-2"></i>Tagihan Belum Lunas
                                            ({{ $stats['unpaid_invoices_count'] }})</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('payments.index') ? route('payments.index') : '#' }}"
                                        class="analysis-pill">
                                        <span><i class="bi bi-wallet2 me-2"></i>Input Pembayaran</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ route('products.index') }}" class="analysis-pill">
                                        <span><i class="bi bi-box-seam me-2"></i>Katalog Produk & Jasa</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                </div>
                            </div>

                            <div
                                class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between small text-muted">
                                <span>Total Pelanggan Aktif</span>
                                <a href="{{ route('customers.index') }}" class="btn btn-lime btn-sm py-1 px-3">
                                    {{ $stats['total_customers'] }} Pelanggan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pesanan Terbaru dengan Filter Instan -->
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">Pesanan Terbaru</h5>
                            <small class="text-muted">Pantau status pembayaran pesanan terkini</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="inkora-filter-group" id="orderFilterGroup">
                                <button type="button" class="inkora-filter-btn active" data-status="all">Semua</button>
                                <button type="button" class="inkora-filter-btn" data-status="paid">Lunas</button>
                                <button type="button" class="inkora-filter-btn" data-status="unpaid">Belum Lunas</button>
                            </div>
                            <a href="{{ \Illuminate\Support\Facades\Route::has('orders.index') ? route('orders.index') : route('customers.index') }}"
                                class="btn btn-lime btn-sm">
                                Semua
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="recentOrdersTable">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Pelanggan</th>
                                    <th>No. Pesanan</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $payStatus = $order->latestInvoice?->payment_status ?? 'unpaid';
                                        $filterGroup = $payStatus === 'paid' ? 'paid' : 'unpaid';
                                    @endphp
                                    <tr data-pay-status="{{ $filterGroup }}">
                                        <td class="fw-semibold">{{ $order->customer->customer_name }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
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
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data transaksi
                                            pesanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (4 Kolom) -->
        <div class="col-xl-4">
            <!-- Kartu Sorotan Edisi dengan Live Indicator -->
            <div class="teal-edition-card mb-4 shadow-sm">
                <div class="small text-uppercase opacity-75 fw-medium mb-1 d-flex align-items-center">
                    <span class="live-pulse-dot"></span> Sistem Manajemen Percetakan
                </div>
                <h3 class="fw-bold mb-1">Inkora {{ strtoupper(\App\Services\FeatureGateService::grade()) }}</h3>
                @feature('production_tracking')
                    <p class="small opacity-75 mb-4">{{ $stats['in_progress_orders'] }} pesanan sedang dalam proses produksi
                        aktif.</p>
                @else
                    <p class="small opacity-75 mb-4">Kelola pesanan, tagihan, dan pelanggan percetakan dengan rapi.</p>
                @endfeature
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('orders.create') ? route('orders.create') : route('customers.create') }}"
                        class="btn btn-lime fw-semibold px-3 py-2">
                        <i class="bi bi-plus-lg me-1"></i>
                        {{ \Illuminate\Support\Facades\Route::has('orders.create') ? 'Buat Pesanan' : 'Tambah Pelanggan' }}
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-light rounded-3 px-3 py-2">
                        <i class="bi bi-box-seam me-1"></i> Tambah Produk
                    </a>
                </div>
            </div>

            <!-- Kartu Status Operasional -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span
                            class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary avatar-circle-sm">
                            <i class="bi bi-layers"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0">Status Operasional</h6>
                            <small class="text-muted">{{ $stats['queued_orders'] }} Antrean &bull;
                                {{ $stats['unpaid_invoices_count'] }} Tagihan</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Pantau seluruh tagihan yang belum lunas (Rp
                        {{ number_format($stats['unpaid_invoices_amount'], 0, ',', '.') }}) atau segera catat pelunasan
                        dari pelanggan.
                    </p>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('payments.index') ? route('payments.index') : route('customers.index') }}"
                        class="btn btn-dark w-100 rounded-3 py-2 fw-medium">
                        Catat Pembayaran Sekarang
                    </a>
                </div>
            </div>

            <!-- Pelanggan Teraktif -->
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Pelanggan Teraktif</h6>
                        <a href="{{ route('customers.index') }}" class="small auth-link-teal">Semua</a>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @forelse($topCustomers as $cust)
                            <a href="{{ route('customers.show', $cust) }}" class="customer-row-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-semibold avatar-circle-md">
                                        {{ strtoupper(substr($cust->customer_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $cust->customer_name }}</div>
                                        <div class="text-muted small">{{ $cust->orders_count }} Pesanan</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </a>
                        @empty
                            <div class="text-muted small">Belum ada data pelanggan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // 1. Animasi Progress Bar Rasio Pelunasan
                const progressFill = document.getElementById('collectionProgressFill');
                if (progressFill) {
                    requestAnimationFrame(() => {
                        progressFill.style.width = (progressFill.dataset.rate || 0) + '%';
                    });
                }

                // 2. Animasi Count-Up Halus pada Angka KPI
                document.querySelectorAll('.count-up').forEach(el => {
                    const target = parseInt(el.dataset.target || '0', 10);
                    if (target <= 0) return;
                    const duration = 650;
                    const startTime = performance.now();
                    const step = (now) => {
                        const progress = Math.min((now - startTime) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        el.textContent = Math.round(target * eased).toLocaleString('id-ID');
                        if (progress < 1) requestAnimationFrame(step);
                    };
                    requestAnimationFrame(step);
                });

                // 3. Chart.js Interaktif dengan Filter Dataset
                const ctx = document.getElementById('inkoraBarChart');
                if (ctx) {
                    const chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                    label: 'Total Tagihan (Rb)',
                                    data: @json($chartOrders),
                                    backgroundColor: '#b8b5ff',
                                    hoverBackgroundColor: '#9b96ff',
                                    borderRadius: 7,
                                    barPercentage: 0.48
                                },
                                {
                                    label: 'Pembayaran Masuk (Rb)',
                                    data: @json($chartRevenue),
                                    backgroundColor: '#8cd4d4',
                                    hoverBackgroundColor: '#69c2c2',
                                    borderRadius: 7,
                                    barPercentage: 0.48
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#181b23',
                                    padding: 10,
                                    cornerRadius: 10,
                                    callbacks: {
                                        label: (context) => {
                                            const val = (context.parsed.y || 0) * 1000;
                                            return ` ${context.dataset.label.replace(' (Rb)', '')}: Rp ${val.toLocaleString('id-ID')}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f1f5f9'
                                    },
                                    border: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    document.querySelectorAll('#chartFilterGroup .inkora-filter-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            document.querySelectorAll('#chartFilterGroup .inkora-filter-btn').forEach(
                                b => b.classList.remove('active'));
                            btn.classList.add('active');
                            const mode = btn.dataset.series;
                            chartInstance.setDatasetVisibility(0, mode === 'all' || mode === 'billed');
                            chartInstance.setDatasetVisibility(1, mode === 'all' || mode === 'paid');
                            chartInstance.update();
                        });
                    });
                }

                // 4. Filter Instan Tabel Pesanan Terbaru
                document.querySelectorAll('#orderFilterGroup .inkora-filter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('#orderFilterGroup .inkora-filter-btn').forEach(b => b
                            .classList.remove('active'));
                        btn.classList.add('active');
                        const filter = btn.dataset.status;
                        document.querySelectorAll('#recentOrdersTable tbody tr[data-pay-status]')
                            .forEach(row => {
                                row.style.display = (filter === 'all' || row.dataset.payStatus ===
                                    filter) ? '' : 'none';
                            });
                    });
                });
            });
        </script>
    @endpush
@endsection
