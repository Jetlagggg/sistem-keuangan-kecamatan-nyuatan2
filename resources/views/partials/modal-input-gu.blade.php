@can('create', App\Models\BudgetStage::class)
<div class="modal fade" id="modalInputGU" tabindex="-1" aria-labelledby="modalInputGULabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('budget.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="modalInputGULabel">Input Pencairan Dana</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="nama_tahap">Jenis Tahapan</label>
                        <input class="form-control" list="listTahapan" id="nama_tahap" name="nama_tahap" placeholder="Contoh: GU 5" required autocomplete="off">
                        <datalist id="listTahapan">
                            <option value="UP">
                            <option value="GU 1">
                            <option value="GU 2">
                            <option value="GU 3">
                            <option value="GU 4">
                            <option value="LS">
                            <option value="TU">
                        </datalist>
                        <small class="form-hint">Pilih dari daftar atau ketik sendiri, misalnya GU 20.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nominal_dana">Nominal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="nominal" id="nominal_dana" class="form-control input-rupiah" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="tanggal_cair">Tanggal Cair</label>
                        <input type="date" name="tanggal_cair" id="tanggal_cair" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="keterangan_dana">Keterangan</label>
                        <textarea name="keterangan" id="keterangan_dana" class="form-control" rows="2" placeholder="Keperluan pencairan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Dana</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
