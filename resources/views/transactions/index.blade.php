@extends('layouts.app')

@section('title', 'Transaksi Keuangan')

@section('content')
    <div class="row">
        @can('create', App\Models\Transaction::class)
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
        @endcan

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
                            <div class="col-md-2">
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
                                <label class="small text-muted fw-bold">Cari Uraian / Penerima</label>
                                <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}" placeholder="Kata kunci...">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted fw-bold">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted fw-bold">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold"><i class="fa-solid fa-magnifying-glass me-1"></i> Cari</button>
                                <a href="{{ route('transactions.cetak', request()->all()) }}" target="_blank" class="btn btn-secondary btn-sm w-100 fw-bold">
                                    <i class="fa-solid fa-print me-1"></i> CETAK
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
                                        {{ $t->tanggal->format('d/m/Y') }}
                                        <div class="mt-1"><span class="badge bg-light text-dark border">{{ $t->budgetStage->nama_tahap ?? 'Non-GU' }}</span></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $t->category->nama ?? 'Umum' }}</div>
                                        <div class="small text-muted">{{ $t->keterangan }}</div>
                                        @if($t->penerima) <div class="small text-primary fst-italic"><i class="fa-solid fa-user me-1"></i> {{ $t->penerima }}</div> @endif
                                    </td>
                                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @can('delete', $t)
                                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                        @endcan
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

            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const pilihKategori = document.getElementById('pilihKategori');
        const inputNominal = document.getElementById('inputNominal');
        const btnSimpan = document.getElementById('btnSimpan');
        const pesanSisa = document.getElementById('pesanSisa');

        if (pilihKategori && inputNominal) {
            pilihKategori.addEventListener('change', function () {
                const sisa = parseFloat(this.options[this.selectedIndex].getAttribute('data-sisa')) || 0;
                inputNominal.disabled = false; inputNominal.value = '';
                pesanSisa.innerHTML = `Sisa Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
            });
            inputNominal.addEventListener('keyup', function () {
                let val = this.value.replace(/\./g, '');
                this.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                let nominal = parseFloat(val) || 0;
                let sisa = parseFloat(pilihKategori.options[pilihKategori.selectedIndex].getAttribute('data-sisa')) || 0;

                if (nominal > sisa) {
                    this.classList.add('is-invalid'); btnSimpan.disabled = true;
                    pesanSisa.innerHTML = `<span class="text-danger fw-bold">Melebihi Anggaran! (Maks: ${new Intl.NumberFormat('id-ID').format(sisa)})</span>`;
                } else {
                    this.classList.remove('is-invalid'); btnSimpan.disabled = false;
                    pesanSisa.innerHTML = `Sisa Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
                }
            });
        }
    </script>
@endpush
