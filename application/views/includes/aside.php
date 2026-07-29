<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a  class="sidebar-logo shadow">
            <img src="<?php echo $url->assets ?>images/logodc.png" alt="site logo" class="light-logo">
            <img src="<?php echo $url->assets ?>images/logodc.png" alt="site logo" class="dark-logo">
            <img src="<?php echo $url->assets ?>images/logo-icon.png" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <?php
            $CI = &get_instance();
            $userId = logged('id');
            $user_roles_aside = [];
            if ($userId && $CI->db->table_exists('user_roles')) {
                foreach ($CI->db->get_where('user_roles', ['user_id' => $userId])->result() as $ur) {
                    $r_row = $CI->db->get_where('roles', ['id' => $ur->role_id])->row();
                    if ($r_row) {
                        $user_roles_aside[] = strtolower((string) $r_row->title);
                    }
                }
            }
            if (empty($user_roles_aside)) {
                $role_title_row = $CI->db->get_where('roles', ['id' => logged('role')])->row();
                if ($role_title_row) {
                    $user_roles_aside[] = strtolower((string) $role_title_row->title);
                }
            }

            $is_admin_aside = false;
            foreach ($user_roles_aside as $r) {
                $r_clean = trim(strtolower((string) $r));
                if ($r_clean === 'admin' || $r_clean === 'administrator' || $r_clean === 'superadmin' || strpos($r_clean, 'admin') !== false) {
                    $is_admin_aside = true;
                    break;
                }
            }

            $is_guru_portal = !$is_admin_aside || in_array('guru', $user_roles_aside, true) || in_array('guru bk', $user_roles_aside, true) || in_array('bk', $user_roles_aside, true) || in_array('wakasek', $user_roles_aside, true) || logged('role') == 4;
            ?>
            <?php if ($is_guru_portal): ?>
                <?php if (hasPermissions('menu_dashboard_guru')): ?>
                    <li>
                        <a href="<?php echo url('guru') ?>">
                            <iconify-icon icon="solar:home-angle-2-linear" class="menu-icon"></iconify-icon>
                            <span>Dashboard Guru</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_data_siswa_guru') || hasPermissions('menu_pembelajaran_guru') || hasPermissions('menu_perangkat_guru') || hasPermissions('menu_jadwal_guru') || hasPermissions('menu_input_nilai_guru') || hasPermissions('menu_profil_ptk_guru') || hasPermissions('kedisiplinan_add') || hasPermissions('menu_kedisiplinan')): ?>
                    <li class="sidebar-menu-group-title">Portal Guru</li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_profil_ptk_guru')): ?>
                    <li>
                        <a href="<?php echo url('profile') ?>">
                            <iconify-icon icon="icon-park-outline:user-business" class="menu-icon"></iconify-icon>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_data_siswa_guru')): ?>
                    <li>
                        <a href="<?php echo url('guru/siswa') ?>">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" class="menu-icon"></iconify-icon>
                            <span>Data Siswa</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_perangkat_guru')): ?>
                    <li>
                        <a href="<?php echo url('guru/perangkat') ?>">
                            <iconify-icon icon="solar:document-add-linear" class="menu-icon"></iconify-icon>
                            <span>Perangkat Pembelajaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('guru/agenda') ?>">
                            <iconify-icon icon="solar:notebook-linear" class="menu-icon"></iconify-icon>
                            <span>Agenda Pembelajaran Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('guru/rekap_absensi_agenda') ?>">
                            <iconify-icon icon="solar:chart-square-linear" class="menu-icon"></iconify-icon>
                            <span>Rekap Absensi Agenda</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_jadwal_guru')): ?>
                    <li>
                        <a href="<?php echo url('guru/jadwal') ?>">
                            <iconify-icon icon="akar-icons:schedule" class="menu-icon"></iconify-icon>
                            <span>Jadwal Saya</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('menu_input_nilai_guru')): ?>
                    <li>
                        <a href="<?php echo url('guru/nilai') ?>">
                            <iconify-icon icon="solar:clipboard-list-linear" class="menu-icon"></iconify-icon>
                            <span>Input Nilai</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermissions('kedisiplinan_add') || hasPermissions('menu_kedisiplinan')): ?>
                    <li>
                        <a href="<?php echo url('kedisiplinan/tambah') ?>" class="text-danger">
                            <iconify-icon icon="solar:shield-warning-bold" class="menu-icon text-danger"></iconify-icon>
                            <span class="text-danger fw-semibold">Laporkan Kenakalan</span>
                        </a>
                    </li>
                <?php endif; ?>

            <?php endif; ?>

