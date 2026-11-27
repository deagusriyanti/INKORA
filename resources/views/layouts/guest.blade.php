<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - {{ $globalCompany->company_name ?? 'Inkora' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/inkora.css') }}" rel="stylesheet">
</head>

<body class="guest-body">
    <div class="auth-wrapper">
        <div class="row g-0">
            <!-- Panel Kiri: Identitas & Sorotan Sistem -->
            <div class="col-md-5 d-none d-md-block">
                <div class="auth-brand-panel">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <span class="auth-brand-icon"><i class="bi bi-layers-half"></i></span>
                            <span class="inkora-brand-title text-white">INKORA</span>
                        </div>
                        <h3 class="fw-bold mb-3">Kelola Operasional Percetakan Lebih Rapi & Cepat.</h3>
                        <p class="small opacity-75 mb-0">
                            Pantau ringkasan transaksi, kelola data pelanggan, serta katalog produk dan jasa cetak dalam
                            satu platform terpadu.
                        </p>
                    </div>

                    <div class="auth-highlight-box mt-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill text-dark fw-bold px-3 py-1 bg-pastel-mint">
                                {{ \App\Services\FeatureGateService::gradeLabel() }}
                            </span>
                        </div>
                        <div class="small opacity-90">
                            {{ $globalCompany->company_name ?? 'Inkora Digital Printing' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Kanan: Form Autentikasi -->
            <div class="col-md-7">
                <div class="auth-form-panel">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
