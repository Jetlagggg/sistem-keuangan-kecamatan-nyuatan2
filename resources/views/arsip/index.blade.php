<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Data - KECAMATAN NYUATAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
</head>
<body class="bg-light" style="font-family: 'Inter', sans-serif;">

    <nav class="navbar navbar-dark mb-4 pt-3 pb-3" style="background: linear-gradient(to right, #1e3a8a, #1e40af);">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold"><i class="fa-solid fa-box-archive me-2 text-warning"></i>Arsip Data</span>
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-list me-1"></i> Transaksi
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-tags me-1"></i> Anggaran
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-users-gear me-1"></i> Pengguna
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div> @endif

        <p class="text-muted small">
            <i class="fa-solid fa-circle-info me-1"></i>
            Data yang dihapus tidak langsung hilang. Pulihkan untuk mengembalikannya, atau hapus permanen bila sudah tidak diperlukan.
        </p>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaksi Terarsip</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small"><tr><th class="ps-4">Tanggal</th><th>Uraian</th><th class="text-end">Nominal</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($transactions as $t)
                            <tr>
                                <td class="ps-4 small text-muted">{{ $t->tanggal?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $t->category->nama ?? 'Umum' }}</div>
                                    <div class="small text-muted">{{ $t->keterangan }}</div>
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="small text-muted">{{ $t->deleted_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center text-nowrap">
                                    <form action="{{ route('arsip.transactions.restore', $t->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                    </form>
                                    <form action="{{ route('arsip.transactions.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada transaksi terarsip.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-folder-open me-2 text-primary"></i>Pos Kegiatan Terarsip</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small"><tr><th class="ps-4">Nama Pos</th><th class="text-end">Pagu</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($categories as $c)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $c->nama }}</td>
                                <td class="text-end">Rp {{ number_format($c->budget, 0, ',', '.') }}</td>
                                <td class="small text-muted">{{ $c->deleted_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center text-nowrap">
                                    <form action="{{ route('arsip.categories.restore', $c->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                    </form>
                                    <form action="{{ route('arsip.categories.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada pos terarsip.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i>Dana (UP/GU) Terarsip</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small"><tr><th class="ps-4">Tahapan</th><th class="text-end">Nominal</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($stages as $s)
                            <tr>
                                <td class="ps-4"><span class="badge bg-primary">{{ $s->nama_tahap }}</span></td>
                                <td class="text-end fw-bold">Rp {{ number_format($s->nominal, 0, ',', '.') }}</td>
                                <td class="small text-muted">{{ $s->deleted_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center text-nowrap">
                                    <form action="{{ route('arsip.budget-stages.restore', $s->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                    </form>
                                    <form action="{{ route('arsip.budget-stages.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada dana terarsip.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
