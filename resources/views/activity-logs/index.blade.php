@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
    <div class="mb-3">
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Riwayat Aktivitas</h5>
        <p class="text-muted small mb-0">Catatan audit seluruh perubahan data.</p>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form action="{{ route('activity-logs.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Pengguna</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Aksi</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Tabel</label>
                        <select name="table_name" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            @foreach($tables as $tb)
                                <option value="{{ $tb }}" {{ request('table_name') === $tb ? 'selected' : '' }}>{{ $tb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted fw-bold">Keterangan</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}" placeholder="Kata kunci...">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-sm fw-bold"><i class="fa-solid fa-filter me-1"></i> Terapkan</button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-light btn-sm fw-bold">Reset</a>
                    </div>
                </div>
            </form>
        </div>
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
