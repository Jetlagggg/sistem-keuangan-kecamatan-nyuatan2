@can('create', App\Models\BudgetStage::class)
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
@endcan
