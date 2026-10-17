<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="container text-center">
        <i class="fa-solid fa-lock text-danger" style="font-size: 72px;"></i>
        <h1 class="fw-bold mt-4">403</h1>
        <p class="text-muted">Anda tidak memiliki hak akses untuk halaman ini.</p>
        <a href="{{ url('/') }}" class="btn btn-primary fw-bold mt-2"><i class="fa-solid fa-house me-1"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
