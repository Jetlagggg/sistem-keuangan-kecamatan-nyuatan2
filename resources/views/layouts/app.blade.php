<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Keuangan') - KECAMATAN NYUATAN</title>
    <meta name="theme-color" content="#f2f1ea">
    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/buku-kas.css') }}">
    @stack('styles')
</head>
<body>
    <a class="skip-link" href="#konten">Lewati ke konten</a>

    <header class="masthead">
        <div class="masthead-inner">
            <div class="masthead-bar">
                <a class="brand" href="{{ route('transactions.index') }}">
                    <img class="brand-mark" src="{{ asset('img/kubar.png') }}" alt="">
                    <span class="brand-text">
                        <b>KANTOR KECAMATAN NYUATAN</b>
                        <span>Sistem Informasi Keuangan · Buku Kas</span>
                    </span>
                </a>
                <div class="masthead-meta">Tahun Anggaran {{ date('Y') }} · <b>Kecamatan Nyuatan</b></div>
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Buka navigasi">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navMain">
                <div class="masthead-nav">
                    <nav class="mainnav" aria-label="Navigasi utama">
                        <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">Transaksi</a>
                        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*', 'budget.*') ? 'active' : '' }}">Anggaran</a>
                        <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">Riwayat</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Pengguna</a>
                        <a href="{{ route('arsip.index') }}" class="{{ request()->routeIs('arsip.*') ? 'active' : '' }}">Arsip</a>
                        @endif
                    </nav>
                    <div class="masthead-actions">
                        @can('create', App\Models\BudgetStage::class)
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalInputGU">
                            <i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Input Dana (GU)
                        </button>
                        @endcan
                        <button class="btn btn-ghost btn-sm btn-icon theme-toggle" type="button" data-theme-toggle aria-pressed="false" aria-label="Aktifkan mode gelap" title="Mode gelap">
                            <i class="fa-solid fa-moon" data-theme-icon aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('profile.edit') }}" class="user-link" title="Profil saya">
                            <i class="fa-regular fa-user" aria-hidden="true"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="role">{{ ucfirst(auth()->user()->role) }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-ghost btn-sm btn-icon" title="Keluar" aria-label="Keluar dari sistem"><i class="fa-solid fa-power-off" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="konten" class="shell">
        @include('partials.alerts')
        @yield('content')
    </main>

    <footer class="site-foot">
        <span>Kantor Kecamatan Nyuatan · Sistem Informasi Keuangan</span>
        <span>Buku Kas · Tahun Anggaran {{ date('Y') }}</span>
    </footer>

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