<?php if ($is_admin_aside && hasPermissions('menu_dashboard')): ?>
    <li>
        <a href="<?php echo url('') ?>">
            <iconify-icon icon="solar:home-angle-2-linear" class="menu-icon"></iconify-icon>
            <span>Dashboard Admin</span>
        </a>
    </li>
<?php endif; ?>

<?php if ((hasPermissions('menu_calon_siswa') || hasPermissions('menu_validasi_daftar_ulang') || hasPermissions('menu_aktivasi_calon_siswa')) && is_daftar_ulang_aktif()): ?>
    <li class="sidebar-menu-group-title">Daftar Ulang</li>

    <?php if (hasPermissions('menu_calon_siswa')): ?>
        <li>
            <a href="<?php echo url('calon_siswa') ?>">
                <iconify-icon icon="solar:user-plus-linear" class="menu-icon"></iconify-icon>
                <span>Calon Siswa</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_validasi_daftar_ulang')): ?>
        <li>
            <a href="<?php echo url('calon_siswa/validasi') ?>">
                <iconify-icon icon="lucide:check-check" class="menu-icon"></iconify-icon>
                <span>Validasi Daftar Ulang</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_aktivasi_calon_siswa')): ?>
        <li>
            <a href="<?php echo url('calon_siswa/aktivasi') ?>">
                <iconify-icon icon="lucide:user-check" class="menu-icon"></iconify-icon>
                <span>Aktivasi Calon Siswa</span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('menu_lembaga') || hasPermissions('menu_sarpras')): ?>
    <li class="sidebar-menu-group-title" style="background: rgb(205, 204, 252);
        background: linear-gradient(90deg, rgb(245, 255, 154) 0%, rgba(255, 255, 255, 0) 100%);">Kelembagaan</li>

    <?php if (hasPermissions('menu_lembaga')): ?>
        <li>
            <a href="<?php echo url('lembaga') ?>">
                <iconify-icon icon="solar:home-linear" class="menu-icon"></iconify-icon>
                <span>Lembaga</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_sarpras')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="hugeicons:maps-square-01" class="menu-icon"></iconify-icon>
                <span>Sarpras</span>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('sarpras/tanah') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Tanah
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('sarpras/bangunan') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Bangunan/Gedung
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('sarpras/ruangan') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Ruangan
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('sarpras/alat') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Alat, Buku & Kendaraan
                    </a>
                </li>
            </ul>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('menu_data_ptk') || hasPermissions('menu_ptk_nonaktif') || hasPermissions('menu_sinkron_dapodik_gtk') || hasPermissions('menu_generate_niy')): ?>
    <li
        class="sidebar-menu-group-title"
        style="background: #bdd3b1;
                background: linear-gradient(90deg, rgb(223, 252, 206) 0%, rgba(255, 255, 255, 0) 100%);">
        Kepegawaian
    </li>
    <?php if (hasPermissions('menu_data_ptk')): ?>
        <li>
            <a href="<?php echo url('ptk/ptk') ?>">
                <iconify-icon icon="icon-park-outline:user-business" class="menu-icon"></iconify-icon>
                <span>Data PTK</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_generate_niy')): ?>
        <li>
            <a href="<?php echo url('generate_niy') ?>">
                <iconify-icon icon="solar:user-id-linear" class="menu-icon"></iconify-icon>
                <span>Generate NIY</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_ptk_nonaktif')): ?>
        <li>
            <a href="<?php echo url('ptk/ptkNonaktif') ?>">
                <iconify-icon icon="icon-park-outline:wrong-user" class="menu-icon"></iconify-icon>
                <span>Data PTK Nonaktif</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_sinkron_dapodik_gtk')): ?>
        <li>
            <a href="<?php echo url('sync_dapodik_ptk') ?>">
                <iconify-icon icon="lucide:refresh-cw" class="menu-icon"></iconify-icon>
                <span>Sinkron Dapodik GTK</span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>



