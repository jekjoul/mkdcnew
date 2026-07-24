<?php $this->load->view('includes/header'); ?>

<div class="row gy-4 mb-24">
    <!-- Card 1: Form Pengaturan Endpoint API & Environment -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm radius-12 h-100">
            <div class="card-header bg-transparent border-bottom p-24">
                <h6 class="mb-0 text-primary-light">Pengaturan Target Server API</h6>
                <span class="text-xs text-muted">Pilih mode environment server dan kunci otentikasi token API</span>
            </div>
            <div class="card-body p-24">
                <?php echo form_open('fingerprint_bridge/simpan_setting'); ?>
                
                <!-- Environment Mode Picker -->
                <div class="mb-20">
                    <label class="form-label text-sm fw-semibold text-primary-light mb-8">Pilih Environment Mode <span class="text-danger-600">*</span></label>
                    <div class="d-flex align-items-center gap-16">
                        <div class="form-check checked-primary d-flex align-items-center gap-2">
                            <input class="form-check-input" type="radio" name="env_mode" id="env_dev" value="development" <?php echo $settings->env_mode === 'development' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm cursor-pointer" for="env_dev">
                                <span class="badge bg-info-focus text-info-main me-1">DEV</span> Development Mode
                            </label>
                        </div>
                        <div class="form-check checked-success d-flex align-items-center gap-2">
                            <input class="form-check-input" type="radio" name="env_mode" id="env_prod" value="production" <?php echo $settings->env_mode === 'production' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm cursor-pointer" for="env_prod">
                                <span class="badge bg-success-focus text-success-main me-1">PROD</span> Production Mode
                            </label>
                        </div>
                    </div>
                </div>

                <!-- URL Development -->
                <div class="mb-16">
                    <label class="form-label text-sm fw-semibold text-secondary-light">Development Endpoint API URL</label>
                    <input type="url" class="form-control radius-8" name="dev_endpoint_url" value="<?php echo html_escape($settings->dev_endpoint_url) ?>" required placeholder="http://localhost/mkdcnew/api/presensi/sync">
                    <span class="text-xs text-muted">URL server lokal saat dalam tahap pengembangan / uji coba LAN.</span>
                </div>

                <!-- URL Production -->
                <div class="mb-16">
                    <label class="form-label text-sm fw-semibold text-secondary-light">Production Endpoint API URL</label>
                    <input type="url" class="form-control radius-8" name="prod_endpoint_url" value="<?php echo html_escape($settings->prod_endpoint_url) ?>" required placeholder="https://domain-sekolah.sch.id/api/presensi/sync">
                    <span class="text-xs text-muted">URL server live online domain sekolah.</span>
                </div>

                <!-- API Secret Token -->
                <div class="mb-16">
                    <label class="form-label text-sm fw-semibold text-secondary-light">API Secret Token Key</label>
                    <div class="input-group">
                        <input type="text" class="form-control radius-8-start" name="api_token" id="api_token_input" value="<?php echo html_escape($settings->api_token) ?>" required>
                        <button type="button" class="btn btn-outline-secondary radius-8-end" onclick="generateToken()">Generate</button>
                    </div>
                    <span class="text-xs text-muted">Token harus sama dengan <code>Presensi::API_TOKEN</code> di server API.</span>
                </div>

                <!-- Auto Sync Interval -->
                <div class="row gy-3 mb-20">
                    <div class="col-6">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Interval Auto-Sync (Detik)</label>
                        <input type="number" class="form-control radius-8" name="auto_sync_interval" value="<?php echo (int)$settings->auto_sync_interval ?>" min="3" max="3600" required>
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-8">
                            <input class="form-check-input" type="checkbox" name="auto_sync_active" id="auto_sync_active" value="1" <?php echo $settings->auto_sync_active ? 'checked' : '' ?>>
                            <label class="form-check-label text-sm fw-semibold text-primary-light" for="auto_sync_active">Aktifkan Auto-Sync</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-12 pt-16 border-top">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">Simpan Pengaturan API</button>
                    <button type="button" id="btn-test-api" class="btn btn-outline-info radius-8 px-20 py-10">
                        <iconify-icon icon="solar:wifi-bold" class="align-middle me-1"></iconify-icon> Tes Koneksi API
                    </button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <!-- Card 2: Pengaturan Multi-Mesin Fingerprint -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm radius-12 h-100">
            <div class="card-header bg-transparent border-bottom p-24 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 text-primary-light">Pengaturan Multi-Mesin</h6>
                    <span class="text-xs text-muted">Daftar mesin sidik jari (bisa > 1 mesin)</span>
                </div>
                <button type="button" class="btn btn-sm btn-success-600 radius-8 px-16 py-8 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambahMesin">
                    <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah Mesin
                </button>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table align-middle">
                        <thead>
                            <tr>
                                <th width="30">No</th>
                                <th>Nama Mesin</th>
                                <th>IP Address & Port</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($machines as $m): 
                            ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?php echo $no++ ?></td>
                                    <td>
                                        <span class="fw-semibold text-primary-light d-block"><?php echo html_escape($m->nama_mesin) ?></span>
                                        <span class="text-xs text-muted"><?php echo html_escape($m->lokasi ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <code class="text-primary-600"><?php echo html_escape($m->ip_address) ?>:<?php echo $m->port ?></code>
                                        <?php if (!empty($m->serial_number)): ?>
                                            <span class="badge bg-neutral-100 text-neutral-600 border radius-4 font-monospace text-xs ms-1"><?php echo html_escape($m->serial_number) ?></span>
                                        <?php endif; ?>
                                        <span class="text-xs text-muted d-block">Key: <?php echo html_escape($m->comm_key) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-4">
                                            <button type="button" class="btn btn-xs btn-outline-warning radius-8 btn-edit-mesin"
                                                data-id="<?php echo $m->id_machine ?>"
                                                data-nama="<?php echo html_escape($m->nama_mesin) ?>"
                                                data-sn="<?php echo html_escape(isset($m->serial_number) ? $m->serial_number : '') ?>"
                                                data-ip="<?php echo html_escape($m->ip_address) ?>"
                                                data-port="<?php echo $m->port ?>"
                                                data-key="<?php echo html_escape($m->comm_key) ?>"
                                                data-tipe="<?php echo html_escape($m->tipe_mesin) ?>"
                                                data-lokasi="<?php echo html_escape($m->lokasi) ?>">
                                                <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                            </button>

                                            <a href="<?php echo url('fingerprint_bridge/hapus_mesin/' . $m->id_machine) ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus mesin ini?')"
                                                class="btn btn-xs btn-outline-danger radius-8">
                                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($machines)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-20 text-muted">Belum ada mesin terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Mesin -->
<div class="modal fade" id="modalTambahMesin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light px-24 py-16">
                <h6 class="modal-title fw-semibold text-primary-light" id="modalMesinTitle">Tambah Mesin Sidik Jari</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('fingerprint_bridge/simpan_mesin'); ?>
            <input type="hidden" name="id_machine" id="mesin_id" value="">
            <div class="modal-body p-24">
                
                <div class="row gy-3 mb-16">
                    <div class="col-6">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Nama Mesin <span class="text-danger-600">*</span></label>
                        <input type="text" class="form-control radius-8" name="nama_mesin" id="mesin_nama" placeholder="Contoh: Mesin Gerbang Utama" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Serial Number (SN) Mesin</label>
                        <input type="text" class="form-control radius-8" name="serial_number" id="mesin_sn" placeholder="Contoh: FS-101010101">
                    </div>
                </div>

                <div class="row gy-3 mb-16">
                    <div class="col-8">
                        <label class="form-label text-sm fw-semibold text-secondary-light">IP Address LAN <span class="text-danger-600">*</span></label>
                        <input type="text" class="form-control radius-8" name="ip_address" id="mesin_ip" placeholder="192.168.1.201" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Port LAN</label>
                        <input type="number" class="form-control radius-8" name="port" id="mesin_port" value="4370" required>
                    </div>
                </div>

                <div class="row gy-3 mb-16">
                    <div class="col-6">
                        <label class="form-label text-sm fw-semibold text-secondary-light">COMM Key / Password</label>
                        <input type="text" class="form-control radius-8" name="comm_key" id="mesin_key" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Tipe Driver Mesin</label>
                        <select name="tipe_mesin" id="mesin_tipe" class="form-select radius-8">
                            <option value="ZK_TCP">Fingerspot ZK Series (Port 4370)</option>
                            <option value="REVO_TCP">Fingerspot Revo/Neo (Port 5005)</option>
                            <option value="ADMS_PUSH">ADMS HTTP Push Server</option>
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="form-label text-sm fw-semibold text-secondary-light">Lokasi Mesin</label>
                    <input type="text" class="form-control radius-8" name="lokasi" id="mesin_lokasi" placeholder="Contoh: Ruang Lobby Depan">
                </div>

            </div>
            <div class="modal-footer border-top bg-light px-24 py-16">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">Simpan Data Mesin</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
function generateToken() {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var token = 'MKDC_FINGERPRINT_SECRET_KEY_';
    for (var i = 0; i < 8; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#api_token_input').val(token);
}

$(document).ready(function() {
    // Edit Mesin Modal Handler
    $('.btn-edit-mesin').on('click', function() {
        var id     = $(this).data('id');
        var nama   = $(this).data('nama');
        var sn     = $(this).data('sn');
        var ip     = $(this).data('ip');
        var port   = $(this).data('port');
        var key    = $(this).data('key');
        var tipe   = $(this).data('tipe');
        var lokasi = $(this).data('lokasi');

        $('#modalMesinTitle').text('Edit Mesin Sidik Jari');
        $('#mesin_id').val(id);
        $('#mesin_nama').val(nama);
        $('#mesin_sn').val(sn);
        $('#mesin_ip').val(ip);
        $('#mesin_port').val(port);
        $('#mesin_key').val(key);
        $('#mesin_tipe').val(tipe);
        $('#mesin_lokasi').val(lokasi);

        var modal = new bootstrap.Modal(document.getElementById('modalTambahMesin'));
        modal.show();
    });

    // Reset modal on close
    $('#modalTambahMesin').on('hidden.bs.modal', function () {
        $('#modalMesinTitle').text('Tambah Mesin Sidik Jari');
        $('#mesin_id').val('');
        $('#mesin_nama').val('');
        $('#mesin_sn').val('');
        $('#mesin_ip').val('');
        $('#mesin_port').val(4370);
        $('#mesin_key').val('0');
        $('#mesin_tipe').val('ZK_TCP');
        $('#mesin_lokasi').val('');
    });

    // Test API Connection Button Handler
    $('#btn-test-api').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true);
        btn.html('<iconify-icon icon="line-md:loading-twotone-loop" class="align-middle me-1"></iconify-icon> Testing API...');

        $.ajax({
            url: "<?php echo url('fingerprint_bridge/tes_koneksi_api') ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false);
                btn.html('<iconify-icon icon="solar:wifi-bold" class="align-middle me-1"></iconify-icon> Tes Koneksi API');

                if (res.status) {
                    Swal.fire({
                        title: 'Koneksi API Berhasil! [' + res.env_mode + ']',
                        text: 'Server API merespons dengan baik di URL: ' + res.target_url,
                        icon: 'success',
                        confirmButtonColor: '#22c55e'
                    });
                } else {
                    Swal.fire({
                        title: 'Koneksi API Gagal!',
                        text: res.message + ' (Target: ' + res.target_url + ')',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function(xhr, status, err) {
                btn.prop('disabled', false);
                btn.html('<iconify-icon icon="solar:wifi-bold" class="align-middle me-1"></iconify-icon> Tes Koneksi API');
                Swal.fire({
                    title: 'Network Error',
                    text: 'Gagal menghubungi controller local bridge: ' + err,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });
});
</script>
