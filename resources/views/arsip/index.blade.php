@extends('layouts.app')

@section('title', 'Arsip Data')

@section('content')
    <div class="mb-3">
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-box-archive me-2 text-primary"></i>Arsip Data</h5>
        <p class="text-muted small mb-0">
            <i class="fa-solid fa-circle-info me-1"></i>
            Data yang dihapus tidak langsung hilang. Pulihkan untuk mengembalikannya, atau hapus permanen bila sudah tidak diperlukan.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaksi Terarsip</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small"><tr><th class="ps-4">Tanggal</th><th>Uraian</th><th class="text-end">Nominal</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $t->tanggal?->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-bold">{{ $t->category->nama ?? 'Umum' }}</div>
                                <div class="small text-muted">{{ $t->keterangan }}</div>
                            </td>
                            <td class="text-end fw-bold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $t->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-center text-nowrap">
                                <form action="{{ route('arsip.transactions.restore', $t->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                </form>
                                <form action="{{ route('arsip.transactions.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada transaksi terarsip.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-folder-open me-2 text-primary"></i>Pos Kegiatan Terarsip</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small"><tr><th class="ps-4">Nama Pos</th><th class="text-end">Pagu</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($categories as $c)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $c->nama }}</td>
                            <td class="text-end">Rp {{ number_format($c->budget, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $c->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-center text-nowrap">
                                <form action="{{ route('arsip.categories.restore', $c->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                </form>
                                <form action="{{ route('arsip.categories.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada pos terarsip.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i>Dana (UP/GU) Terarsip</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small"><tr><th class="ps-4">Tahapan</th><th class="text-end">Nominal</th><th>Dihapus</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($stages as $s)
                        <tr>
                            <td class="ps-4"><span class="badge bg-primary">{{ $s->nama_tahap }}</span></td>
                            <td class="text-end fw-bold">Rp {{ number_format($s->nominal, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $s->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-center text-nowrap">
                                <form action="{{ route('arsip.budget-stages.restore', $s->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-rotate-left"></i> Pulihkan</button>
                                </form>
                                <form action="{{ route('arsip.budget-stages.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada dana terarsip.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