<?php if (hasPermissions('menu_kesiswaan_data_siswa') || hasPermissions('menu_sinkron_dapodik') || hasPermissions('menu_generate_nipd')): ?>
    <li class="sidebar-menu-group-title"
        style="background: rgb(205, 204, 252);
        background: linear-gradient(90deg, rgb(207, 219, 255) 0%, rgba(255, 255, 255, 0) 100%);">Kesiswaan
    </li>

    <?php if (hasPermissions('menu_kesiswaan_data_siswa')): ?>
        <li>
            <a href="<?php echo url('siswa/all') ?>">
                <iconify-icon icon="icon-park-outline:every-user" class="menu-icon"></iconify-icon>
                <span>Data Siswa (Semua)</span>
            </a>
        </li>
        <?php
        $CI = &get_instance();
        $CI->db->select('p.id_pembelajaran, l.nama_lembaga, l.nama_lembaga_singkat, l.bentuk_pendidikan, t.nama_tingkat, t.tingkat_angka, r.nama_rombel');
        $CI->db->from('pembelajaran p');
        $CI->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $CI->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $CI->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $CI->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $CI->db->where('tp.status', 'Aktif');
        $CI->db->where('p.status', 'Aktif');
        $CI->db->order_by('l.nama_lembaga', 'ASC');
        $CI->db->order_by('t.tingkat_angka', 'ASC');
        $CI->db->order_by('r.nama_rombel', 'ASC');
        $menu_pembelajaran_siswa = $CI->db->get()->result();

        $menu_siswa_lembaga = [];
        foreach ($menu_pembelajaran_siswa as $menu_row) {
            $nama_singkat = !empty($menu_row->nama_lembaga_singkat) 
                ? $menu_row->nama_lembaga_singkat 
                : (!empty($menu_row->bentuk_pendidikan) ? $menu_row->bentuk_pendidikan : $menu_row->nama_lembaga);
            $menu_siswa_lembaga[$nama_singkat][] = $menu_row;
        }
        ?>
        <?php foreach ($menu_siswa_lembaga as $nama_lembaga_singkat => $menu_rows): ?>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="lucide:users" class="menu-icon"></iconify-icon>
                    <span>Data Siswa <?php echo html_escape($nama_lembaga_singkat) ?></span>
                </a>
                <ul class="sidebar-submenu">
                    <?php foreach ($menu_rows as $menu_row): ?>
                        <li>
                            <a href="<?php echo url('siswa/pembelajaran/' . $menu_row->id_pembelajaran) ?>">
                                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> <?php echo html_escape($menu_row->nama_tingkat . ' - ' . $menu_row->nama_rombel) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (hasPermissions('menu_generate_nipd')): ?>
        <li>
            <a href="<?php echo url('generate_nipd') ?>">
                <iconify-icon icon="solar:user-id-linear" class="menu-icon"></iconify-icon>
                <span>Generate NIPD</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_sinkron_dapodik')): ?>
        <li>
            <a href="<?php echo url('sync_dapodik') ?>">
                <iconify-icon icon="lucide:refresh-cw" class="menu-icon"></iconify-icon>
                <span>Sinkron Dapodik</span>
            </a>
        </li>
    <?php endif; ?>

