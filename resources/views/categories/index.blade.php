@extends('layouts.app')

@section('title', 'Monev Anggaran')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Monev Anggaran</h1>
            <p class="page-sub">Monitoring &amp; evaluasi keuangan Kecamatan Nyuatan. Pagu, terserap, dan sisa alokasi dibaca sebagai satu lembar kas.</p>
        </div>
        <div class="page-actions">
            @if($sisaAlokasi < 0)
                <span class="stamp">Alokasi Melebihi Pagu</span>
            @endif
            <a href="{{ route('categories.laporan') }}" target="_blank" class="btn btn-secondary">
                <i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i> Laporan Pos
            </a>
            <a href="{{ route('categories.laporan.csv') }}" class="btn btn-secondary">
                <i class="fa-solid fa-file-excel me-1" aria-hidden="true"></i> Excel
            </a>
            @can('create', App\Models\Category::class)
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Buat Pos Baru
            </button>
            @endcan
        </div>
    </div>

    <section class="ledger-summary" aria-label="Ringkasan anggaran">
        <div class="summary-cell">
            <div class="summary-label">Total Pagu Kantor (UP + GU)</div>
            <div class="summary-value">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
            <div class="summary-note">Akumulasi dana masuk dari {{ $stages->count() }} tahap</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Total Terserap</div>
            <div class="summary-value">Rp {{ number_format($totalTerserap, 0, ',', '.') }}</div>
            <div class="summary-note">{{ number_format($persen, 1, ',', '.') }}% dari pagu kantor terealisasi</div>
        </div>
        <div class="summary-cell {{ $sisaAlokasi < 0 ? 'is-negative' : '' }}">
            <div class="summary-label">Sisa Alokasi Anggaran</div>
            <div class="summary-value">{{ $sisaAlokasi < 0 ? '−' : '' }}Rp {{ number_format(abs($sisaAlokasi), 0, ',', '.') }}</div>
            <div class="summary-note">
                @if($sisaAlokasi < 0)
                    Alokasi pos melebihi pagu kantor
                @else
                    Dana yang belum dibagi ke pos kegiatan
                @endif
            </div>
        </div>
    </section>

    @if($jumlahPending > 0)
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="status">
        <span><i class="fa-solid fa-hourglass-half me-2" aria-hidden="true"></i>{{ $jumlahPending }} transaksi menunggu verifikasi (Rp {{ number_format($totalPending, 0, ',', '.') }}) belum dihitung sebagai realisasi.</span>
        <a href="{{ route('transactions.index', ['status' => 'draft']) }}" class="btn btn-sm btn-primary">Verifikasi</a>
    </div>
    @endif

    <section class="sheet" aria-labelledby="tren-title">
        <div class="sheet-head">
            <h2 class="sheet-title" id="tren-title">Tren Realisasi Bulanan</h2>
            <span class="sheet-meta">Tahun {{ $trenTahun }}</span>
        </div>
        <div class="sheet-body">
            <canvas id="grafikTren" height="80" role="img" aria-label="Grafik realisasi pengeluaran per bulan tahun {{ $trenTahun }}"></canvas>
        </div>
    </section>

    <section class="sheet" aria-labelledby="riwayat-title">
        <div class="sheet-head">
            <h2 class="sheet-title" id="riwayat-title">Riwayat Pencairan Dana (UP / GU)</h2>
            <span class="sheet-meta">{{ $stages->count() }} tahap · total Rp {{ number_format($totalPagu, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Tahapan</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="num">Nominal</th>
                        <th scope="col" class="balance">Saldo Kas</th>
                        <th scope="col" class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saldoKas = 0; @endphp
                    @forelse($stages as $stg)
                    @php $saldoKas += $stg->nominal; @endphp
                    <tr>
                        <td class="cell-date">{{ $stg->tanggal_cair->format('d/m/Y') }}</td>
                        <td><span class="tag tag-muted">{{ $stg->nama_tahap }}</span></td>
                        <td>{{ $stg->keterangan ?? '—' }}</td>
                        <td class="num">Rp {{ number_format($stg->nominal, 0, ',', '.') }}</td>
                        <td class="balance">Rp {{ number_format($saldoKas, 0, ',', '.') }}</td>
                        <td class="center">
                            @can('delete', $stg)
                            <form action="{{ route('budget.destroy', $stg) }}" method="POST" onsubmit="return confirm('Hapus dana ini? Pagu kantor akan berkurang dan data dipindahkan ke arsip.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="6">Belum ada dana UP/GU. Gunakan tombol Input Dana (GU) di atas.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($stages->isNotEmpty())
                <tfoot>
                    <tr>
                        <td colspan="3">Jumlah Dana Masuk</td>
                        <td class="num">Rp {{ number_format($totalPagu, 0, ',', '.') }}</td>
                        <td class="balance">Rp {{ number_format($totalPagu, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </section>

    <div class="section-head">
        <div>
            <h2 class="section-title">Daftar Pos Kegiatan</h2>
            <p class="section-note">Pagu, realisasi, dan sisa dana tiap pos. Baris penuh ditandai merah.</p>
        </div>
        <span class="meta-line">{{ $categories->count() }} pos · pagu Rp {{ number_format($categories->sum('budget'), 0, ',', '.') }}</span>
    </div>

    <div class="pos-grid">
        @forelse($categories as $category)
        @php
            $penuh = $category->sisa_saat_ini <= 0 || $category->persen_realisasi >= 100;
            $lebar = max(0, min(100, $category->persen_realisasi));
        @endphp
        <article class="pos-sheet {{ $penuh ? 'pos-full' : '' }}">
            <div class="pos-head">
                <h3 class="pos-name">{{ $category->nama }}</h3>
                <span class="pos-index">
                    @if($penuh)
                        <span class="tag tag-red">Pagu Habis</span>
                    @else
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    @endif
                </span>
            </div>
            <div class="pos-figures">
                <div class="pos-figure">
                    <span class="k">Pagu</span>
                    <div class="v">Rp {{ number_format($category->budget, 0, ',', '.') }}</div>
                </div>
                <div class="pos-figure">
                    <span class="k">Terserap</span>
                    <div class="v">Rp {{ number_format($category->terpakai_saat_ini, 0, ',', '.') }}</div>
                </div>
                <div class="pos-figure">
                    <span class="k">Sisa</span>
                    <div class="v {{ $category->sisa_saat_ini < 0 ? 'negative' : '' }}">Rp {{ number_format($category->sisa_saat_ini, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="pos-body">
                <div class="pos-realisation">
                    <div class="realisasi-track" role="img" aria-label="Realisasi {{ number_format($category->persen_realisasi, 1, ',', '.') }} persen">
                        <div class="realisasi-fill" style="width: {{ $lebar }}%"></div>
                    </div>
                    <span class="pos-percent">{{ number_format($category->persen_realisasi, 1, ',', '.') }}%</span>
                </div>
                <div class="pos-note {{ $penuh ? 'warning' : '' }}">
                    @if($penuh)
                        Tidak ada sisa pagu untuk pos ini.
                    @else
                        Sisa Rp {{ number_format($category->sisa_saat_ini, 0, ',', '.') }} dari pagu pos.
                    @endif
                </div>
                <div class="pos-actions">
                    @can('update', $category)
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                        Edit
                    </button>
                    @endcan
                    @can('delete', $category)
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus pos ini? Pos dipindahkan ke arsip dan dapat dipulihkan admin.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                    @endcan
                </div>
            </div>
        </article>

        <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('categories.update', $category->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="editModalLabel{{ $category->id }}">Edit Pos Anggaran</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="nama{{ $category->id }}">Nama Pos Kegiatan</label>
                                <input type="text" id="nama{{ $category->id }}" name="nama" class="form-control" value="{{ $category->nama }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="budget{{ $category->id }}">Pagu Anggaran (Rp)</label>
                                <input type="text" id="budget{{ $category->id }}" name="budget" class="form-control input-rupiah" value="{{ number_format($category->budget, 0, ',', '.') }}" required>
                                <small class="form-hint">Total dana maksimal untuk pos ini.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update Pos</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="ledger-note" style="grid-column: 1 / -1;">
            <h3>Belum ada pos kegiatan</h3>
            <p>Buat pos pertama untuk membagi pagu kantor ke kegiatan. Setiap transaksi akan menahan sisa pagu pos ini.</p>
        </div>
        @endforelse
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="createModalLabel">Buat Pos Kegiatan Baru</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="namaPosBaru">Nama Pos Kegiatan</label>
                            <input type="text" id="namaPosBaru" name="nama" class="form-control" placeholder="Contoh: Belanja ATK" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="paguPosBaru">Alokasi Pagu (Rp)</label>
                            <input type="text" id="paguPosBaru" name="budget" class="form-control input-rupiah" placeholder="0" required>
                            <small class="form-hint">Total dana yang dialokasikan untuk pos ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pos</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-9nhczxUqK87bcKHh20fSQcTGD4qq5GhayNYSYWqwBkINBhOfQLg/P5HG5lF1urn4" crossorigin="anonymous"></script>
    <script>
        (function () {
            const canvas = document.getElementById('grafikTren');
            if (!canvas || !window.Chart) {
                return;
            }

            const root = document.documentElement;
            const rgb = (value) => value.replace(/\s+/g, '');

            function chartColors() {
                const styles = getComputedStyle(root);

                return {
                    red: rgb(styles.getPropertyValue('--red')),
                    muted: rgb(styles.getPropertyValue('--muted')),
                    rule: rgb(styles.getPropertyValue('--rule')),
                };
            }

            const colors = chartColors();
            const chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: @json($trenLabels),
                    datasets: [{
                        label: 'Realisasi (Rp)',
                        data: @json($trenData),
                        backgroundColor: colors.red,
                        borderColor: colors.red,
                        borderWidth: 1,
                        borderRadius: 0,
                        maxBarThickness: 28,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { color: colors.rule },
                            ticks: { color: colors.muted, font: { family: 'Roboto Mono', size: 10 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.rule },
                            border: { display: false },
                            ticks: {
                                color: colors.muted,
                                callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                            }
                        }
                    }
                }
            });

            document.addEventListener('themechange', function () {
                const next = chartColors();
                chart.data.datasets[0].backgroundColor = next.red;
                chart.data.datasets[0].borderColor = next.red;
                chart.options.scales.x.ticks.color = next.muted;
                chart.options.scales.x.border.color = next.rule;
                chart.options.scales.y.ticks.color = next.muted;
                chart.options.scales.y.grid.color = next.rule;
                chart.update('none');
            });
        })();
    </script>
@endpush
