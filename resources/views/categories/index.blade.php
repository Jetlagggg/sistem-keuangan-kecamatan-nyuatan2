@extends('layouts.app')

@section('title', 'Monev Anggaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-chart-pie fs-2 me-3 text-primary"></i>
            <div>
                <h4 class="fw-bold mb-0">MONEV ANGGARAN</h4>
                <small class="text-muted">Monitoring &amp; Evaluasi Keuangan Kecamatan</small>
            </div>
        </div>
        @can('create', App\Models\Category::class)
        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fa-solid fa-plus me-2"></i> Buat Pos Baru
        </button>
        @endcan
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-summary card-pagu h-100 p-4">
                <h6 class="text-uppercase text-white-50 fw-bold small mb-2">Total Pagu Kantor (UP + GU)</h6>
                <h2 class="fw-bold text-warning mb-0">Rp {{ number_format($totalPagu, 0, ',', '.') }}</h2>
                <small class="opacity-50">Akumulasi dana masuk</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary card-serap h-100 p-4">
                <h6 class="text-uppercase text-white-50 fw-bold small mb-2">Total Terserap</h6>
                <h2 class="fw-bold mb-0">Rp {{ number_format($totalTerserap, 0, ',', '.') }}</h2>

                <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.1);">
                    <div class="progress-bar bg-info" style="width: {{ $persen }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-info fw-bold">{{ number_format($persen, 1) }}%</small>
                    <small class="opacity-50">Terealisasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary card-sisa h-100 p-4">
                <h6 class="text-uppercase text-white-50 fw-bold small mb-2">Sisa Alokasi Anggaran</h6>
                <h2 class="fw-bold {{ $sisaAlokasi < 0 ? 'text-danger' : 'text-success' }} mb-0">
                    Rp {{ number_format($sisaAlokasi, 0, ',', '.') }}
                </h2>
                @if($sisaAlokasi < 0)
                    <small class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Alokasi pos melebihi pagu kantor!</small>
                @else
                    <small class="opacity-50">Dana yang belum dibagi ke Pos Kegiatan</small>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-history me-2 text-primary"></i> Riwayat Pencairan Dana (UP / GU)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-gu mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Tahapan</th>
                            <th>Tanggal Cair</th>
                            <th>Keterangan</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stages as $stg)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary">{{ $stg->nama_tahap }}</span>
                            </td>
                            <td>{{ $stg->tanggal_cair->format('d M Y') }}</td>
                            <td class="text-muted small">{{ $stg->keterangan ?? '-' }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($stg->nominal, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @can('delete', $stg)
                                <form action="{{ route('budget.destroy', $stg) }}" method="POST" onsubmit="return confirm('Hapus dana ini? Pagu kantor akan berkurang.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data UP/GU. Silakan input tombol kuning di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Pos Kegiatan</h5>
            <p class="text-muted small mb-0">Rincian pembagian anggaran per kegiatan.</p>
        </div>
    </div>

    <div class="row">
        @foreach($categories as $category)
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3 text-primary">
                                <i class="fa-solid fa-folder-open fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">{{ $category->nama }}</h6>
                                <small class="text-muted">Pagu: Rp {{ number_format($category->budget, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <h6 class="fw-bold text-success mb-0">Rp {{ number_format($category->sisa_saat_ini, 0, ',', '.') }}</h6>
                            <small class="text-muted" style="font-size: 11px;">Sisa Dana</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between small mb-1">
                        <span>Realisasi: Rp {{ number_format($category->terpakai_saat_ini, 0, ',', '.') }}</span>
                        <span class="fw-bold {{ $category->persen_realisasi >= 100 ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($category->persen_realisasi, 1) }}%
                        </span>
                    </div>

                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar {{ $category->persen_realisasi >= 100 ? 'bg-danger' : 'bg-warning' }}"
                             role="progressbar"
                             style="width: {{ $category->persen_realisasi }}%">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        @can('update', $category)
                        <button class="btn btn-sm btn-outline-warning fw-bold px-3"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $category->id }}">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        @endcan
                        @can('delete', $category)
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus Pos ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger fw-bold px-3">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Pos Anggaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Pos Kegiatan</label>
                                    <input type="text" name="nama" class="form-control" value="{{ $category->nama }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pagu Anggaran (Rp)</label>
                                    <input type="text" name="budget" class="form-control input-rupiah"
                                        value="{{ number_format($category->budget, 0, ',', '.') }}" required>
                                    <small class="text-muted">Total dana maksimal untuk pos ini.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning fw-bold">Update Pos</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Buat Pos Kegiatan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Pos Kegiatan</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Belanja ATK" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alokasi Pagu (Rp)</label>
                            <input type="text" name="budget" class="form-control input-rupiah" placeholder="0" required>
                            <small class="text-muted">Total dana yang dialokasikan untuk pos ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Pos</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
