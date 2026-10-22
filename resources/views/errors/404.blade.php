<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <meta name="theme-color" content="#f2f1ea">
    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/buku-kas.css') }}">
</head>
<body>
    <main class="error-page">
        <section class="error-sheet">
            <p class="error-code">404</p>
            <h1 class="error-title">Halaman tidak ditemukan</h1>
            <p class="error-text">Alamat yang Anda buka tidak ada di buku kas ini. Periksa kembali tautan atau kembali ke beranda.</p>
            <div class="error-rule"></div>
            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
        </section>
    </main>
</body>
</html>