<?php endif; ?>

<?php if (hasPermissions('menu_kedisiplinan')): ?>
    <li>
        <a href="<?php echo url('kedisiplinan') ?>">
            <iconify-icon icon="solar:shield-warning-linear" class="menu-icon"></iconify-icon>
            <span>Kedisiplinan & BK</span>
        </a>
    </li>
<?php endif; ?>

<?php if (hasPermissions('menu_kesiswaan_data_siswa')): ?>
        <li>
            <a href="<?php echo url('siswa/nonaktif') ?>">
                <iconify-icon icon="lucide:user-x" class="menu-icon"></iconify-icon>
                <span>Data Siswa Tidak Aktif</span>
            </a>
        </li>

    <!-- <li>
        <a href="<?php echo url('siswa/kelulusan') ?>">
            <iconify-icon icon="lucide:graduation-cap" class="menu-icon"></iconify-icon>
            <span>Kelulusan Kolektif</span>
        </a>
    </li> -->

<?php endif; ?>

<?php if (hasPermissions('pembelajaran_list') || hasPermissions('menu_tugas_tambahan_ptk') || hasPermissions('menu_jadwal_pelajaran') || hasPermissions('menu_jadwal_tidak_aktif') || hasPermissions('menu_perangkat_pembelajaran') || hasPermissions('menu_nilai_siswa') || hasPermissions('menu_tahun_pelajaran') || hasPermissions('menu_ekstrakurikuler')): ?>
    <li class="sidebar-menu-group-title"
        style="background: #d3c5b1;
        background: linear-gradient(90deg, rgb(255, 233, 135) 0%, rgba(255, 255, 255, 0) 100%);">
        Pembelajaran
    </li>

    <?php if (hasPermissions('pembelajaran_list')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="solar:book-bookmark-linear" class="menu-icon"></iconify-icon>
                <span>Data Pembelajaran</span>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('pembelajaran') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Daftar Kelas
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('pembelajaran/nonaktif') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Kelas Tidak Aktif
                    </a>
                </li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_tugas_tambahan_ptk')): ?>
        <li>
            <a href="<?php echo url('tugas_tambahan_ptk') ?>">
                <iconify-icon icon="solar:user-id-linear" class="menu-icon"></iconify-icon>
                <span>Tugas Tambahan PTK</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_jadwal_pelajaran') || hasPermissions('menu_jadwal_tidak_aktif')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="akar-icons:schedule" class="menu-icon"></iconify-icon>
                <span>Jadwal Pelajaran</span>
            </a>
            <ul class="sidebar-submenu">
                <?php if (hasPermissions('menu_jadwal_pelajaran')): ?>
                    <li>
                        <a href="<?php echo url('jadwal_pelajaran') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Daftar Jadwal
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (hasPermissions('menu_jadwal_tidak_aktif')): ?>
                    <li>
                        <a href="<?php echo url('jadwal_pelajaran/nonaktif') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Jadwal Tidak Aktif
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_perangkat_pembelajaran')): ?>
        <li>
            <a href="<?php echo url('perangkat_pembelajaran') ?>">
                <iconify-icon icon="solar:document-add-linear" class="menu-icon"></iconify-icon>
                <span>Perangkat Pembelajaran</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_nilai_siswa')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="solar:clipboard-list-linear" class="menu-icon"></iconify-icon>
                <span>Nilai Siswa</span>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('nilai_siswa') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Input Nilai
                    </a>
                </li>

                <li>
                    <a href="<?php echo url('nilai_siswa/setting/0') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Setting Persentase
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('nilai_siswa/nonaktif') ?>">
                        <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                        Data Tidak Aktif
                    </a>
                </li>
            </ul>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_tahun_pelajaran')): ?>
        <li>
            <a href="<?php echo url('tahun_pelajaran') ?>">
                <iconify-icon icon="material-symbols:punch-clock-outline-sharp" class="menu-icon"></iconify-icon>
                <span>Tahun Pelajaran</span>
            </a>
        </li>
    <?php endif; ?>
    
    <?php if (hasPermissions('menu_ekstrakurikuler')): ?>
        <li>
            <a href="<?php echo url('ekstrakurikuler') ?>">
                <iconify-icon icon="solar:dialog-linear" class="menu-icon"></iconify-icon>
                <span>Ekstrakurikuler</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_kelas_jauh')): ?>
        <li>
            <a href="<?php echo url('kelas_jauh') ?>">
                <iconify-icon icon="solar:globus-linear" class="menu-icon"></iconify-icon>
                <span>Kelas Jauh (Menginduk)</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (logged('role') == '1' || logged('role') == 'admin'): ?>
        <li>
            <a href="<?php echo url('tutup_semester') ?>">
                <iconify-icon icon="solar:lock-keyhole-bold" class="menu-icon text-danger-main"></iconify-icon>
                <span class="text-danger-main fw-semibold">Penutupan Semester</span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('menu_surat_menyurat') || hasPermissions('menu_pencetakan')): ?>
    <li class="sidebar-menu-group-title"
        style="background: #bdd3b1;
        background: linear-gradient(90deg, rgb(232, 191, 255) 0%, rgba(255, 255, 255, 0) 100%);">Pencetakan & Surat
    </li>

    <?php if (hasPermissions('menu_surat_menyurat')): ?>
    <li class="dropdown">
        <a href="javascript:void(0)">
            <iconify-icon icon="solar:letter-linear" class="menu-icon"></iconify-icon>
            <span>Surat Menyurat</span>
        </a>
        <ul class="sidebar-submenu">
            <li>
                <a href="<?php echo url('surat/masuk') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Surat Masuk
                </a>
            </li>
            <li>
                <a href="<?php echo url('surat/keluar') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Surat Keluar
                </a>
            </li>
            <li>
                <a href="<?php echo url('surat/kode') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Kode Surat
                </a>
            </li>
            <li>
                <a href="<?php echo url('surat/template') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Template Surat
                </a>
            </li>
            <li>
                <a href="<?php echo url('surat/kop') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Kop Surat
                </a>
            </li>
        </ul>
    </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_pencetakan')): ?>
    <li class="dropdown">
        <a href="javascript:void(0)">
            <iconify-icon icon="solar:printer-linear" class="menu-icon"></iconify-icon>
            <span>Pencetakan</span>
        </a>
        <ul class="sidebar-submenu">
            <?php if (hasPermissions('pencetakan_absensi')): ?>
            <li>
                <a href="<?php echo url('pencetakan/absensi') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Cetak Absensi
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('menu_presensi')): ?>
    <li class="sidebar-menu-group-title"
        style="background: #bdd3b1;
        background: linear-gradient(90deg, rgb(253, 206, 154) 0%, rgba(255, 255, 255, 0) 100%);">Presensi</li>
    <li class="dropdown">
        <a href="javascript:void(0)">
            <iconify-icon icon="solar:user-speak-linear" class="menu-icon"></iconify-icon>
            <span>Presensi & Kehadiran</span>
        </a>
        <ul class="sidebar-submenu">
            <li>
                <a href="<?php echo url('presensi/siswa') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Presensi Siswa
                </a>
            </li>
            <li>
                <a href="<?php echo url('presensi/guru') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    Presensi PTK
                </a>
            </li>
            <li>
                <a href="<?php echo url('presensi/user_fingerprint') ?>">
                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                    User Mesin & Sidik Jari
                </a>
            </li>
        </ul>
    </li>
