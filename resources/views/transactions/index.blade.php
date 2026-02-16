<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - KECAMATAN NYUATAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .navbar-camat { background: linear-gradient(to right, #1e3a8a, #1e40af); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .card-header-camat { background-color: #fff; border-bottom: 2px solid #e5e7eb; color: #1e3a8a; font-weight: 700; padding: 15px 20px; }
        .btn-simpan { background-color: #d97706; color: white; font-weight: 600; }
        .text-masuk { color: #059669; font-weight: bold; background-color: #ecfdf5; padding: 2px 8px; border-radius: 4px; }
        .text-keluar { color: #dc2626; font-weight: bold; background-color: #fef2f2; padding: 2px 8px; border-radius: 4px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-camat mb-4 pt-3 pb-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-landmark me-3 fs-4 text-warning"></i>
                <div>
                    <span class="d-block fw-bold">KANTOR KECAMATAN NYUATAN</span>
                    <span style="font-size: 12px; opacity: 0.8;">Sistem Informasi Keuangan Digital</span>
                </div>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <button class="btn btn-warning btn-sm me-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputGU">
                    <i class="fa-solid fa-money-bill-wave me-1"></i> Input Dana (GU)
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm me-3">
                    <i class="fa-solid fa-tags me-1"></i> Atur Anggaran
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf <button class="btn btn-danger btn-sm"><i class="fa-solid fa-power-off"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div> @endif

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header-camat"><i class="fa-solid fa-pen-to-square me-2"></i> Input Transaksi Baru</div>
                    <div class="card-body p-4">
                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">TANGGAL</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">SUMBER DANA (GU)</label>
                                <select name="budget_stage_id" class="form-select" required>
                                    <option value="">-- Pilih Sumber Dana --</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->nama_tahap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">POS ANGGARAN</label>
                                <select name="category_id" id="pilihKategori" class="form-select" required>
                                    <option value="" data-sisa="0">-- Pilih Pos --</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" data-sisa="{{ $c->sisa_saat_ini }}">{{ $c->nama }} (Sisa: {{ number_format($c->sisa_saat_ini) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">NOMINAL (Rp)</label>
                                <input type="text" name="nominal" id="inputNominal" class="form-control input-rupiah" placeholder="0" required disabled>
                                <small id="pesanSisa" class="d-block mt-1 fw-bold" style="font-size: 11px;"></small>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">PENERIMA</label>
                                <input type="text" name="penerima" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-muted mb-1">URAIAN</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" id="btnSimpan" class="btn btn-simpan w-100 py-2" disabled>SIMPAN DATA</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="row mb-3">
                    <div class="col-md-4"><div class="card bg-success text-white p-3 text-center shadow-sm"><small>Total Pagu</small><h6 class="fw-bold">Rp {{ number_format($paguGlobal, 0, ',', '.') }}</h6></div></div>
                    <div class="col-md-4"><div class="card bg-danger text-white p-3 text-center shadow-sm"><small>Total Realisasi</small><h6 class="fw-bold">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h6></div></div>
                    <div class="col-md-4"><div class="card bg-primary text-white p-3 text-center shadow-sm"><small>Sisa Kas</small><h6 class="fw-bold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h6></div></div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-3 bg-white rounded">
                        <form action="{{ route('transactions.index') }}" method="GET" id="searchForm">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold">Filter GU</label>
                                    <select name="budget_stage_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua GU --</option>
                                        @foreach($stages as $stg)
                                            <option value="{{ $stg->id }}" {{ request('budget_stage_id') == $stg->id ? 'selected' : '' }}>
                                                {{ $stg->nama_tahap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold">Cari Uraian</label>
                                    <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold">Tanggal</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold"><i class="fa-solid fa-magnifying-glass"></i></button>
                                    <a href="{{ route('transactions.cetak', request()->all()) }}" target="_blank" class="btn btn-secondary btn-sm w-100 fw-bold">
                                        <i class="fa-solid fa-print"></i> CETAK
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small"><tr><th class="ps-4">TANGGAL</th><th>URAIAN</th><th class="text-end">NOMINAL</th><th class="text-center">AKSI</th></tr></thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                    <tr>
                                        <td class="ps-4 small text-muted">
                                            {{ date('d/m/Y', strtotime($t->tanggal)) }}
                                            <div class="mt-1"><span class="badge bg-light text-dark border">{{ $t->budgetStage->nama_tahap ?? 'Non-GU' }}</span></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $t->category->nama ?? 'Umum' }}</div>
                                            <div class="small text-muted">{{ $t->keterangan }}</div>
                                            @if($t->penerima) <div class="small text-primary fst-italic"><i class="fa-solid fa-user me-1"></i> {{ $t->penerima }}</div> @endif
                                        </td>
                                        <td class="text-end text-danger fw-bold">- Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-light text-danger"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Data kosong.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInputGU" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('budget.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning"><h5 class="modal-title fw-bold">Input Pencairan Dana</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Jenis Tahapan</label><input class="form-control" list="listTahapan" name="nama_tahap" required><datalist id="listTahapan"><option value="UP"><option value="GU 1"><option value="GU 2"></datalist></div>
                        <div class="mb-3"><label>Nominal</label><input type="text" name="nominal" class="form-control input-rupiah" required></div>
                        <div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal_cair" class="form-control" required></div>
                        <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary fw-bold">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const pilihKategori = document.getElementById('pilihKategori');
        const inputNominal = document.getElementById('inputNominal');
        const btnSimpan = document.getElementById('btnSimpan');
        const pesanSisa = document.getElementById('pesanSisa');

        if(pilihKategori && inputNominal) {
            pilihKategori.addEventListener('change', function() {
                const sisa = parseFloat(this.options[this.selectedIndex].getAttribute('data-sisa')) || 0;
                inputNominal.disabled = false; inputNominal.value = '';
                pesanSisa.innerHTML = `Sisa Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
            });
            inputNominal.addEventListener('keyup', function() {
                let val = this.value.replace(/\./g, '');
                this.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                let nominal = parseFloat(val) || 0;
                let sisa = parseFloat(pilihKategori.options[pilihKategori.selectedIndex].getAttribute('data-sisa')) || 0;
                
                if(nominal > sisa) {
                    this.classList.add('is-invalid'); btnSimpan.disabled = true;
                    pesanSisa.innerHTML = `<span class="text-danger fw-bold">Melebihi Anggaran! (Maks: ${new Intl.NumberFormat('id-ID').format(sisa)})</span>`;
                } else {
                    this.classList.remove('is-invalid'); btnSimpan.disabled = false;
                    pesanSisa.innerHTML = `Sisa Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
                }
            });
        }
        
        document.addEventListener('keyup', function(e) {
            if(e.target.classList.contains('input-rupiah')) {
                let val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
            }
        });
    </script>
</body>
</html>