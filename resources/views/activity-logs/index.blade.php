@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
    <div class="mb-3">
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Riwayat Aktivitas</h5>
        <p class="text-muted small mb-0">Catatan audit seluruh perubahan data.</p>
    </div>

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
@endsection
