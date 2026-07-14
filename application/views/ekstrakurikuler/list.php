<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Manajemen Ekstrakurikuler</h6>
            <?php if ($is_admin): ?>
                <a href="<?php echo url('ekstrakurikuler/tambah') ?>" class="btn btn-sm btn-primary text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon> Tambah Ekskul Baru
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Tahun Ajaran</th>
                            <th>Nama Ekskul</th>
                            <th>Pembina (PTK)</th>
                            <th>Deskripsi / Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ekskul as $row): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($row->logo) && file_exists('./uploads/ekskul/'.$row->logo)): ?>
                                        <img src="<?php echo url('uploads/ekskul/'.$row->logo) ?>" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-neutral-200 d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 45px; height: 45px;">
                                            <?php echo strtoupper(substr($row->nama_ekskul, 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                <td class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_ekskul) ?></td>
                                 <td>
                                     <span class="text-secondary-light fw-medium">
                                         <?php 
                                         $pembina_names = [];
                                         if (!empty($row->id_ptk_pembina)) {
                                             $decoded = json_decode($row->id_ptk_pembina, true);
                                             if (is_array($decoded) && !empty($decoded)) {
                                                 $this->db->where_in('id_ptk', array_map('intval', $decoded));
                                                 $res = $this->db->get('ptk')->result();
                                                 foreach ($res as $ptk) {
                                                     $pembina_names[] = $ptk->nama_ptk;
                                                 }
                                             } else {
                                                 $ptk = $this->db->get_where('ptk', ['id_ptk' => (int) $row->id_ptk_pembina])->row();
                                                 if ($ptk) {
                                                     $pembina_names[] = $ptk->nama_ptk;
                                                 }
                                             }
                                         }
                                         echo !empty($pembina_names) ? html_escape(implode(', ', $pembina_names)) : 'Belum ditentukan';
                                         ?>
                                     </span>
                                 </td>
                                <td><?php echo html_escape($row->keterangan ?: '-') ?></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <a href="<?php echo url('ekstrakurikuler/daftar_siswa/' . $row->id_ekskul) ?>" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" title="Atur Anggota">
                                            <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Anggota
                                        </a>
                                        <a href="<?php echo url('ekstrakurikuler/detail/' . $row->id_ekskul) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" title="Input Nilai">
                                            <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Input Nilai
                                        </a>
                                        <?php if ($is_admin): ?>
                                            <a href="<?php echo url('ekstrakurikuler/edit/' . $row->id_ekskul) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Edit">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </a>
                                            <a href="<?php echo url('ekstrakurikuler/hapus/' . $row->id_ekskul) ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="return confirm('Apakah Anda yakin ingin menghapus data ekstrakurikuler ini?')" title="Hapus">
                                                <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