<?php endif; ?>



<?php if (hasPermissions('menu_alumni') || hasPermissions('menu_buku_induk_siswa') || hasPermissions('menu_kesiswaan_data_siswa')): ?>
    <li class="sidebar-menu-group-title"
        style="background: #bdd3b1;
        background: linear-gradient(90deg, rgb(253, 200, 200) 0%, rgba(255, 255, 255, 0) 100%);">Alumni</li>

    <?php if (hasPermissions('menu_alumni')): ?>
        <li>
            <a href="<?php echo url('alumni') ?>">
                <iconify-icon icon="solar:archive-linear" class="menu-icon"></iconify-icon>
                <span>Data Alumni</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_buku_induk_siswa')): ?>
        <li>
            <a href="<?php echo url('buku_induk_siswa') ?>">
                <iconify-icon icon="solar:book-bookmark-linear" class="menu-icon"></iconify-icon>
                <span>Buku Induk Siswa</span>
            </a>
        </li>
    <?php endif; ?>


<?php endif; ?>

<?php if (hasPermissions('menu_master_lembaga') || hasPermissions('menu_master_tingkat') || hasPermissions('menu_master_rombel') || hasPermissions('menu_master_rombel_nonaktif') || hasPermissions('menu_master_mapel') || hasPermissions('menu_master_sarana') || hasPermissions('menu_master_tugas_tambahan')): ?>
    <li class="sidebar-menu-group-title">Master Data</li>

    <?php if (hasPermissions('menu_master_lembaga')): ?>
        <li>
            <a href="<?php echo url('master/lembaga') ?>">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Lembaga</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_master_tingkat')): ?>
        <li class="sidebar-menu-item">
            <a href="<?php echo url('master/tingkatSekolah') ?>" class="sidebar-menu-link">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Tingkat</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_master_rombel')): ?>
        <li class="sidebar-menu-item">
            <a href="<?php echo url('master/rombel') ?>" class="sidebar-menu-link">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Rombel</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_master_rombel_nonaktif')): ?>
        <li class="sidebar-menu-item">
            <a href="<?php echo url('master/rombelNonaktif') ?>" class="sidebar-menu-link">
                <iconify-icon icon="material-symbols:archive-outline" class="menu-icon"></iconify-icon>
                <span>Master Rombel Nonaktif</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_master_mapel')): ?>
        <li>
            <a href="<?php echo url('master/mapel') ?>">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Mata Pelajaran</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_master_sarana')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Sarana</span>
            </a>

            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('master/jenisRuangan') ?>">
                        <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Jenis Ruangan
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('master/jenisSarana') ?>">
                        <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Jenis Sarana
                    </a>
                </li>
                <li>
                    <a href="<?php echo url('master/standarSarana') ?>">
                        <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Standar Sarana
                    </a>
                </li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_master_tugas_tambahan')): ?>
        <li>
            <a href="<?php echo url('master_tugas_tambahan') ?>">
                <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                <span>Master Tugas Tambahan</span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>


