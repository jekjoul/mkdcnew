<?php
$this->load->helper('text');
defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Header Card -->
    <div class="card mb-24 border-0 shadow-sm radius-12 overflow-hidden">
        <div class="card-header bg-warning-900 p-24 text-light d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h6 class="text-light mb-1">Perangkat & Agenda Pembelajaran</h6>
                <p class="text-light text-sm mb-0">
                    <?php echo html_escape($item->nama_mapel . ' — ' . trim($item->nama_lembaga . ' ' . $item->nama_tingkat . ' ' . $item->nama_rombel)) ?>
                </p>
            </div>
            <a href="<?php echo $back_url ?>" class="btn btn-sm btn-outline-light radius-8 px-16 py-8">
                <iconify-icon icon="solar:arrow-left-linear" class="me-1"></iconify-icon> Kembali
            </a>
        </div>
        <div class="card-body p-24">
            <div class="row gy-3">
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Tahun Ajaran / Semester</span>
                    <strong class="text-primary-light"><?php echo html_escape($item->tahun_pelajaran . ' (' . $item->semester . ')') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Guru Pengampu</span>
                    <strong class="text-primary-light"><?php echo html_escape($item->nama_ptk ?: '-') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Jadwal Kelas</span>
                    <strong class="text-primary-light">
                        <?php
                        $schedules = $this->db->get_where('jadwal_pelajaran_item', [
                            'id_pembelajaran' => $item->id_pembelajaran,
                            'id_mapel' => $item->id_mapel
                        ])->result();
                        if (!empty($schedules)) {
                            $days = array_unique(array_column($schedules, 'hari'));
                            echo implode(', ', array_map('ucfirst', $days));
                        } else {
                            echo '<span class="text-danger text-sm">Jadwal belum dibuat</span>';
                        }
                        ?>
                    </strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Kalender Hari Aktif</span>
                    <strong class="text-primary-light">
                        <?php
                        $active_days_count = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
                            ->where_in('status', ['Efektif', 'Daring', 'Luar Kelas'])
                            ->get('pembelajaran_hari_efektif')->num_rows();
                        if ($active_days_count > 0) {
                            echo $active_days_count . ' Hari Efektif';
                        } else {
                            echo '<span class="text-danger text-sm">Kalender akademik belum diset</span>';
                        }
                        ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs border-bottom mb-24 gap-2" id="perangkatTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active btn btn-outline-primary radius-8 px-20 py-10" id="berkas-tab" data-bs-toggle="tab" data-bs-target="#tab-berkas" type="button" role="tab">
                <iconify-icon icon="solar:folder-with-files-linear" class="me-1"></iconify-icon> Berkas Perangkat
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn btn-outline-primary radius-8 px-20 py-10" id="modul-tab" data-bs-toggle="tab" data-bs-target="#tab-modul" type="button" role="tab">
                <iconify-icon icon="solar:document-text-linear" class="me-1"></iconify-icon> Modul Ajar / RPP
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">

        <!-- ============================================================ -->
        <!-- TAB 1: Berkas Perangkat                                      -->
        <!-- ============================================================ -->
        <div class="tab-pane fade show active" id="tab-berkas" role="tabpanel">

            <!-- Info bersama: berkas berlaku untuk tingkat & mapel yang sama -->
            <div class="alert bg-info-focus text-info-main border border-info-200 radius-12 p-16 d-flex align-items-start gap-12 mb-24">
                <iconify-icon icon="lucide:info" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                <div class="text-sm">
                    <strong>Informasi:</strong> Berkas perangkat ini berlaku bersama untuk seluruh rombel
                    <strong class="text-info-main"><?php echo html_escape($item->nama_tingkat) ?></strong>
                    yang mengampu mata pelajaran <strong class="text-info-main"><?php echo html_escape($item->nama_mapel) ?></strong>
                    di tahun pelajaran yang sama. Upload sekali, berlaku untuk semua rombel.
                </div>
            </div>

            <div class="card border-0 shadow-sm radius-12">
                <div class="card-header bg-transparent border-bottom p-24 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="mb-0 text-primary-light">Unggah & Kelola Dokumen Pembelajaran</h6>
                        <span class="text-secondary-light text-xs mt-4 d-block">
                            Berkas ini diupload otomatis ke akun google drive anda dan dikelola bersama untuk pembelajaran tingkat <strong><?php echo html_escape($item->nama_tingkat . ' — ' . $item->nama_mapel) ?></strong>
                        </span>
                    </div>
                    <!-- Tombol Salin dari Tahun Lalu -->
                    <?php echo form_open($salin_perangkat_url, ['class' => 'd-inline']); ?>
                    <input type="hidden" name="salin" value="1">
                    <button type="submit"
                        onclick="return confirm('Salin semua referensi berkas dari tahun pelajaran sebelumnya? Berkas yang sudah ada tidak akan dihapus.')"
                        class="btn btn-sm btn-outline-secondary radius-8 px-16 py-8">
                        <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin dari Tahun Lalu
                    </button>
                    <?php echo form_close(); ?>
                </div>
                <div class="card-body p-24">
                    <style>
                    .accordion-button::after {
                        display: none !important;
                    }
                    .accordion-button {
                        padding-inline-end: 16px !important;
                    }
                    </style>
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table bordered-table align-middle">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">No</th>
                                    <th width="280">Nama Dokumen / Perangkat</th>
                                    <th width="150" class="text-center">Status</th>
                                    <th>File & Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $files_config = [
                                    'file_cp'        => ['label' => '1. Capaian Pembelajaran (CP)',              'key' => 'cp',        'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_tp'        => ['label' => '2. Tujuan Pembelajaran (TP)',               'key' => 'tp',        'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_atp'       => ['label' => '3. Alur Tujuan Pembelajaran (ATP)',         'key' => 'atp',       'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_kktp'      => ['label' => '4. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)', 'key' => 'kktp', 'accept' => '.docx,.xlsx',                                                      'hint' => 'DOCX / XLSX saja'],
                                    'file_kisi_sts'  => ['label' => '5. Kisi-kisi Sumatif Tengah Semester (STS)', 'key' => 'kisi_sts', 'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_soal_sts'  => ['label' => '6. Soal Sumatif Tengah Semester (STS)',    'key' => 'soal_sts',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_kisi_sas'  => ['label' => '7. Kisi-kisi Sumatif Akhir Semester (SAS)', 'key' => 'kisi_sas',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    'file_soal_sas'  => ['label' => '8. Soal Sumatif Akhir Semester (SAS)',     'key' => 'soal_sas',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                ];
                                $no = 1;
                                foreach ($files_config as $field => $cfg):
                                    $uploaded_file = $perangkat ? $perangkat->$field : null;
                                ?>
                                    <tr>
                                        <td class="text-center fw-semibold"><?php echo $no++ ?></td>
                                        <td>
                                            <span class="fw-semibold text-primary-light d-block"><?php echo $cfg['label'] ?></span>
                                            <span class="text-muted text-xs"><?php echo $cfg['hint'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($uploaded_file): ?>
                                                <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Sudah Upload</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-focus text-warning-main px-12 py-6 radius-4">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($uploaded_file): ?>
                                                <div class="d-flex align-items-center gap-8">
                                                    <?php
                                                    $key_drive = $cfg['key'] . '_drive_file_id';
                                                    $drive_file_id = $perangkat ? $perangkat->$key_drive : null;
                                                    if ($drive_file_id):
                                                    ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-info-100 text-info-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 btn-preview-doc"
                                                            data-drive-id="<?php echo html_escape($drive_file_id) ?>"
                                                            data-title="<?php echo html_escape($cfg['label']) ?>">
                                                            <iconify-icon icon="lucide:eye"></iconify-icon> Lihat (Preview)
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="<?php echo $unduh_berkas_url . '/' . $cfg['key'] ?>"
                                                        class="btn btn-sm btn-secondary-100 text-secondary-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                        <iconify-icon icon="lucide:download"></iconify-icon> Unduh
                                                    </a>

                                                    <?php if ($drive_file_id):
                                                        // Build dynamic edit link
                                                        $is_xlsx = (strpos($uploaded_file, '.xlsx') !== false);
                                                        $editor_base = $is_xlsx ? 'https://docs.google.com/spreadsheets/d/' : 'https://docs.google.com/document/d/';
                                                        $drive_url = $editor_base . html_escape($drive_file_id) . '/edit';
                                                    ?>
                                                        <a href="<?php echo $drive_url ?>"
                                                            target="_blank"
                                                            class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                            <iconify-icon icon="logos:google-drive" class="align-middle"></iconify-icon> Edit Online
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo $hapus_berkas_url . '/' . $cfg['key'] ?>"
                                                        onclick="return confirm('Hapus berkas ini? Rombel lain yang sama tingkat & mapelnya juga tidak bisa mengaksesnya.')"
                                                        class="btn btn-sm btn-danger-100 text-danger-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus & Upload Ulang
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                 <?php 
                                                 // Determine sequential generation access
                                                 $seq_order = ['file_cp', 'file_tp', 'file_atp', 'file_kktp', 'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'];
                                                 $current_idx = array_search($field, $seq_order);
                                                 $can_generate_ai = true;
                                                 $prev_label = '';
                                                 
                                                 if ($current_idx > 0) {
                                                     $prev_field = $seq_order[$current_idx - 1];
                                                     $prev_uploaded = $perangkat ? $perangkat->$prev_field : null;
                                                     if (!$prev_uploaded) {
                                                         $can_generate_ai = false;
                                                         // Find label of previous field
                                                         foreach ($files_config as $f_k => $cfg_item) {
                                                             if ($f_k === $prev_field) {
                                                                 $prev_label = $cfg_item['label'];
                                                                 break;
                                                             }
                                                         }
                                                     }
                                                 }
                                                 ?>

                                                 <div class="d-flex flex-column gap-2">
                                                     <?php echo form_open_multipart($save_berkas_url, ['class' => 'd-flex align-items-center gap-8']); ?>
                                                     <input type="hidden" name="single_field" value="<?php echo $field ?>">
                                                     <input type="file" name="<?php echo $field ?>" required class="form-control radius-8 form-control-sm w-auto scan-enabled" accept="<?php echo $cfg['accept'] ?>">
                                                     <button type="submit" class="btn btn-sm btn-primary-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                         <iconify-icon icon="lucide:upload-cloud"></iconify-icon> Upload
                                                     </button>
                                                     <?php echo form_close(); ?>

                                                     <div class="d-flex align-items-center gap-8">
                                                         <?php if ($can_generate_ai): ?>
                                                              <?php if ($cfg['key'] === 'kisi_sts' || $cfg['key'] === 'kisi_sas'): ?>
                                                                  <button type="button" 
                                                                      data-field="<?php echo html_escape($field) ?>"
                                                                      data-label="<?php echo html_escape($cfg['label']) ?>"
                                                                      class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 btn-trigger-kisi-modal">
                                                                      <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                                                      Generate via AI
                                                                  </button>
                                                              <?php else: ?>
                                                                  <?php echo form_open($generate_berkas_ai_url); ?>
                                                                  <input type="hidden" name="field" value="<?php echo html_escape($field) ?>">
                                                                  <button type="submit" 
                                                                      data-label="<?php echo html_escape($cfg['label']) ?>"
                                                                      class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 trigger-ai">
                                                                      <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                                                      Generate via AI
                                                                  </button>
                                                                  <?php echo form_close(); ?>
                                                              <?php endif; ?>
                                                         <?php else: ?>
                                                             <button type="button" 
                                                                 disabled 
                                                                 title="Silakan upload/generate <?php echo html_escape($prev_label) ?> terlebih dahulu"
                                                                 class="btn btn-sm btn-light text-muted radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                                 <iconify-icon icon="lucide:lock-keyhole" class="text-xs"></iconify-icon>
                                                                 Generate via AI (Terkunci)
                                                             </button>
                                                             <span class="text-xs text-danger fst-italic">Terkunci! Butuh <?php echo html_escape(preg_replace('/^\d+\.\s+/', '', $prev_label)) ?></span>
                                                         <?php endif; ?>
                                                     </div>
                                                 </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Accordion View -->
                    <div class="accordion d-block d-md-none" id="accordionBerkasPerangkat">
                        <?php
                        $m_no = 1;
                        foreach ($files_config as $field => $cfg):
                            $uploaded_file = $perangkat ? $perangkat->$field : null;
                        ?>
                            <div class="accordion-item border radius-12 mb-12 shadow-xs overflow-hidden">
                                <h2 class="accordion-header" id="headingBerkas<?php echo $cfg['key'] ?>">
                                    <button class="accordion-button <?php echo ($m_no === 1) ? '' : 'collapsed'; ?> bg-base text-primary-light px-16 py-12" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapseBerkas<?php echo $cfg['key'] ?>" 
                                            aria-expanded="<?php echo ($m_no === 1) ? 'true' : 'false'; ?>" 
                                            aria-controls="collapseBerkas<?php echo $cfg['key'] ?>">
                                        <div class="w-100 me-2 d-flex align-items-center justify-content-between gap-2">
                                            <div>
                                                <span class="fw-bold text-primary-900 text-sm"><?php echo html_escape($cfg['label']) ?></span>
                                            </div>
                                            <div>
                                                <?php if ($uploaded_file): ?>
                                                    <span class="badge bg-success-focus text-success-main px-8 py-3 radius-4 text-xs">Sudah Upload</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-focus text-warning-main px-8 py-3 radius-4 text-xs">Belum Ada</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapseBerkas<?php echo $cfg['key'] ?>" 
                                     class="accordion-collapse collapse <?php echo ($m_no === 1) ? 'show' : ''; ?>" 
                                     aria-labelledby="headingBerkas<?php echo $cfg['key'] ?>" 
                                     data-bs-parent="#accordionBerkasPerangkat">
                                    <div class="accordion-body bg-neutral-50 p-16">
                                        <span class="text-muted text-xs d-block mb-12"><?php echo $cfg['hint'] ?></span>
                                        <?php if ($uploaded_file): ?>
                                            <div class="d-flex flex-column gap-2">
                                                <?php
                                                $key_drive = $cfg['key'] . '_drive_file_id';
                                                $drive_file_id = $perangkat ? $perangkat->$key_drive : null;
                                                if ($drive_file_id):
                                                ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-info-100 text-info-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 btn-preview-doc w-100"
                                                        data-drive-id="<?php echo html_escape($drive_file_id) ?>"
                                                        data-title="<?php echo html_escape($cfg['label']) ?>">
                                                        <iconify-icon icon="lucide:eye"></iconify-icon> Lihat (Preview)
                                                    </button>
                                                <?php endif; ?>

                                                <a href="<?php echo $unduh_berkas_url . '/' . $cfg['key'] ?>"
                                                    class="btn btn-sm btn-secondary-100 text-secondary-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                                    <iconify-icon icon="lucide:download"></iconify-icon> Unduh
                                                </a>

                                                <?php if ($drive_file_id):
                                                    $is_xlsx = (strpos($uploaded_file, '.xlsx') !== false);
                                                    $editor_base = $is_xlsx ? 'https://docs.google.com/spreadsheets/d/' : 'https://docs.google.com/document/d/';
                                                    $drive_url = $editor_base . html_escape($drive_file_id) . '/edit';
                                                ?>
                                                    <a href="<?php echo $drive_url ?>"
                                                        target="_blank"
                                                        class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                                        <iconify-icon icon="logos:google-drive" class="align-middle"></iconify-icon> Edit Online
                                                    </a>
                                                <?php endif; ?>

                                                <a href="<?php echo $hapus_berkas_url . '/' . $cfg['key'] ?>"
                                                    onclick="return confirm('Hapus berkas ini? Rombel lain yang sama tingkat & mapelnya juga tidak bisa mengaksesnya.')"
                                                    class="btn btn-sm btn-danger-100 text-danger-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                                    <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus & Upload Ulang
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <?php 
                                            $seq_order = ['file_cp', 'file_tp', 'file_atp', 'file_kktp', 'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'];
                                            $current_idx = array_search($field, $seq_order);
                                            $can_generate_ai = true;
                                            $prev_label = '';
                                            
                                            if ($current_idx > 0) {
                                                $prev_field = $seq_order[$current_idx - 1];
                                                $prev_uploaded = $perangkat ? $perangkat->$prev_field : null;
                                                if (!$prev_uploaded) {
                                                    $can_generate_ai = false;
                                                    foreach ($files_config as $f_k => $cfg_item) {
                                                        if ($f_k === $prev_field) {
                                                            $prev_label = $cfg_item['label'];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>

                                            <div class="d-flex flex-column gap-2">
                                                <?php echo form_open_multipart($save_berkas_url, ['class' => 'd-flex flex-column gap-2']); ?>
                                                <input type="hidden" name="single_field" value="<?php echo $field ?>">
                                                <input type="file" name="<?php echo $field ?>" required class="form-control radius-8 form-control-sm w-100 scan-enabled" accept="<?php echo $cfg['accept'] ?>">
                                                <button type="submit" class="btn btn-sm btn-primary-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                                    <iconify-icon icon="lucide:upload-cloud"></iconify-icon> Upload
                                                </button>
                                                <?php echo form_close(); ?>

                                                <div class="mt-2">
                                                    <?php if ($can_generate_ai): ?>
                                                         <?php if ($cfg['key'] === 'kisi_sts' || $cfg['key'] === 'kisi_sas'): ?>
                                                             <button type="button" 
                                                                 data-field="<?php echo html_escape($field) ?>"
                                                                 data-label="<?php echo html_escape($cfg['label']) ?>"
                                                                 class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100 btn-trigger-kisi-modal">
                                                                 <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                                                 Generate via AI
                                                             </button>
                                                         <?php else: ?>
                                                             <?php echo form_open($generate_berkas_ai_url); ?>
                                                             <input type="hidden" name="field" value="<?php echo html_escape($field) ?>">
                                                             <button type="submit" 
                                                                 data-label="<?php echo html_escape($cfg['label']) ?>"
                                                                 class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100 trigger-ai">
                                                                 <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                                                 Generate via AI
                                                             </button>
                                                             <?php echo form_close(); ?>
                                                         <?php endif; ?>
                                                    <?php else: ?>
                                                        <button type="button" 
                                                            disabled 
                                                            title="Silakan upload/generate <?php echo html_escape($prev_label) ?> terlebih dahulu"
                                                            class="btn btn-sm btn-light text-muted radius-8 px-12 py-8 d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                                            <iconify-icon icon="lucide:lock-keyhole" class="text-xs"></iconify-icon>
                                                            Generate via AI (Terkunci)
                                                        </button>
                                                        <span class="text-xs text-danger fst-italic mt-1 d-block text-center">Terkunci! Butuh <?php echo html_escape(preg_replace('/^\d+\.\s+/', '', $prev_label)) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php $m_no++; endforeach; ?>
                    </div>
                </div>
            </div>
    </div><!-- /tab-berkas -->


    <!-- ============================================================ -->
    <!-- TAB 2: Modul Ajar / RPP (Multifile & AI Generator)           -->
    <!-- ============================================================ -->
    <div class="tab-pane fade" id="tab-modul" role="tabpanel">
        
        <!-- Info bersama -->
        <div class="alert bg-info-focus text-info-main border border-info-200 radius-12 p-16 d-flex align-items-start gap-12 mb-24">
            <iconify-icon icon="lucide:info" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
            <div class="text-sm">
                <strong>Informasi:</strong> Dokumen Modul Ajar / RPP mendukung penyimpanan lebih dari 1 file. 
                Anda dapat mengunggah berkas secara manual atau merumuskannya langsung via **Google AI** berdasarkan berkas ATP yang telah diunggah sebelumnya. 
                Modul Ajar yang digenerate AI akan disimpan langsung ke Google Drive sebagai dokumen DOCX yang dapat Anda **Edit Online**.
            </div>
        </div>

        <div class="row gy-4">
            <!-- Kolom Kiri: Daftar File Modul Ajar -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm radius-12 h-100">
                    <div class="card-header bg-transparent border-bottom p-24">
                        <h6 class="mb-0 text-primary-light">Daftar Modul Ajar / RPP Aktif</h6>
                    </div>
                    <div class="card-body p-24">
                        <!-- Desktop Table View -->
                        <div class="table-responsive w-100 d-none d-md-block">
                            <table class="table bordered-table align-middle w-100" id="modulAjarTable" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th width="40" class="text-center">No</th>
                                        <th>Nama Modul / Rencana Kegiatan</th>
                                        <th width="150" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no_modul = 1;
                                    foreach ($modul_ajar_list as $modul): 
                                    ?>
                                        <tr>
                                            <td class="text-center fw-semibold"><?php echo $no_modul++ ?></td>
                                            <td>
                                                <span class="fw-semibold text-primary-light d-block"><?php echo html_escape($modul->label) ?></span>
                                                <span class="text-muted text-xs d-block mt-4" style="word-break: break-all;">
                                                    <iconify-icon icon="lucide:file-text" class="align-middle"></iconify-icon> 
                                                    <?php echo html_escape($modul->nama_file) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-8 justify-content-center">
                                                    <?php if ($modul->drive_file_id): ?>
                                                        <button type="button"
                                                            class="btn btn-xs btn-info-100 text-info-600 radius-8 p-8 d-inline-flex align-items-center btn-preview-doc"
                                                            title="Lihat (Preview)"
                                                            data-drive-id="<?php echo html_escape($modul->drive_file_id) ?>"
                                                            data-title="<?php echo html_escape($modul->label) ?>">
                                                            <iconify-icon icon="lucide:eye"></iconify-icon>
                                                        </button>

                                                        <?php 
                                                        $drive_url = 'https://docs.google.com/document/d/' . html_escape($modul->drive_file_id) . '/edit';
                                                        ?>
                                                        <a href="<?php echo $drive_url ?>"
                                                            target="_blank"
                                                            title="Edit Online"
                                                            class="btn btn-xs btn-success-100 text-success-600 radius-8 p-8 d-inline-flex align-items-center">
                                                            <iconify-icon icon="logos:google-drive"></iconify-icon>
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="<?php echo $unduh_modul_url . '/' . $modul->id_modul ?>"
                                                        title="Unduh (.docx)"
                                                        class="btn btn-xs btn-secondary-100 text-secondary-600 radius-8 p-8 d-inline-flex align-items-center">
                                                        <iconify-icon icon="lucide:download"></iconify-icon>
                                                    </a>

                                                    <a href="<?php echo $delete_modul_url . '/' . $modul->id_modul ?>"
                                                        title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus berkas modul ajar ini? Berkas lokal dan Google Drive akan dihapus secara permanen.')"
                                                        class="btn btn-xs btn-danger-100 text-danger-600 radius-8 p-8 d-inline-flex align-items-center">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($modul_ajar_list)): ?>
                                        <tr>
                                            <td></td>
                                            <td class="text-center py-24 text-secondary-light text-sm">
                                                Belum ada file Modul Ajar / RPP yang diunggah atau digenerate.
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Accordion View -->
                        <div class="accordion d-block d-md-none" id="accordionModulAjar">
                            <?php if (!empty($modul_ajar_list)): ?>
                                <?php 
                                $m_no_modul = 1;
                                foreach ($modul_ajar_list as $modul): 
                                ?>
                                    <div class="accordion-item border radius-12 mb-12 shadow-xs overflow-hidden">
                                        <h2 class="accordion-header" id="headingModul<?php echo $modul->id_modul ?>">
                                            <button class="accordion-button <?php echo ($m_no_modul === 1) ? '' : 'collapsed'; ?> bg-base text-primary-light px-16 py-12" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapseModul<?php echo $modul->id_modul ?>" 
                                                    aria-expanded="<?php echo ($m_no_modul === 1) ? 'true' : 'false'; ?>" 
                                                    aria-controls="collapseModul<?php echo $modul->id_modul ?>">
                                                <div class="w-100 me-2">
                                                    <span class="fw-bold text-primary-900 text-sm d-block"><?php echo html_escape($modul->label) ?></span>
                                                    <span class="text-muted text-xs d-block mt-2" style="word-break: break-all;">
                                                        <iconify-icon icon="lucide:file-text" class="align-middle"></iconify-icon> 
                                                        <?php echo html_escape($modul->nama_file) ?>
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseModul<?php echo $modul->id_modul ?>" 
                                             class="accordion-collapse collapse <?php echo ($m_no_modul === 1) ? 'show' : ''; ?>" 
                                             aria-labelledby="headingModul<?php echo $modul->id_modul ?>" 
                                             data-bs-parent="#accordionModulAjar">
                                            <div class="accordion-body bg-neutral-50 p-16">
                                                <div class="d-flex align-items-center gap-8 justify-content-center flex-wrap">
                                                    <?php if ($modul->drive_file_id): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-info-100 text-info-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 btn-preview-doc flex-grow-1 justify-content-center"
                                                            title="Lihat (Preview)"
                                                            data-drive-id="<?php echo html_escape($modul->drive_file_id) ?>"
                                                            data-title="<?php echo html_escape($modul->label) ?>">
                                                            <iconify-icon icon="lucide:eye"></iconify-icon> Lihat
                                                        </button>

                                                        <?php $drive_url = 'https://docs.google.com/document/d/' . html_escape($modul->drive_file_id) . '/edit'; ?>
                                                        <a href="<?php echo $drive_url ?>"
                                                            target="_blank"
                                                            title="Edit Online"
                                                            class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 flex-grow-1 justify-content-center">
                                                            <iconify-icon icon="logos:google-drive"></iconify-icon> Edit Online
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="<?php echo $unduh_modul_url . '/' . $modul->id_modul ?>"
                                                        title="Unduh (.docx)"
                                                        class="btn btn-sm btn-secondary-100 text-secondary-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 flex-grow-1 justify-content-center">
                                                        <iconify-icon icon="lucide:download"></iconify-icon> Unduh
                                                    </a>

                                                    <a href="<?php echo $delete_modul_url . '/' . $modul->id_modul ?>"
                                                        title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus berkas modul ajar ini? Berkas lokal dan Google Drive akan dihapus secara permanen.')"
                                                        class="btn btn-sm btn-danger-100 text-danger-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1 flex-grow-1 justify-content-center">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php $m_no_modul++; endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-neutral-400 py-24 bg-base radius-12 border">
                                    <iconify-icon icon="lucide:file-text" style="font-size: 28px;"></iconify-icon>
                                    <div class="mt-4 text-xs">Belum ada file Modul Ajar / RPP yang diunggah atau digenerate.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Upload Manual & AI Generator -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    
                    <!-- Box 1: Upload Manual -->
                    <div class="card border-0 shadow-sm radius-12">
                        <div class="card-header bg-transparent border-bottom p-24">
                            <h6 class="mb-0 text-primary-light">Upload Modul Ajar Manual</h6>
                        </div>
                        <div class="card-body p-24">
                            <?php echo form_open_multipart($upload_modul_url); ?>
                            <div class="mb-16">
                                <label class="form-label text-sm fw-semibold text-secondary-light">Nama / Label Modul</label>
                                <input type="text" name="label" required placeholder="Contoh: Modul Ajar Pertemuan 1 - Algoritma" class="form-control radius-8">
                            </div>
                            <div class="mb-20">
                                <label class="form-label text-sm fw-semibold text-secondary-light">Pilih File (.docx saja)</label>
                                <input type="file" name="file_modul_rpp" required class="form-control radius-8" accept=".docx">
                            </div>
                            <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 d-inline-flex align-items-center justify-content-center gap-1">
                                <iconify-icon icon="lucide:upload-cloud"></iconify-icon> Unggah Berkas
                            </button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>

                    <!-- Box 2: AI Generator -->
                    <div class="card border-0 shadow-sm radius-12 border border-success-200">
                        <div class="card-header bg-success-50 border-bottom border-success-100 p-24">
                            <h6 class="mb-0 text-success-800 d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                Google AI Modul Ajar Generator
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <?php 
                            $has_atp = $perangkat && !empty($perangkat->file_atp); 
                            if (!$has_atp):
                            ?>
                                <div class="text-center py-16">
                                    <iconify-icon icon="lucide:alert-circle" class="text-warning-600 text-3xl mb-8"></iconify-icon>
                                    <p class="text-xs text-secondary-light mb-0">
                                        Untuk menggunakan generator AI, silakan upload file **Alur Tujuan Pembelajaran (ATP)** terlebih dahulu di tab **Berkas Perangkat**.
                                    </p>
                                </div>
                            <?php else: ?>
                                <?php echo form_open($generate_modul_ai_url); ?>
                                <div class="mb-20">
                                    <label class="form-label text-sm fw-semibold text-secondary-light">Topik / Bahasan Pembelajaran</label>
                                    <textarea name="topic" required rows="3" placeholder="Contoh: Pengenalan struktur percabangan If-Else pada bahasa Python dengan studi kasus lampu lalu lintas..." class="form-control radius-8"></textarea>
                                    <div class="text-xs text-secondary-light mt-4">Tulis topik secara mendalam agar AI dapat menyusun RPP / Modul Ajar yang lengkap dan detail.</div>
                                </div>
                                <button type="submit" 
                                    data-label="Modul Ajar / RPP"
                                    onclick="return confirm('AI akan menyusun modul ajar interaktif lengkap dan menyimpannya langsung ke Google Drive sebagai berkas DOCX. Lanjutkan?')"
                                    class="btn btn-success-600 w-100 radius-8 py-10 d-inline-flex align-items-center justify-content-center gap-1 trigger-ai">
                                    <iconify-icon icon="logos:google-gemini" class="align-middle"></iconify-icon>
                                    Generate via Google AI
                                </button>
                                <?php echo form_close(); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div><!-- /tab-modul -->

</div><!-- /tab-content -->
</div><!-- /dashboard-main-body -->


<!-- ================================================================ -->
<!-- Modal: Edit Agenda (TinyMCE)                                     -->
<!-- ================================================================ -->
<div class="modal fade" id="modalEditAgenda" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
            <?php echo form_open($save_agenda_url, ['id' => 'form-edit-agenda']); ?>
            <div class="modal-header bg-warning-900 p-20 text-light border-0">
                <h6 class="modal-title text-light" id="modal-agenda-title">Edit Agenda Harian</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-24">
                <input type="hidden" name="id_agenda" id="modal-id-agenda">
                <input type="hidden" name="slide_drive_id" id="modal-slide-drive-id">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Hari / Tanggal</label>
                        <input type="text" id="modal-date-text" readonly class="form-control-plaintext fw-bold text-primary-light">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                        <select name="status" id="modal-status" class="form-select radius-8">
                            <option value="Belum">Belum Terlaksana</option>
                            <option value="Terlaksana">Terlaksana</option>
                            <option value="Libur">Libur KBM</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jml Jam</label>
                        <div class="input-group">
                            <input type="number" name="jumlah_jam" id="modal-jumlah-jam" class="form-control radius-8">
                            <span class="input-group-text">JP</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="modal-jam-mulai" class="form-control radius-8">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="modal-jam-selesai" class="form-control radius-8">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Materi Pembelajaran</label>
                        <textarea id="modal-materi-editor" class="tinymce-editor"></textarea>
                        <input type="hidden" name="materi" id="modal-materi-submit">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kegiatan Pembelajaran</label>
                        <textarea id="modal-kegiatan-editor" class="tinymce-editor"></textarea>
                        <input type="hidden" name="kegiatan" id="modal-kegiatan-submit">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan / Hambatan</label>
                        <input type="text" name="catatan" id="modal-catatan" placeholder="Misal: Siswa antusias, listrik padam..." class="form-control radius-8">
                    </div>
                    
                    <!-- Widget Asisten AI (Google Gemini) -->
                    <div class="col-12">
                        <div class="card border border-primary-200 bg-primary-50 radius-12 p-16">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <h6 class="text-primary-600 mb-0 d-flex align-items-center gap-2 text-sm fw-bold">
                                    <iconify-icon icon="logos:google-gemini" style="font-size:18px;"></iconify-icon>
                                    Generate Media & Materi Pembelajaran (Google AI)
                                </h6>
                                <span class="badge bg-primary-100 text-primary-600 text-xs px-8 py-4 radius-4">Kurikulum Merdeka</span>
                            </div>

                            <div class="row gy-2">
                                <div class="col-md-5">
                                    <label class="form-label text-xs fw-semibold text-secondary-light mb-4">Pilih Jenis Output / Media</label>
                                    <select id="ai-media-type" class="form-select form-select-sm radius-8 text-xs">
                                        <option value="gambar">🖼️ Diagram / Visual (SVG/HTML Card)</option>
                                        <option value="slide">📊 Slide Presentasi Interaktif</option>
                                        <option value="materi">📝 Rangkuman Materi & Kuis Pembelajaran</option>
                                        <option value="youtube">🎬 Cari Link Video YouTube Relevan</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label text-xs fw-semibold text-secondary-light mb-4">Prompt / Instruksi Tambahan (Opsional)</label>
                                    <input type="text" id="ai-custom-prompt" placeholder="Contoh: Buat slide 4 poin penting, buat diagram daur air..." class="form-control form-control-sm radius-8 text-xs">
                                </div>
                                <div class="col-12 mt-8 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <span class="text-xs text-secondary-light">
                                        <i class="ri-information-line me-1"></i> Audio & Video generator tidak diperkenankan; video dicari dari YouTube.
                                    </span>
                                    <button type="button" id="btn-generate-ai-media" class="btn btn-sm btn-primary-600 radius-8 px-16 py-6 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:stars-minimalistic-bold" class="text-sm"></iconify-icon>
                                        <span id="ai-btn-text">Generate dengan AI</span>
                                    </button>
                                </div>
                            </div>
                            <div id="ai-media-alert" class="mt-8 d-none"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            <iconify-icon icon="logos:youtube-icon" class="me-1" style="font-size:16px;"></iconify-icon>
                            Link Video Pembelajaran (Opsional)
                        </label>
                        <input type="url" name="link_video" id="modal-link-video"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="form-control radius-8">
                        <div class="text-xs text-secondary-light mt-4">Tempel URL YouTube atau platform video lain sebagai referensi belajar untuk pertemuan ini.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-20 border-0 bg-light d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">
                    <iconify-icon icon="lucide:check" class="me-1"></iconify-icon> Simpan Perubahan
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<!-- ================================================================ -->
<!-- Modal: Salin Isi Agenda dari Sumber Lain                        -->
<!-- ================================================================ -->
<?php if ($source_last_year_id || !empty($other_active_rombel_agendas)): ?>
    <div class="modal fade" id="modalSalinAgenda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
                <?php echo form_open($salin_agenda_url, ['id' => 'form-salin-agenda']); ?>
                <input type="hidden" name="salin" value="1">
                <div class="modal-header bg-neutral-800 p-20 border-0">
                    <h6 class="modal-title text-white">Salin Isi Agenda dari Sumber</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <p class="text-sm text-secondary-light mb-20">
                        Pilih sumber agenda yang akan disalin isinya (materi, kegiatan, status) ke agenda rombel ini.
                        Penyalinan dilakukan berdasarkan nomor pertemuan yang cocok.
                    </p>

                    <div class="d-flex flex-column gap-12">

                        <?php 
                        $first_checked = false;
                        if ($source_last_year_id): 
                            $first_checked = true;
                        ?>
                            <label class="d-flex align-items-start gap-12 p-16 border border-2 border-primary radius-12 cursor-pointer salin-option" style="cursor:pointer">
                                <input type="radio" name="source_id" value="<?php echo $source_last_year_id ?>" class="form-check-input mt-1 flex-shrink-0" checked>
                                <div>
                                    <span class="fw-semibold text-primary-light d-block">
                                        <iconify-icon icon="lucide:history" class="me-1"></iconify-icon>
                                        Salin dari Tahun Lalu
                                    </span>
                                    <span class="text-xs text-secondary-light">
                                        Rombel <?php echo html_escape($item->nama_rombel) ?> — Mapel <?php echo html_escape($item->nama_mapel) ?> — Tahun sebelumnya
                                    </span>
                                </div>
                            </label>
                        <?php endif; ?>

                        <?php foreach ($other_active_rombel_agendas as $index => $rombel): 
                            $checked_attr = '';
                            $border_class = 'border-secondary';
                            if (!$first_checked && $index === 0) {
                                $checked_attr = 'checked';
                                $border_class = 'border-primary';
                            }
                        ?>
                            <label class="d-flex align-items-start gap-12 p-16 border border-2 <?php echo $border_class ?> radius-12 cursor-pointer salin-option" style="cursor:pointer">
                                <input type="radio" name="source_id" value="<?php echo $rombel->id_pembelajaran_mapel ?>" class="form-check-input mt-1 flex-shrink-0" <?php echo $checked_attr ?>>
                                <div>
                                    <span class="fw-semibold text-primary-light d-block">
                                        <iconify-icon icon="lucide:users" class="me-1"></iconify-icon>
                                        Salin dari Rombel <?php echo html_escape($rombel->nama_rombel) ?>
                                    </span>
                                    <span class="text-xs text-secondary-light">
                                        <?php echo html_escape($item->nama_mapel) ?> — <?php echo html_escape($rombel->tahun_pelajaran . ' (' . $rombel->semester . ')') ?> (<?php echo $rombel->total_agenda ?> Agenda)
                                    </span>
                                </div>
                            </label>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="modal-footer p-20 border-0 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        onclick="return confirm('Menyalin akan menimpa isi materi & kegiatan agenda saat ini. Lanjutkan?')"
                        class="btn btn-neutral-700 radius-8 px-20 py-10 text-white">
                        <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin Sekarang
                    </button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php include viewPath('includes/footer'); ?>

<!-- TinyMCE CDN -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    $(document).ready(function() {

        // Cek jika baru saja berhasil men-generate berkas/agenda via AI
        if (localStorage.getItem('ai_generated_just_now') === 'true') {
            localStorage.removeItem('ai_generated_just_now');
            Swal.fire({
                title: '<span style="color:#ef4444; font-weight:bold; font-size:20px;">PENTING: Mohon Telaah Ulang Hasil Dokumen</span>',
                html: '<div style="text-align:justify; font-size:14px; line-height:1.6; color:#374151;">' +
                      '<p>Dokumen ini dihasilkan oleh kecerdasan buatan (AI). Perlu diingat bahwa AI adalah alat bantu, bukan penentu.</p>' +
                      '<p style="margin-top:12px; font-weight: 500;">Sebagai pendidik, Anda adalah benteng utama yang menyaring ilmu. Harap cek kembali kesesuaian materi ini dengan kurikulum dan norma yang berlaku. Jangan biarkan kesalahan teknis teknologi menyesatkan logika dan moral generasi anak bangsa. <strong>Tinjau, edit, dan sempurnakan!</strong></p>' +
                      '</div>',
                icon: 'warning',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Saya Mengerti dan Akan Memeriksa',
                allowOutsideClick: false
            });
        }

        // Handle URL tab parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab === 'agenda') {
            const agendaTabBtn = new bootstrap.Tab(document.querySelector('#agenda-tab'));
            agendaTabBtn.show();
        } else if (activeTab === 'modul') {
            const modulTabBtn = new bootstrap.Tab(document.querySelector('#modul-tab'));
            modulTabBtn.show();
        }

        // Initialize DataTable for agenda
        if ($('#agendaTable').length > 0) {
            $('#agendaTable').DataTable({
                pageLength: 25,
                order: [
                    [0, 'asc']
                ]
            });
        }

        // Initialize DataTable for modul ajar list
        if ($('#modulAjarTable').length > 0) {
            $('#modulAjarTable').DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
        }

        // Initialize TinyMCE Rich Text Editor ala Google Docs inside modal
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            height: 380,
            menubar: 'file edit view insert format tools table',
            branding: false,
            statusbar: true,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount fontfamily fontsize blocks align forecolor backcolor',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image media | removeformat code fullscreen preview',
            content_style: 'body { font-family:Inter,sans-serif; font-size:14px; line-height:1.6; }'
        });

        // Handle "Edit Agenda" button click
        $(document).on('click', '.edit-agenda-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            const pert = btn.data('pertemuan');
            const hari = btn.data('hari');
            const tgl = btn.data('tanggal');
            const status = btn.data('status');
            const catatan = btn.data('catatan');
            const video = btn.data('video') || '';
            const slideDriveId = btn.data('slide-drive-id') || '';
            const jmlJam = btn.data('jumlah-jam');
            const jamMulai = btn.data('jam-mulai');
            const jamSelesai = btn.data('jam-selesai');

            const materi = btn.closest('tr').find('.hidden-materi').val() || '';
            const kegiatan = btn.closest('tr').find('.hidden-kegiatan').val() || '';

            $('#modal-agenda-title').text('Edit Agenda — Pertemuan Ke-' + pert);
            $('#modal-id-agenda').val(id);
            $('#modal-slide-drive-id').val(slideDriveId);
            $('#modal-date-text').val(hari + ', ' + tgl);
            $('#modal-status').val(status);
            $('#modal-catatan').val(catatan);
            $('#modal-link-video').val(video);
            $('#modal-jumlah-jam').val(jmlJam);
            $('#modal-jam-mulai').val(jamMulai);
            $('#modal-jam-selesai').val(jamSelesai);

            // Reset AI Widget inputs
            $('#ai-custom-prompt').val('');
            $('#ai-media-alert').addClass('d-none').removeClass('alert-danger alert-success').html('');

            // Set TinyMCE content after modal is shown
            const modalEl = document.getElementById('modalEditAgenda');
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();

            modalEl.addEventListener('shown.bs.modal', function handler() {
                if (tinymce.get('modal-materi-editor')) tinymce.get('modal-materi-editor').setContent(materi);
                if (tinymce.get('modal-kegiatan-editor')) tinymce.get('modal-kegiatan-editor').setContent(kegiatan);
                modalEl.removeEventListener('shown.bs.modal', handler);
            });
        });

        // Handle Generate Media AI di Modal Edit Agenda
        $(document).on('click', '#btn-generate-ai-media', function() {
            var btn = $(this);
            var jenisMedia = $('#ai-media-type').val();
            var customPrompt = $('#ai-custom-prompt').val();

            // Ambil materi dari TinyMCE atau teks biasa
            var currentMateriHtml = '';
            if (tinymce.get('modal-materi-editor')) {
                currentMateriHtml = tinymce.get('modal-materi-editor').getContent({format: 'text'}).trim();
            }
            if (!currentMateriHtml) {
                currentMateriHtml = $('#modal-date-text').val() || 'Pembelajaran Terpadu';
            }

            var generateUrl = "<?php echo $generate_media_ai_url ?? '' ?>";

            btn.prop('disabled', true);
            $('#ai-btn-text').text('Menyusun AI...');
            $('#ai-media-alert').addClass('d-none').removeClass('alert-danger alert-success').html('');

            $.ajax({
                url: generateUrl,
                type: 'POST',
                data: {
                    jenis_media: jenisMedia,
                    materi_topik: currentMateriHtml,
                    prompt_tambahan: customPrompt
                },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false);
                    $('#ai-btn-text').text('Generate dengan AI');

                    if (res && res.status && res.data) {
                        var d = res.data;
                        if (d.type === 'youtube' && d.url) {
                            $('#modal-link-video').val(d.url);
                            $('#ai-media-alert').removeClass('d-none alert-danger').addClass('alert alert-success py-6 px-12 text-xs')
                                .html('<i class="ri-checkbox-circle-fill me-1"></i> Video YouTube ditemukan: <strong>' + (d.title || d.url) + '</strong> (Otomatis dimasukkan ke kolom Link Video Pembelajaran).');
                        } else if (d.type === 'html' && d.content) {
                            if (jenisMedia === 'slide') {
                                if (d.slide_drive_id) {
                                    $('#modal-slide-drive-id').val(d.slide_drive_id);
                                    var driveEditUrl = 'https://docs.google.com/document/d/' + d.slide_drive_id + '/edit';
                                    $('#ai-media-alert').removeClass('d-none alert-danger').addClass('alert alert-success py-8 px-12 text-xs')
                                        .html('<i class="ri-checkbox-circle-fill me-1"></i> Slide Presentasi berhasil dibuat & disimpan ke Google Drive Anda!<br>'
                                            + '<div class="mt-4 gap-2 d-flex"><button type="button" class="btn btn-xs btn-info-100 text-info-600 radius-6 px-8 py-4 btn-preview-doc" data-drive-id="' + d.slide_drive_id + '" data-title="' + d.title + '"><i class="ri-eye-line me-1"></i> Lihat Preview</button> '
                                            + '<a href="' + driveEditUrl + '" target="_blank" class="btn btn-xs btn-success-100 text-success-600 radius-6 px-8 py-4"><i class="ri-google-fill me-1"></i> Edit Online di Google Drive</a></div>');
                                } else {
                                    $('#ai-media-alert').removeClass('d-none alert-danger').addClass('alert alert-success py-6 px-12 text-xs')
                                        .html('<i class="ri-checkbox-circle-fill me-1"></i> Slide Presentasi berhasil digenerate.');
                                }

                                if (tinymce.get('modal-materi-editor')) {
                                    var oldContent = tinymce.get('modal-materi-editor').getContent();
                                    tinymce.get('modal-materi-editor').setContent(oldContent + '<br>' + d.content);
                                }
                            } else {
                                // Diagram / Visual atau Rangkuman Materi & Kuis -> Masuk ke editor Materi Pembelajaran
                                if (tinymce.get('modal-materi-editor')) {
                                    var oldContent = tinymce.get('modal-materi-editor').getContent();
                                    tinymce.get('modal-materi-editor').setContent(oldContent + '<br>' + d.content);
                                }
                                $('#ai-media-alert').removeClass('d-none alert-danger').addClass('alert alert-success py-6 px-12 text-xs')
                                    .html('<i class="ri-checkbox-circle-fill me-1"></i> ' + (d.title || 'Media/Materi') + ' berhasil digenerate dan dimasukkan ke form Materi Pembelajaran.');
                            }
                        } else {
                            $('#ai-media-alert').removeClass('d-none alert-success').addClass('alert alert-danger py-6 px-12 text-xs')
                                .html('<i class="ri-error-warning-fill me-1"></i> Gagal memproses keluaran AI.');
                        }
                    } else {
                        var msg = (res && res.message) ? res.message : 'Terjadi kesalahan sistem.';
                        $('#ai-media-alert').removeClass('d-none alert-success').addClass('alert alert-danger py-6 px-12 text-xs')
                            .html('<i class="ri-error-warning-fill me-1"></i> ' + msg);
                    }
                },
                error: function() {
                    btn.prop('disabled', false);
                    $('#ai-btn-text').text('Generate dengan AI');
                    $('#ai-media-alert').removeClass('d-none alert-success').addClass('alert alert-danger py-6 px-12 text-xs')
                        .html('<i class="ri-error-warning-fill me-1"></i> Gagal terhubung ke server untuk proses AI.');
                }
            });
        });

        // Before form submits, copy TinyMCE content to hidden inputs
        $('#form-edit-agenda').on('submit', function() {
            if (tinymce.get('modal-materi-editor')) $('#modal-materi-submit').val(tinymce.get('modal-materi-editor').getContent());
            if (tinymce.get('modal-kegiatan-editor')) $('#modal-kegiatan-submit').val(tinymce.get('modal-kegiatan-editor').getContent());
            return true;
        });

        // Salin agenda: visual selection highlight
        $(document).on('change', 'input[name="source_id"]', function() {
            $('.salin-option').removeClass('border-primary').addClass('border-secondary');
            $(this).closest('.salin-option').removeClass('border-secondary').addClass('border-primary');
        });

        // Preview Google Drive Document Ajax Modal Trigger
        $(document).on('click', '.btn-preview-doc', function() {
            var driveId = $(this).data('drive-id');
            var title = $(this).data('title');

            $('#previewDocTitle').text(title);

            // Show loading spinner
            $('#preview-doc-body').html('<div class="text-center py-40"><iconify-icon icon="line-md:loading-twotone-loop" class="text-primary-600" style="font-size: 48px;"></iconify-icon><p class="text-secondary-light text-sm mt-8">Memuat pratinjau dokumen dari Google Drive...</p></div>');

            var bsModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPreviewDoc'));
            bsModal.show();

            // Build preview URL: Google Drive Preview URL
            var previewUrl = 'https://docs.google.com/file/d/' + driveId + '/preview';

            setTimeout(function() {
                var iframeHtml = '<iframe src="' + previewUrl + '" width="100%" height="600" style="border: none; border-radius: 8px;"></iframe>';
                $('#preview-doc-body').html(iframeHtml);
            }, 300);
        });

        // Handle Trigger AI Loading Spinner
        $(document).on('click', '.trigger-ai', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = btn.closest('form');
            var docLabel = btn.data('label') || 'Dokumen';

            var confirmMsg = "AI akan menyusun \"" + docLabel + "\" dan menyimpannya langsung ke Google Drive. Lanjutkan?";
            if (docLabel.indexOf('Agenda') !== -1) {
                confirmMsg = "Apakah Anda yakin ingin men-generate agenda secara otomatis menggunakan Google AI? Proses ini akan menyusun silabus baru.";
            }

            Swal.fire({
                title: 'Konfirmasi AI',
                text: confirmMsg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Inline Loading Spinner
                    btn.addClass('btn-warning-100 text-warning-600').removeClass('btn-success-100 btn-success text-success-600');
                    btn.html('<iconify-icon icon="line-md:loading-twotone-loop" class="align-middle me-1"></iconify-icon> Menyusun ' + docLabel + ' via AI...');

                    // Set flag untuk deteksi reload
                    localStorage.setItem('ai_generated_just_now', 'true');

                    // Submit form native
                    if (form.length > 0) {
                        form[0].submit();
                    }
                }
            });
        });

        // Trigger Kisi-kisi Parameter Config Modal
        $(document).on('click', '.btn-trigger-kisi-modal', function() {
            var field = $(this).data('field');
            var label = $(this).data('label');
            
            $('#kisi-field-input').val(field);
            $('#kisiConfigTitle').text('Konfigurasi AI: ' + label);

            // Reset forms
            $('#kisiForm')[0].reset();
            $('#form-group-pg, #form-group-essai').show(); // show both by default

            var modalKisi = new bootstrap.Modal(document.getElementById('modalKisiConfig'));
            modalKisi.show();
        });

        // Dynamic toggle pg / essai inputs based on select box
        $(document).on('change', '#kisi-bentuk-soal', function() {
            var val = $(this).val();
            if (val === 'Pilihan Ganda') {
                $('#form-group-pg').show();
                $('#form-group-essai').hide();
                $('#kisi-jumlah-essai').val('0');
            } else if (val === 'Essai') {
                $('#form-group-pg').hide();
                $('#form-group-essai').show();
                $('#kisi-jumlah-pg').val('0');
            } else {
                $('#form-group-pg').show();
                $('#form-group-essai').show();
            }
        });

        // Submit Kisi parameter form with inline button loading
        $(document).on('submit', '#kisiForm', function() {
            var submitBtn = $('#btn-submit-kisi-ai');
            submitBtn.prop('disabled', true);
            submitBtn.html('<iconify-icon icon="line-md:loading-twotone-loop" class="align-middle me-1"></iconify-icon> AI Sedang Menyusun Kisi-kisi...');
            
            // Set flag untuk deteksi reload
            localStorage.setItem('ai_generated_just_now', 'true');
            
            // Keep modal open but let form submit natively
            return true;
        });

    });
</script>

<!-- Modal Preview Document Google Drive -->
<div class="modal fade" id="modalPreviewDoc" tabindex="-1" aria-labelledby="previewDocTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light px-24 py-16">
                <h6 class="modal-title fw-semibold text-primary-light" id="previewDocTitle">Pratinjau Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24" id="preview-doc-body">
                <!-- Ajax content loads here -->
            </div>
            <div class="modal-footer border-top bg-light px-24 py-16">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kisi-kisi Parameter Config -->
<div class="modal fade" id="modalKisiConfig" tabindex="-1" aria-labelledby="kisiConfigTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light px-24 py-16">
                <h6 class="modal-title fw-semibold text-primary-light" id="kisiConfigTitle">Konfigurasi AI Kisi-kisi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($generate_berkas_ai_url, ['id' => 'kisiForm']); ?>
            <input type="hidden" name="field" id="kisi-field-input" value="">
            <div class="modal-body p-24">
                
                <div class="mb-16">
                    <label class="form-label text-sm fw-semibold text-secondary-light">Bentuk Soal</label>
                    <select name="bentuk_soal" id="kisi-bentuk-soal" class="form-select radius-8" required>
                        <option value="Pilihan Ganda & Essai">Pilihan Ganda & Essai</option>
                        <option value="Pilihan Ganda">Pilihan Ganda</option>
                        <option value="Essai">Essai</option>
                    </select>
                </div>

                <div class="row gy-3 mb-16">
                    <div class="col-6" id="form-group-pg">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Jumlah Soal PG</label>
                        <input type="number" name="jumlah_pg" id="kisi-jumlah-pg" value="20" min="0" max="100" class="form-control radius-8" required>
                    </div>
                    <div class="col-6" id="form-group-essai">
                        <label class="form-label text-sm fw-semibold text-secondary-light">Jumlah Soal Essai</label>
                        <input type="number" name="jumlah_essai" id="kisi-jumlah-essai" value="5" min="0" max="100" class="form-control radius-8" required>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="form-label text-sm fw-semibold text-secondary-light">Alokasi Waktu (Menit)</label>
                    <input type="number" name="alokasi_waktu" value="90" min="10" max="300" class="form-control radius-8" required>
                </div>

            </div>
            <div class="modal-footer border-top bg-light px-24 py-16">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                <button type="submit" id="btn-submit-kisi-ai" class="btn btn-success radius-8 px-20 py-10">
                    <iconify-icon icon="logos:google-gemini" class="align-middle me-1"></iconify-icon>
                    Generate via AI
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>