@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Riwayat Aktivitas</h1>
            <p class="page-sub">Catatan audit seluruh perubahan data. Setiap baris menyimpan pengguna, waktu, dan jejak alamat IP.</p>
        </div>
        <div class="page-actions">
            <span class="meta-line">{{ $logs->total() }} catatan</span>
        </div>
    </div>

    <section class="sheet" aria-labelledby="filter-title">
        <div class="sheet-head">
            <h2 class="sheet-title" id="filter-title">Filter Jejak Audit</h2>
            <span class="sheet-meta">Persempit catatan berdasarkan pelaku dan waktu</span>
        </div>
        <div class="sheet-body">
            <form action="{{ route('activity-logs.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-user">Pengguna</label>
                        <select name="user_id" id="filter-user" class="form-select">
                            <option value="">— Semua —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-action">Aksi</label>
                        <select name="action" id="filter-action" class="form-select">
                            <option value="">— Semua —</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-table">Tabel</label>
                        <select name="table_name" id="filter-table" class="form-select">
                            <option value="">— Semua —</option>
                            @foreach($tables as $tb)
                                <option value="{{ $tb }}" {{ request('table_name') === $tb ? 'selected' : '' }}>{{ $tb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-start">Dari Tanggal</label>
                        <input type="date" name="start_date" id="filter-start" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-end">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="filter-end" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label" for="filter-keyword">Keterangan</label>
                        <input type="text" name="keyword" id="filter-keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Kata kunci">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1" aria-hidden="true"></i> Terapkan</button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="sheet" aria-labelledby="log-title">
        <div class="sheet-head">
            <h2 class="sheet-title" id="log-title">Jejak Audit</h2>
            <span class="sheet-meta">50 catatan per halaman</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Waktu</th>
                        <th scope="col">Pengguna</th>
                        <th scope="col">Aksi</th>
                        <th scope="col">Tabel</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $aksi = match ($log->action) {
                            'create' => ['Tambah', 'tag-green'],
                            'update' => ['Ubah', 'tag-muted'],
                            'delete' => ['Hapus', 'tag-red'],
                            'restore' => ['Pulihkan', 'tag-green'],
                            'force-delete' => ['Hapus permanen', 'tag-red'],
                            default => [ucfirst($log->action), 'tag-muted'],
                        };
                    @endphp
                    <tr>
                        <td class="cell-date">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Sistem' }}</td>
                        <td><span class="tag {{ $aksi[1] }}">{{ $aksi[0] }}</span></td>
                        <td class="cell-date">{{ $log->table_name }} #{{ $log->record_id }}</td>
                        <td>{{ $log->description }}</td>
                        <td class="cell-date">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="6">Belum ada aktivitas tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>
        {{ $logs->links() }}
    </div>
@endsection