<?php if (hasPermissions('menu_users') || hasPermissions('menu_roles')): ?>
    <li class="sidebar-menu-group-title">Manajeman Akun</li>
    <?php if (hasPermissions('menu_users')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="clarity:users-solid" class="menu-icon"></iconify-icon>
                <span>Akun</span>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('users') ?>"><i class="ri-circle-fill circle-icon text-default-600 w-auto"></i>
                        Daftar Akun</a>
                </li>
                <li>
                    <a href="<?php echo url('users/add') ?>"><i class="ri-circle-fill circle-icon text-default-main w-auto"></i>
                        Tambah Akun</a>
                </li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if (hasPermissions('menu_roles')): ?>
        <li class="dropdown">
            <a href="javascript:void(0)">
                <iconify-icon icon="simple-icons:openaccess" class="menu-icon"></iconify-icon>
                <span>Hak Akses</span>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a href="<?php echo url('roles') ?>"><i class="ri-circle-fill circle-icon text-default-600 w-auto"></i>
                        Roles</a>
                </li>
                <li>
                    <a href="<?php echo url('permissions') ?>"><i class="ri-circle-fill circle-icon text-default-main w-auto"></i>
                        Permission</a>
                </li>
            </ul>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('menu_alat_khusus') || hasPermissions('menu_verifikasi_dokumen_siswa')): ?>
    <li class="sidebar-menu-group-title"
        style="background: #bdd3b1;
               background: linear-gradient(90deg, rgb(255, 220, 185) 0%, rgba(255, 255, 255, 0) 100%);">
        Alat Khusus
    </li>
    <?php if (hasPermissions('menu_verifikasi_dokumen_siswa')): ?>
        <li>
            <a href="<?php echo url('siswa/verifikasiDokumen') ?>">
                <iconify-icon icon="solar:checklist-minimalistic-bold" class="menu-icon"></iconify-icon>
                <span>Cek Berkas Siswa</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_edit_inline_ptk')): ?>
        <li>
            <a href="<?php echo url('edit_inline_ptk') ?>">
                <iconify-icon icon="solar:pen-new-square-linear" class="menu-icon"></iconify-icon>
                <span>Edit Inline PTK</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_edit_inline_siswa')): ?>
        <li>
            <a href="<?php echo url('edit_inline_siswa') ?>">
                <iconify-icon icon="solar:pen-new-square-linear" class="menu-icon"></iconify-icon>
                <span>Edit Inline Siswa</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('input_rekam_medis') ?>">
                <iconify-icon icon="solar:heart-pulse-linear" class="menu-icon"></iconify-icon>
                <span>Input Rekam Medis</span>
            </a>
        </li>
    <?php endif; ?>
   
    <?php if (hasPermissions('menu_export_siswa')): ?>
        <li>
            <a href="<?php echo url('export_siswa') ?>">
                <iconify-icon icon="solar:download-linear" class="menu-icon"></iconify-icon>
                <span>Export Siswa massal</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if (hasPermissions('menu_export_ptk')): ?>
        <li>
            <a href="<?php echo url('export_ptk') ?>">
                <iconify-icon icon="solar:download-linear" class="menu-icon"></iconify-icon>
                <span>Export PTK massal</span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (hasPermissions('general_settings') || hasPermissions('company_settings') || hasPermissions('email_templates') || hasPermissions('activity_log_list') || logged('role') == 1): ?>
    <li class="sidebar-menu-group-title">Pengaturan</li>
    <?php if (hasPermissions('general_settings') || hasPermissions('company_settings')): ?>
    <li>
        <a href="<?php echo url('settings/general') ?>">
            <iconify-icon icon="solar:settings-linear" class="menu-icon"></iconify-icon>
            <span>Pengaturan</span>
        </a>
    </li>
    <?php endif; ?>
    <?php if (hasPermissions('activity_log_list') || logged('role') == 1): ?>
    <li>
        <a href="<?php echo url('activity_logs') ?>">
            <iconify-icon icon="solar:history-bold-duotone" class="menu-icon text-primary"></iconify-icon>
            <span>Log Aktivitas</span>
        </a>
    </li>
    <?php endif; ?>
<?php endif; ?>

<li class="sidebar-menu-group-title">Aplikasi Tambahan</li>
<li>
    <a href="<?php echo $url->assets ?>downloads/mkdc_scanner_bridge.zip" download class="d-flex align-items-center gap-2">
        <iconify-icon icon="lucide:download" class="menu-icon text-primary"></iconify-icon>
        <span>Download Scanner Bridge</span>
    </a>
</li>
<li>
    <a href="<?php echo url('update_log') ?>">
        <iconify-icon icon="solar:history-linear" class="menu-icon text-primary"></iconify-icon>
        <span>Update Log</span>
    </a>
</li>
</ul>
</li>
</ul>
</div>
</aside>
<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            const activeMenu = document.querySelector('.sidebar-menu .active-page');
            if (activeMenu) {
                activeMenu.scrollIntoView({
                    behavior: 'auto',
                    block: 'center'
                });
            }
        }, 200);
    });
</script>