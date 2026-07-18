<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light text-md mb-0">Cetak Absensi Rombel Pembelajaran</h6>
                </div>
                <div class="card-body p-24">
                    <form id="printForm" action="<?php echo url('pencetakan/absensi') ?>" method="get">
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Rombel / Pembelajaran Aktif</label>
                                <select class="form-control radius-8 form-select" name="id_pembelajaran" required>
                                    <option value="">Pilih Kelas / Rombel</option>
                                    <?php foreach ($pembelajaran_list as $p): ?>
                                        <option value="<?php echo $p->id_pembelajaran ?>">
                                            <?php echo htmlspecialchars($p->nama_lembaga_singkat . ' - ' . $p->nama_tingkat . ' (' . $p->nama_rombel . ') - TP. ' . $p->tahun_pelajaran . ' ' . $p->semester) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-20">
                                <div class="d-flex flex-wrap gap-24">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pakai_kop" value="1" id="checkKop">
                                        <label class="form-check-label fw-semibold text-sm" for="checkKop">
                                            Gunakan Kop Surat
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pakai_ttd" value="1" id="checkTtd">
                                        <label class="form-check-label fw-semibold text-sm" for="checkTtd">
                                            Tampilkan Tanda Tangan (Wali Kelas)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 mb-20 d-flex gap-12">
                                <button type="submit" onclick="submitType = 'print';" class="btn btn-primary w-100 text-md py-12 radius-8 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:printer-linear" class="text-xl"></iconify-icon>
                                    Cetak Absensi
                                </button>
                                <button type="submit" onclick="submitType = 'pdf';" class="btn btn-success w-100 text-md py-12 radius-8 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:file-text-linear" class="text-xl"></iconify-icon>
                                    Unduh PDF (A4)
                                </button>
                                <button type="submit" onclick="submitType = 'excel';" class="btn btn-warning w-100 text-md py-12 radius-8 d-flex align-items-center justify-content-center gap-2 text-white">
                                    <iconify-icon icon="solar:file-spreadsheet-linear" class="text-xl"></iconify-icon>
                                    Ekspor Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var submitType = 'print';
    document.getElementById('printForm').onsubmit = function(e) {
        e.preventDefault();
        
        var id_pembelajaran = this.querySelector('[name="id_pembelajaran"]').value;
        var pakai_kop = this.querySelector('[name="pakai_kop"]').checked ? '1' : '0';
        var pakai_ttd = this.querySelector('[name="pakai_ttd"]').checked ? '1' : '0';
        
        var format = (submitType === 'print') ? '' : submitType;
        var url = this.action + '?id_pembelajaran=' + id_pembelajaran + '&pakai_kop=' + pakai_kop + '&pakai_ttd=' + pakai_ttd + '&format=' + format;
        
        window.open(url, '_blank');
    };
</script>

<?php include viewPath('includes/footer'); ?>
