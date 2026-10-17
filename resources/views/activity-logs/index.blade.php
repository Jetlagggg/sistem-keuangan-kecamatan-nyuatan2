<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - KECAMATAN NYUATAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
</head>
<body class="bg-light" style="font-family: 'Inter', sans-serif;">

    <nav class="navbar navbar-dark mb-4 pt-3 pb-3" style="background: linear-gradient(to right, #1e3a8a, #1e40af);">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Aktivitas</span>
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-list me-1"></i> Transaksi
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-tags me-1"></i> Anggaran
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fa-solid fa-users-gear me-1"></i> Pengguna
                </a>
                <a href="{{ route('arsip.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-box-archive me-1"></i> Arsip
                </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-4">Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Tabel</th>
                                <th>Keterangan</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 small text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->user->name ?? 'Sistem' }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->action === 'delete' ? 'danger' : ($log->action === 'update' ? 'warning text-dark' : 'success') }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="small">{{ $log->table_name }} #{{ $log->record_id }}</td>
                                <td class="small text-muted">{{ $log->description }}</td>
                                <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada aktivitas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>

</body>
</html>
