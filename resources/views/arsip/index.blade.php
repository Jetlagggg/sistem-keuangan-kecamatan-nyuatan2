@extends('layouts.app')

@section('title', 'Arsip Data')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Arsip Data</h1>
            <p class="page-sub">Data yang dihapus tidak langsung hilang. Pulihkan untuk mengembalikannya, atau hapus permanen bila sudah tidak diperlukan.</p>
        </div>
        <div class="page-actions">
            <span class="meta-line">{{ $transactions->count() + $categories->count() + $stages->count() }} arsip</span>
        </div>
    </div>

    <section class="sheet" aria-labelledby="arsip-transaksi">
        <div class="sheet-head">
            <h2 class="sheet-title" id="arsip-transaksi">Transaksi Terarsip</h2>
            <span class="sheet-meta">{{ $transactions->count() }} baris</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Uraian</th>
                        <th scope="col" class="num">Nominal</th>
                        <th scope="col">Dihapus</th>
                        <th scope="col" class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td class="cell-date">{{ $t->tanggal?->format('d/m/Y') }}</td>
                        <td>
                            <span class="cell-strong">{{ $t->category->nama ?? 'Umum' }}</span>
                            @if($t->keterangan)
                            <span class="cell-sub">{{ $t->keterangan }}</span>
                            @endif
                        </td>
                        <td class="num">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                        <td class="cell-date">{{ $t->deleted_at?->format('d/m/Y H:i') }}</td>
                        <td class="center" style="white-space: nowrap;">
                            <form action="{{ route('arsip.transactions.restore', $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Pulihkan</button>
                            </form>
                            <form action="{{ route('arsip.transactions.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus permanen</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="5">Tidak ada transaksi terarsip.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="sheet" aria-labelledby="arsip-pos">
        <div class="sheet-head">
            <h2 class="sheet-title" id="arsip-pos">Pos Kegiatan Terarsip</h2>
            <span class="sheet-meta">{{ $categories->count() }} baris</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Nama Pos</th>
                        <th scope="col" class="num">Pagu</th>
                        <th scope="col">Dihapus</th>
                        <th scope="col" class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $c)
                    <tr>
                        <td class="cell-strong">{{ $c->nama }}</td>
                        <td class="num">Rp {{ number_format($c->budget, 0, ',', '.') }}</td>
                        <td class="cell-date">{{ $c->deleted_at?->format('d/m/Y H:i') }}</td>
                        <td class="center" style="white-space: nowrap;">
                            <form action="{{ route('arsip.categories.restore', $c->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Pulihkan</button>
                            </form>
                            <form action="{{ route('arsip.categories.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus permanen</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="4">Tidak ada pos terarsip.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="sheet" aria-labelledby="arsip-dana">
        <div class="sheet-head">
            <h2 class="sheet-title" id="arsip-dana">Dana (UP/GU) Terarsip</h2>
            <span class="sheet-meta">{{ $stages->count() }} baris</span>
        </div>
        <div class="table-responsive">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th scope="col">Tahapan</th>
                        <th scope="col" class="num">Nominal</th>
                        <th scope="col">Dihapus</th>
                        <th scope="col" class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stages as $s)
                    <tr>
                        <td><span class="tag tag-muted">{{ $s->nama_tahap }}</span></td>
                        <td class="num">Rp {{ number_format($s->nominal, 0, ',', '.') }}</td>
                        <td class="cell-date">{{ $s->deleted_at?->format('d/m/Y H:i') }}</td>
                        <td class="center" style="white-space: nowrap;">
                            <form action="{{ route('arsip.budget-stages.restore', $s->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Pulihkan</button>
                            </form>
                            <form action="{{ route('arsip.budget-stages.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus permanen</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="4">Tidak ada dana terarsip.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
