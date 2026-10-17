<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Keuangan') - KECAMATAN NYUATAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .navbar-camat { background: linear-gradient(to right, #1e3a8a, #1e40af); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .card-header-camat { background-color: #fff; border-bottom: 2px solid #e5e7eb; color: #1e3a8a; font-weight: 700; padding: 15px 20px; }
        .btn-simpan { background-color: #d97706; color: white; font-weight: 600; }
        .text-masuk { color: #059669; font-weight: bold; background-color: #ecfdf5; padding: 2px 8px; border-radius: 4px; }
        .text-keluar { color: #dc2626; font-weight: bold; background-color: #fef2f2; padding: 2px 8px; border-radius: 4px; }
        .card-summary { border: none; border-radius: 12px; transition: transform .2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); }
        .card-summary:hover { transform: translateY(-5px); }
        .card-pagu { background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; }
        .card-serap { background: linear-gradient(135deg, #334155, #1e293b); color: #fff; }
        .card-sisa { background: linear-gradient(135deg, #475569, #334155); color: #fff; }
        .table-gu thead { background-color: #e2e8f0; }
        .footer { width: 100%; text-align: right; margin-top: 30px; padding-right: 40px; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-camat mb-4 pt-3 pb-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('transactions.index') }}">
                <i class="fa-solid fa-landmark me-3 fs-4 text-warning"></i>
                <div>
                    <span class="d-block fw-bold">KANTOR KECAMATAN NYUATAN</span>
                    <span style="font-size: 12px; opacity: 0.8;">Sistem Informasi Keuangan Digital</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <div class="ms-auto d-flex align-items-center flex-wrap gap-2 mt-2 mt-lg-0">
                    @can('create', App\Models\BudgetStage::class)
                    <button class="btn btn-warning btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputGU">
                        <i class="fa-solid fa-money-bill-wave me-1"></i> Input Dana (GU)
                    </button>
                    @endcan
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list me-1"></i> Transaksi
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('categories.*', 'budget.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags me-1"></i> Anggaran
                    </a>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear me-1"></i> Pengguna
                    </a>
                    <a href="{{ route('arsip.index') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('arsip.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-archive me-1"></i> Arsip
                    </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-sm {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil saya">
                        <i class="fa-solid fa-circle-user me-1"></i> {{ auth()->user()->name }}
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger btn-sm" title="Keluar"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @include('partials.alerts')
        @yield('content')
    </div>

    @include('partials.modal-input-gu')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('keyup', function (e) {
            if (e.target && e.target.classList.contains('input-rupiah')) {
                let val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
