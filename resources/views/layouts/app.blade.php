<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $globalCompany->company_name ?? 'Inkora' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/inkora.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="app-body">
    <div class="d-flex app-shell">
        <!-- Sidebar Kiri (Terkunci / Tidak Bisa Di-scroll) -->
        <aside class="inkora-sidebar flex-shrink-0" id="appSidebar">
            <div>
                <a href="{{ route('dashboard') }}" class="inkora-brand">
                    @if (!empty($globalCompany->logo))
                        <img src="{{ asset('storage/' . $globalCompany->logo) }}" alt="Logo" width="34"
                            height="34" class="rounded-3 object-fit-cover">
                    @else
                        <span class="inkora-brand-icon"><i class="bi bi-layers-half"></i></span>
                    @endif
                    <span class="inkora-brand-title">INKORA</span>
                </a>

                <nav class="nav flex-column inkora-nav">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>

                    <div class="inkora-nav-label">Transaksi</div>
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                        href="{{ \Illuminate\Support\Facades\Route::has('orders.index') ? route('orders.index') : '#' }}">
                        <i class="bi bi-receipt-cutoff"></i> Pesanan
                    </a>
                    <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
                        href="{{ \Illuminate\Support\Facades\Route::has('invoices.index') ? route('invoices.index') : '#' }}">
                        <i class="bi bi-file-earmark-text"></i> Invoice
                    </a>
                    <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                        href="{{ \Illuminate\Support\Facades\Route::has('payments.index') ? route('payments.index') : '#' }}">
                        <i class="bi bi-wallet2"></i> Pembayaran
                    </a>

                    <div class="inkora-nav-label">Master Data</div>
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                        href="{{ route('customers.index') }}">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="bi bi-box-seam"></i> Produk & Jasa
                    </a>

                    @feature('billing_reminders')
                        <div class="inkora-nav-label">Penagihan</div>
                        <a class="nav-link" href="#"><i class="bi bi-bell"></i> Pengingat Tagihan</a>
                    @endfeature

                    @feature('production_tracking')
                        <div class="inkora-nav-label">Operasional</div>
                        <a class="nav-link" href="#"><i class="bi bi-cpu"></i> Produksi</a>
                    @endfeature

                    @feature('delivery_tracking')
                        <a class="nav-link" href="#"><i class="bi bi-truck"></i> Pengiriman</a>
                    @endfeature

                    <div class="inkora-nav-label">Sistem</div>
                    <a class="nav-link" href="#"><i class="bi bi-bar-chart-line"></i> Laporan</a>
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                        href="{{ \Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : '#' }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </nav>
            </div>

            <!-- Tombol Logout di Bawah Sidebar -->
            <div class="sidebar-footer mt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-sidebar-logout">
                        <i class="bi bi-box-arrow-left"></i> Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama Kanan (Area yang Dapat Di-scroll) -->
        <div class="flex-grow-1 d-flex flex-column inkora-main-area">
            <header class="inkora-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light border d-lg-none rounded-3" type="button"
                        onclick="document.getElementById('appSidebar').classList.toggle('show')">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h3 class="page-heading">@yield('title', 'Dashboard')</h3>
                        <div class="text-muted small">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }} &bull;
                            {{ $globalCompany->company_name ?? 'Inkora Digital Printing' }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('orders.create') ? route('orders.create') : '#' }}"
                        class="icon-circle-btn" title="Buat Pesanan Baru">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('invoices.index') ? route('invoices.index') : '#' }}"
                        class="icon-circle-btn" title="Cek Invoice">
                        <i class="bi bi-bell"></i>
                    </a>
                    <div class="topbar-user-pill ms-1">
                        @php
                            $initials = collect(explode(' ', auth()->user()->name ?? 'AD'))
                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <div class="topbar-avatar">{{ $initials }}</div>
                        <div class="d-none d-sm-block lh-sm">
                            <div class="fw-bold small">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-muted small">{{ \App\Services\FeatureGateService::gradeLabel() }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 py-3 flex-grow-1">
                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm alert-dismissible fade show"
                        role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm alert-dismissible fade show"
                        role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm alert-dismissible fade show"
                        role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
