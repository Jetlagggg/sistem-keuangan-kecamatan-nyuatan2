<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monev Anggaran - Kecamatan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .header-monev { background-color: #1e293b; color: white; padding: 20px 0; margin-bottom: 30px; border-bottom: 4px solid #f59e0b; }
        .card-summary { border: none; border-radius: 12px; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .card-summary:hover { transform: translateY(-5px); }
        .card-pagu { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; }
        .card-serap { background: linear-gradient(135deg, #334155 0%, #1e293b 100%); color: white; }
        .card-sisa { background: linear-gradient(135deg, #475569 0%, #334155 100%); color: white; }
        .btn-action { border-radius: 8px; font-weight: 600; padding: 8px 16px; }
        .table-gu thead { background-color: #e2e8f0; }
    </style>
</head>
<body>

    <div class="header-monev shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-chart-pie fs-2 me-3 text-warning"></i>
                <div>
                    <h4 class="fw-bold mb-0">MONEV ANGGARAN</h4>
                    <small class="opacity-75">Monitoring & Evaluasi Keuangan Kecamatan</small>
                </div>
            </div>
            <div>
                <button class="btn btn-warning btn-action me-2 text-dark shadow" data-bs-toggle="modal" data-bs-target="#modalInputGU">
                    <i class="fa-solid fa-circle-plus me-2"></i> Input Dana (GU)
                </button>

                <a href="{{ route('transactions.index') }}" class="btn btn-light btn-action">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Transaksi
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        
        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        </div>
        @endif

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
                    <h2 class="fw-bold {{ $sisaAlokasi < 0 ? 'text-danger' : 'text-danger' }} mb-0">
                        Rp {{ number_format($sisaAlokasi, 0, ',', '.') }}
                    </h2>
                    <small class="opacity-50">Dana yang belum dibagi ke Pos Kegiatan</small>
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
                                <td>{{ date('d M Y', strtotime($stg->tanggal_cair)) }}</td>
                                <td class="text-muted small">{{ $stg->keterangan ?? '-' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($stg->nominal, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('budget.destroy', $stg->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus dana ini? Pagu kantor akan berkurang.')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
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
            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fa-solid fa-plus me-2"></i> Buat Pos Baru
            </button>
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
                            <button class="btn btn-sm btn-outline-warning fw-bold px-3" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $category->id }}">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus Pos ini? Semua transaksi terkait akan hilang!')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger fw-bold px-3">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
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

    </div>

    <div class="modal fade" id="modalInputGU" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('budget.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Input Pencairan Dana</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">JENIS TAHAPAN</label>
                            <input class="form-control fw-bold" list="listTahapan" name="nama_tahap" placeholder="Ketik manual (Contoh: GU 5)" required autocomplete="off">
                            <datalist id="listTahapan">
                                <option value="UP"><option value="GU 1"><option value="GU 2"><option value="GU 3"><option value="GU 4"><option value="LS"><option value="TU">
                            </datalist>
                            <small class="text-muted fst-italic" style="font-size: 11px;">*Klik panah untuk pilih cepat, atau ketik sendiri (Misal: GU 20).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">NOMINAL (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" name="nominal" class="form-control input-rupiah" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">TANGGAL CAIR</label>
                            <input type="date" name="tanggal_cair" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">KETERANGAN</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Dana</button>
                    </div>
                </div>
            </form>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SCRIPT FORMAT RUPIAH
        document.addEventListener('keyup', function(e) {
            if (e.target && e.target.classList.contains('input-rupiah')) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) {
                    e.target.value = new Intl.NumberFormat('id-ID').format(value);
                } else {
                    e.target.value = '';
                }
            }
        });
    </script>
</body>
</html>