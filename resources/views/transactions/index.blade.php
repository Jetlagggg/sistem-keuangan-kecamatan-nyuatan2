@extends('layouts.app')

@section('title', 'Transaksi Keuangan')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Transaksi Keuangan</h1>
            <p class="page-sub">Catat pengeluaran harian, telusuri riwayatnya, dan cetak laporan tanpa meninggalkan buku kas.</p>
        </div>
        <div class="page-actions">
            <span class="meta-line">{{ $transactions->total() }} transaksi tercatat</span>
        </div>
    </div>

    <div class="row g-4">
        @can('create', App\Models\Transaction::class)
        <div class="col-lg-4">
            <section class="sheet" aria-labelledby="input-title">
                <div class="sheet-head">
                    <h2 class="sheet-title" id="input-title">Input Transaksi Baru</h2>
                    <span class="sheet-meta">Pengeluaran</span>
                </div>
                <div class="sheet-body">
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="budget_stage_id">Sumber Dana (GU)</label>
                            <select name="budget_stage_id" id="budget_stage_id" class="form-select" required>
                                <option value="">— Pilih Sumber Dana —</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->nama_tahap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pilihKategori">Pos Anggaran</label>
                            <select name="category_id" id="pilihKategori" class="form-select" required>
                                <option value="" data-sisa="0">— Pilih Pos —</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" data-sisa="{{ $c->sisa_saat_ini }}">{{ $c->nama }} (Sisa: {{ number_format($c->sisa_saat_ini, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="inputNominal">Nominal (Rp)</label>
                            <input type="text" name="nominal" id="inputNominal" class="form-control input-rupiah" placeholder="0" required disabled>
                            <small id="pesanSisa" class="form-hint" aria-live="polite"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="penerima">Penerima</label>
                            <input type="text" id="penerima" name="penerima" class="form-control" placeholder="Nama penerima">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="keterangan">Uraian</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Keperluan pengeluaran"></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" id="btnSimpan" class="btn btn-primary" disabled>Simpan Transaksi</button>
                        </div>
                        <small class="form-hint" style="margin-top: 12px;">Nominal ditahan terhadap sisa pagu pos dan sisa dana tahap di server.</small>
                    </form>
                </div>
            </section>
        </div>
        @endcan

        <div class="{{ auth()->user()->can('create', App\Models\Transaction::class) ? 'col-lg-8' : 'col-12' }}">
            <section class="ledger-summary compact" aria-label="Ringkasan kas">
                <div class="summary-cell">
                    <div class="summary-label">Total Pagu</div>
                    <div class="summary-value">Rp {{ number_format($paguGlobal, 0, ',', '.') }}</div>
                    <div class="summary-note">Dana masuk seluruh tahap</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Realisasi</div>
                    <div class="summary-value">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                    <div class="summary-note">Pengeluaran tercatat</div>
                </div>
                <div class="summary-cell {{ $saldoAkhir < 0 ? 'is-negative' : '' }}">
                    <div class="summary-label">Sisa Kas</div>
                    <div class="summary-value">{{ $saldoAkhir < 0 ? '−' : '' }}Rp {{ number_format(abs($saldoAkhir), 0, ',', '.') }}</div>
                    <div class="summary-note">Pagu dikurangi realisasi</div>
                </div>
            </section>

            @if($jumlahPending > 0)
            <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="status">
                <span><i class="fa-solid fa-hourglass-half me-2" aria-hidden="true"></i>{{ $jumlahPending }} transaksi menunggu verifikasi (Rp {{ number_format($totalPending, 0, ',', '.') }}).</span>
                <a href="{{ route('transactions.index', ['status' => 'draft']) }}" class="btn btn-sm btn-primary">Lihat</a>
            </div>
            @endif

            <section class="sheet" aria-labelledby="filter-title">
                <div class="sheet-head">
                    <h2 class="sheet-title" id="filter-title">Filter &amp; Cetak</h2>
                    <span class="sheet-meta">Laporan mengikuti filter aktif</span>
                </div>
                <div class="sheet-body">
                    <form action="{{ route('transactions.index') }}" method="GET" id="searchForm">
                        <div class="filter-bar">
                            <div class="filter-field gu">
                                <label class="form-label" for="filter-gu">Filter GU</label>
                                <select name="budget_stage_id" id="filter-gu" class="form-select" onchange="this.form.submit()">
                                    <option value="">— Semua GU —</option>
                                    @foreach($stages as $stg)
                                        <option value="{{ $stg->id }}" {{ request('budget_stage_id') == $stg->id ? 'selected' : '' }}>
                                            {{ $stg->nama_tahap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-field keyword">
                                <label class="form-label" for="filter-keyword">Cari Uraian / Penerima</label>
                                <input type="text" name="keyword" id="filter-keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Kata kunci">
                            </div>
                            <div class="filter-field date">
                                <label class="form-label" for="filter-start">Dari Tanggal</label>
                                <input type="date" name="start_date" id="filter-start" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="filter-field date">
                                <label class="form-label" for="filter-end">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="filter-end" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="filter-field status">
                                <label class="form-label" for="filter-status">Status</label>
                                <select name="status" id="filter-status" class="form-select" onchange="this.form.submit()">
                                    <option value="">— Semua —</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="filter-field actions">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass me-1" aria-hidden="true"></i> Cari</button>
                                <a href="{{ route('transactions.cetak', request()->all()) }}" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print me-1" aria-hidden="true"></i> Cetak</a>
                                <a href="{{ route('transactions.export', request()->all()) }}" class="btn btn-secondary"><i class="fa-solid fa-file-excel me-1" aria-hidden="true"></i> CSV</a>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <section class="sheet" aria-labelledby="buku-title">
                <div class="sheet-head">
                    <h2 class="sheet-title" id="buku-title">Buku Transaksi</h2>
                    <span class="sheet-meta">{{ $transactions->count() }} baris di halaman ini</span>
                </div>
                <div class="table-responsive">
                    <table class="table-ledger">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Uraian</th>
                                <th scope="col" class="num">Nominal</th>
                                <th scope="col" class="center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                            @php
                                $statusMeta = match ($t->status) {
                                    \App\Models\Transaction::STATUS_DISETUJUI => ['Disetujui', 'tag-green'],
                                    \App\Models\Transaction::STATUS_DITOLAK => ['Ditolak', 'tag-red'],
                                    default => ['Menunggu', 'tag-muted'],
                                };
                            @endphp
                            <tr>
                                <td class="cell-date">
                                    {{ $t->tanggal->format('d/m/Y') }}
                                    <span class="cell-meta">{{ $t->budgetStage->nama_tahap ?? 'Non-GU' }}</span>
                                    <span class="tag {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                </td>
                                <td>
                                    <span class="cell-strong">{{ $t->category->nama ?? 'Umum' }}</span>
                                    @if($t->keterangan)
                                    <span class="cell-sub">{{ $t->keterangan }}</span>
                                    @endif
                                    @if($t->penerima)
                                    <span class="cell-meta"><i class="fa-regular fa-user me-1" aria-hidden="true"></i>{{ $t->penerima }}</span>
                                    @endif
                                </td>
                                <td class="num" style="color: var(--red);">− Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="center">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        @can('verify', $t)
                                        @if($t->isDraft())
                                        <form action="{{ route('transactions.verify', $t->id) }}" method="POST" onsubmit="return confirm('Setujui transaksi ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">Setujui</button>
                                        </form>
                                        <form action="{{ route('transactions.reject', $t->id) }}" method="POST" onsubmit="return confirm('Tolak transaksi ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">Tolak</button>
                                        </form>
                                        @endif
                                        @endcan
                                        @can('delete', $t)
                                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Data dipindahkan ke arsip dan dapat dipulihkan admin.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="empty-row">
                                <td colspan="4">Belum ada transaksi pada filter ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div>
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
                    pesanSisa.innerHTML = `<span class="text-danger fw-bold">Melebihi anggaran. Maksimal Rp ${new Intl.NumberFormat('id-ID').format(sisa)}</span>`;
                } else {
                    this.classList.remove('is-invalid'); btnSimpan.disabled = false;
                    pesanSisa.innerHTML = `Sisa Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
                }
            });
        }
    </script>
@endpush
