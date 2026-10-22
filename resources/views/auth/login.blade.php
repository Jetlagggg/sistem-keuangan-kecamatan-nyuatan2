<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Buku Kas Kecamatan Nyuatan</title>
    <meta name="theme-color" content="#f2f1ea">
    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/buku-kas.css') }}">
</head>
<body class="login-page">
    <button class="btn btn-ghost btn-sm btn-icon login-theme theme-toggle" type="button" data-theme-toggle aria-pressed="false" aria-label="Aktifkan mode gelap" title="Mode gelap">
        <i class="fa-solid fa-moon" data-theme-icon aria-hidden="true"></i>
    </button>
    <main class="login-shell">
        <div class="login-sheet">
            <div class="login-head">
                <img src="{{ asset('img/kubar.png') }}" alt="Lambang Kabupaten Kubar">
                <div class="login-org">
                    KANTOR KECAMATAN NYUATAN
                    <span>Sistem Informasi Keuangan · Buku Kas</span>
                </div>
            </div>

            <div class="login-body">
                <h1 class="login-title">Masuk ke Buku Kas</h1>
                <p class="login-sub">Gunakan email pegawai dan kata sandi yang terdaftar.</p>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">Email Pegawai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                            <input type="email" name="email" id="email" class="form-control" placeholder="nama@nyuatan.go.id" value="{{ old('email') }}" required autofocus autocomplete="username">
                        </div>
                        @error('email')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Kata sandi" required autocomplete="current-password">
                        </div>
                        @error('password')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        Masuk Sistem <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <div class="login-foot">
                <span>Akses terbatas</span>
                <span>&copy; {{ date('Y') }} Kecamatan Nyuatan</span>
            </div>
        </div>
        <p class="login-meta">Buku kas internal kantor kecamatan</p>
    </main>
</body>
</html>
