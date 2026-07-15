<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>



<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <!-- Main content -->
      <section class="content">

        <!-- Default card -->
        <div class="card">
          <div class="card-header with-border">
            <h3 class="card-title">Edit Role</h3>
          </div>

          <?php echo form_open('roles/update/' . $role->id, ['class' => 'form-validate']); ?>
          <div class="card-body">

            <div class="form-group">
              <label for="formClient-Name"><?php echo lang('role_name') ?></label>
              <input type="text" class="form-control" name="name" id="formClient-Name" required placeholder="<?php echo lang('role_name') ?>" autofocus value="<?php echo $role->title ?>" />
            </div>

            <div class="form-group">
              <label class="fw-bold mb-3"><?php echo lang('permissions') ?></label>
              <style>
                .permission-tree {
                    list-style: none;
                    padding-left: 0;
                }
                .permission-tree ul {
                    list-style: none;
                    padding-left: 28px;
                    border-left: 1px dashed #cbd5e1;
                    margin-top: 8px;
                    margin-bottom: 8px;
                    position: relative;
                }
                .permission-tree li {
                    margin-bottom: 12px;
                    position: relative;
                }
                .permission-tree li::before {
                    content: "";
                    position: absolute;
                    left: -28px;
                    top: 12px;
                    width: 20px;
                    height: 1px;
                    border-top: 1px dashed #cbd5e1;
                }
                .permission-tree > li::before {
                    display: none;
                }
                .level-1-title {
                    font-size: 15px;
                    font-weight: 700;
                    color: #1e293b;
                    background: #f1f5f9;
                    padding: 10px 16px;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    border: 1px solid #e2e8f0;
                }
                .level-2-title {
                    font-size: 14px;
                    font-weight: 600;
                    color: #334155;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    padding: 4px 0;
                }
                .level-3-box {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    padding: 10px 14px;
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    margin-top: 6px;
                    margin-bottom: 6px;
                }
                .level-3-item {
                    font-size: 12px;
                    color: #475569;
                    background: #ffffff;
                    border: 1px solid #cbd5e1;
                    padding: 5px 12px;
                    border-radius: 6px;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    transition: all 0.2s;
                    cursor: pointer;
                }
                .level-3-item:hover {
                    border-color: #94a3b8;
                    background: #f1f5f9;
                }
                .form-check-input {
                    cursor: pointer;
                }
              </style>

              <div class="row">
                <div class="col-md-12">
                  <div class="card border shadow-none mb-3">
                    <div class="card-header bg-danger-50 d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="text-danger-800 mb-0 fw-bold"><i class="ri-node-tree"></i> Konfigurasi Hak Akses Role (Hierarchy Tree)</h6>
                      <div class="d-flex align-items-center gap-3">
                        <button type="button" id="btnSelectAllTree" class="btn btn-xs btn-primary radius-6 px-10 py-4 text-xs">Pilih Semua</button>
                        <button type="button" id="btnDeselectAllTree" class="btn btn-xs btn-outline-secondary radius-6 px-10 py-4 text-xs">Kosongkan</button>
                      </div>
                    </div>
                    <div class="card-body p-24" style="max-height: 700px; overflow-y: auto;">
                      
                      <?php
                      // Definisikan struktur Tree Permission fungsional 3 Level
                      $permission_tree = [
                          [
                              'title' => 'Dashboard Utama',
                              'sub' => [
                                  ['code' => 'menu_dashboard', 'title' => 'Dashboard Admin'],
                                  ['code' => 'menu_dashboard_guru', 'title' => 'Dashboard Guru']
                              ]
                          ],
                          [
                              'title' => 'Kelembagaan & Sarpras',
                              'sub' => [
                                  ['code' => 'menu_lembaga', 'title' => 'Data Lembaga'],
                                  ['code' => 'menu_sarpras', 'title' => 'Data Sarana Prasarana (Sarpras)']
                              ]
                          ],
                          [
                              'title' => 'Kepegawaian (PTK)',
                              'sub' => [
                                  ['code' => 'menu_data_ptk', 'title' => 'Daftar Kepegawaian GTK/PTK'],
                                  ['code' => 'menu_ptk_nonaktif', 'title' => 'PTK Nonaktif'],
                                  ['code' => 'menu_sinkron_dapodik_gtk', 'title' => 'Sinkron Dapodik GTK']
                              ]
                          ],
                          [
                              'title' => 'Kesiswaan & Kedisiplinan',
                              'sub' => [
                                  ['code' => 'menu_kesiswaan_data_siswa', 'title' => 'Data Siswa Utama (Admin/Kesiswaan)'],
                                  ['code' => 'menu_data_siswa_guru', 'title' => 'Data Siswa Rombel (Portal Guru)'],
                                  ['code' => 'menu_sinkron_dapodik', 'title' => 'Sinkron Dapodik Siswa'],
                                  [
                                      'code' => 'menu_kedisiplinan',
                                      'title' => 'Kedisiplinan & BK',
                                      'features' => [
                                          ['code' => 'kedisiplinan_add', 'title' => 'Laporkan Pelanggaran Murid'],
                                          ['code' => 'kedisiplinan_bk', 'title' => 'Tindak Lanjut Konseling BK & Poin'],
                                          ['code' => 'kedisiplinan_delete', 'title' => 'Hapus Laporan Pelanggaran']
                                      ]
                                  ]
                              ]
                          ],
                          [
                              'title' => 'Kurikulum & Pembelajaran',
                              'sub' => [
                                  ['code' => 'menu_pembelajaran_guru', 'title' => 'Pembelajaran Saya (Portal Guru)'],
                                  ['code' => 'menu_perangkat_guru', 'title' => 'Perangkat Mengajar (Portal Guru)'],
                                  ['code' => 'menu_jadwal_guru', 'title' => 'Jadwal Mengajar (Portal Guru)'],
                                  ['code' => 'menu_input_nilai_guru', 'title' => 'Input Nilai Siswa (Portal Guru)'],
                                  ['code' => 'menu_profil_ptk_guru', 'title' => 'Profil PTK (Portal Guru)'],
                                  [
                                      'code' => 'menu_pembelajaran',
                                      'title' => 'Manajemen Pembelajaran Rombel',
                                      'features' => [
                                          ['code' => 'pembelajaran_list', 'title' => 'Melihat Daftar Rombel'],
                                          ['code' => 'pembelajaran_add', 'title' => 'Atur Rombel Baru'],
                                          ['code' => 'pembelajaran_edit', 'title' => 'Ubah Pembelajaran Rombel'],
                                          ['code' => 'pembelajaran_delete', 'title' => 'Hapus Pembelajaran Rombel']
                                      ]
                                  ],
                                  ['code' => 'menu_jadwal_pelajaran', 'title' => 'Jadwal Pelajaran Rombel'],
                                  ['code' => 'menu_jadwal_tidak_aktif', 'title' => 'Jadwal Tidak Aktif'],
                                  ['code' => 'menu_perangkat_pembelajaran', 'title' => 'Perangkat Pembelajaran Rombel'],
                                  ['code' => 'menu_nilai_siswa', 'title' => 'Penilaian Siswa Rombel'],
                                  ['code' => 'menu_tahun_pelajaran', 'title' => 'Tahun Pelajaran & Kalender Akademik'],
                                  [
                                      'code' => 'menu_ekstrakurikuler',
                                      'title' => 'Ekstrakurikuler & Roster',
                                      'features' => [
                                          ['code' => 'ekstrakurikuler_add', 'title' => 'Menambah Ekskul Baru'],
                                          ['code' => 'ekstrakurikuler_edit', 'title' => 'Mengubah Ekskul'],
                                          ['code' => 'ekstrakurikuler_delete', 'title' => 'Menghapus Ekskul'],
                                          ['code' => 'ekstrakurikuler_anggota', 'title' => 'Mengelola Anggota Ekskul'],
                                          ['code' => 'ekstrakurikuler_nilai', 'title' => 'Input Nilai Ekskul']
                                      ]
                                  ]
                              ]
                          ],
                          [
                              'title' => 'Pencetakan & Administrasi Surat',
                              'sub' => [
                                  ['code' => 'menu_surat_menyurat', 'title' => 'Surat Menyurat & Arsip Masuk/Keluar']
                              ]
                          ],
                          [
                              'title' => 'Alumni & Dokumen Sekolah',
                              'sub' => [
                                  ['code' => 'menu_alumni', 'title' => 'Data Alumni Siswa'],
                                  ['code' => 'menu_buku_induk_siswa', 'title' => 'Buku Induk Siswa']
                              ]
                          ],
                          [
                              'title' => 'Master Data Referensi',
                              'sub' => [
                                  ['code' => 'menu_master_lembaga', 'title' => 'Master Lembaga'],
                                  ['code' => 'menu_master_tingkat', 'title' => 'Master Tingkat Sekolah'],
                                  ['code' => 'menu_master_rombel', 'title' => 'Master Rombel'],
                                  ['code' => 'menu_master_rombel_nonaktif', 'title' => 'Master Rombel Nonaktif'],
                                  ['code' => 'menu_master_mapel', 'title' => 'Master Mata Pelajaran'],
                                  ['code' => 'menu_master_sarana', 'title' => 'Master Sarana & Prasarana'],
                                  [
                                      'title' => 'Aksi Master Data Referensi',
                                      'features' => [
                                          ['code' => 'master_list', 'title' => 'Melihat Master'],
                                          ['code' => 'master_add', 'title' => 'Menambah Master'],
                                          ['code' => 'master_edit', 'title' => 'Mengubah Master'],
                                          ['code' => 'master_delete', 'title' => 'Menghapus Master']
                                      ]
                                  ]
                              ]
                          ],
                          [
                              'title' => 'Manajemen Pengguna',
                              'sub' => [
                                  [
                                      'code' => 'menu_users',
                                      'title' => 'Akun Pengguna',
                                      'features' => [
                                          ['code' => 'users_list', 'title' => 'Melihat Akun'],
                                          ['code' => 'users_add', 'title' => 'Tambah Akun'],
                                          ['code' => 'users_edit', 'title' => 'Ubah Akun'],
                                          ['code' => 'users_delete', 'title' => 'Hapus Akun']
                                      ]
                                  ],
                                  [
                                      'code' => 'menu_roles',
                                      'title' => 'Hak Akses Role & Permissions',
                                      'features' => [
                                          ['code' => 'roles_list', 'title' => 'Melihat Role'],
                                          ['code' => 'roles_add', 'title' => 'Tambah Role'],
                                          ['code' => 'roles_edit', 'title' => 'Ubah Role']
                                      ]
                                  ]
                              ]
                          ]
                      ];

                      // Ambil fallback permissions yang belum terpetakan di tree
                      $mapped_codes = [];
                      foreach ($permission_tree as $g) {
                          foreach ($g['sub'] as $s) {
                              if (isset($s['code'])) $mapped_codes[] = $s['code'];
                              if (isset($s['features'])) {
                                  foreach ($s['features'] as $f) {
                                      $mapped_codes[] = $f['code'];
                                  }
                              }
                          }
                      }
                      $all_perms = $this->permissions_model->get();
                      $fallback_perms = [];
                      if (!empty($all_perms)) {
                          foreach ($all_perms as $p) {
                              if (!in_array($p->code, $mapped_codes)) {
                                  $fallback_perms[] = $p;
                              }
                          }
                      }
                      
                      // Tambahkan fallback jika ada
                      if (!empty($fallback_perms)) {
                          $fallback_sub = [];
                          foreach ($fallback_perms as $fp) {
                              $fallback_sub[] = ['code' => $fp->code, 'title' => $fp->title];
                          }
                          $permission_tree[] = [
                              'title' => 'Fitur & Aksi Lainnya (Fallback)',
                              'sub' => $fallback_sub
                          ];
                      }
                      ?>

                      <ul class="permission-tree">
                        <?php foreach ($permission_tree as $gIndex => $group): ?>
                          <!-- LEVEL 1 -->
                          <li class="group-container mb-20">
                            <div class="level-1-title">
                              <span><i class="ri-folder-open-fill text-warning-main"></i> <?php echo $group['title'] ?></span>
                              <div class="form-check m-0">
                                <input type="checkbox" class="form-check-input check-lvl-1" id="group_<?php echo $gIndex ?>">
                                <label for="group_<?php echo $gIndex ?>" class="form-check-label text-xs fw-bold cursor-pointer">Pilih Grup</label>
                              </div>
                            </div>

                            <!-- LEVEL 2 -->
                            <ul>
                              <?php foreach ($group['sub'] as $sIndex => $sub): ?>
                                <li class="sub-container">
                                  <div class="level-2-title">
                                    <?php if (isset($sub['code'])): ?>
                                      <?php $isChecked = in_array($sub['code'], $role_permissions) ? 'checked' : ''; ?>
                                      <span><i class="ri-checkbox-circle-line text-primary"></i> <?php echo $sub['title'] ?></span>
                                      <input type="checkbox" class="form-check-input check-lvl-2" name="permission[]" value="<?php echo $sub['code'] ?>" <?php echo $isChecked ?> id="sub_<?php echo $gIndex ?>_<?php echo $sIndex ?>">
                                    <?php else: ?>
                                      <span><i class="ri-settings-4-line text-secondary"></i> <?php echo $sub['title'] ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <!-- LEVEL 3 -->
                                  <?php if (isset($sub['features'])): ?>
                                    <div class="level-3-box">
                                      <?php foreach ($sub['features'] as $fIndex => $feat): ?>
                                        <?php $isChecked = in_array($feat['code'], $role_permissions) ? 'checked' : ''; ?>
                                        <label class="level-3-item m-0" for="feat_<?php echo $gIndex ?>_<?php echo $sIndex ?>_<?php echo $fIndex ?>">
                                          <input type="checkbox" class="form-check-input check-lvl-3 m-0" name="permission[]" value="<?php echo $feat['code'] ?>" <?php echo $isChecked ?> id="feat_<?php echo $gIndex ?>_<?php echo $sIndex ?>_<?php echo $fIndex ?>">
                                          <span><?php echo $feat['title'] ?></span>
                                        </label>
                                      <?php endforeach; ?>
                                    </div>
                                  <?php endif; ?>

                                </li>
                              <?php endforeach; ?>
                            </ul>

                          </li>
                        <?php endforeach; ?>
                      </ul>

                    </div>
                  </div>
                </div>
              </div>

            </div>

          </div>
          <!-- /.card-body -->


          <div class="card-footer">
            <div class="row">
              <div class="col"><a href="<?php echo url('/roles') ?>" onclick="return confirm('Are you sure you want to leave?')" class="btn btn-flat btn-danger"><?php echo lang('cancel') ?></a></div>
              <div class="col text-right"><button type="submit" class="btn btn-flat btn-primary"><?php echo lang('submit') ?></button></div>
            </div>
          </div>
          <!-- /.card-footer-->

          <?php echo form_close(); ?>

        </div>
        <!-- /.card -->

      </section>
      <!-- /.content -->

    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.form-validate').validate({
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });

    // Klik Level 1 (Grup Utama)
    $(document).on('change', '.check-lvl-1', function() {
      const isChecked = $(this).is(':checked');
      const container = $(this).closest('.group-container');
      container.find('.check-lvl-2, .check-lvl-3').prop('checked', isChecked);
    });

    // Klik Level 2 (Submenu / Menu)
    $(document).on('change', '.check-lvl-2', function() {
      const isChecked = $(this).is(':checked');
      const container = $(this).closest('.sub-container');
      container.find('.check-lvl-3').prop('checked', isChecked);
      
      if (isChecked) {
        $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
      } else {
        // Uncheck Level 1 jika seluruh Level 2 di grup tersebut kosong
        const group = $(this).closest('.group-container');
        if (group.find('.check-lvl-2:checked').length === 0) {
          group.find('.check-lvl-1').prop('checked', false);
        }
      }
    });

    // Klik Level 3 (Fitur / Aksi)
    $(document).on('change', '.check-lvl-3', function() {
      const isChecked = $(this).is(':checked');
      
      if (isChecked) {
        $(this).closest('.sub-container').find('.check-lvl-2').prop('checked', true);
        $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
      } else {
        // Uncheck Level 2 jika seluruh Level 3 di submenu tersebut kosong
        const sub = $(this).closest('.sub-container');
        if (sub.find('.check-lvl-3:checked').length === 0) {
          sub.find('.check-lvl-2').prop('checked', false);
        }
        // Uncheck Level 1 jika seluruh Level 2 di grup tersebut kosong
        const group = $(this).closest('.group-container');
        if (group.find('.check-lvl-2:checked').length === 0) {
          group.find('.check-lvl-1').prop('checked', false);
        }
      }
    });

    // Tombol Pilih Semua
    $('#btnSelectAllTree').on('click', function() {
      $('.check-lvl-1, .check-lvl-2, .check-lvl-3').prop('checked', true);
    });

    // Tombol Kosongkan
    $('#btnDeselectAllTree').on('click', function() {
      $('.check-lvl-1, .check-lvl-2, .check-lvl-3').prop('checked', false);
    });

    // Sinkronisasi status checked awal saat halaman dimuat
    $('.group-container').each(function() {
      const group = $(this);
      // Jika ada anak yang ter-check, check Level 1 di atasnya
      if (group.find('.check-lvl-2:checked, .check-lvl-3:checked').length > 0) {
        group.find('.check-lvl-1').prop('checked', true);
      }
      
      group.find('.sub-container').each(function() {
        const sub = $(this);
        if (sub.find('.check-lvl-3:checked').length > 0) {
          sub.find('.check-lvl-2').prop('checked', true);
          group.find('.check-lvl-1').prop('checked', true);
        }
      });
    });


  })
</script>

<?php include viewPath('includes/footer'); ?>

<script>
  //Initialize Select2 Elements
  $('.select2').select2()
</script>