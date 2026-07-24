<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Dynamic Mobile Bottom Navigation Bar
$CI = &get_instance();
$current_url = current_url();
$role_id = logged('role');
$role_row = $CI->db->get_where('roles', ['id' => $role_id])->row();
$role_title = $role_row ? strtolower((string)$role_row->title) : '';
$is_guru = ($role_title === 'guru' || $role_id == 4);

if ($is_guru) {
    $nav_items = [
        ['title' => 'Beranda', 'url' => url('guru'), 'icon' => 'solar:home-angle-2-linear'],
        ['title' => 'Jadwal', 'url' => url('guru/jadwal'), 'icon' => 'akar-icons:schedule'],
        ['title' => 'Agenda', 'url' => url('guru/agenda'), 'icon' => 'solar:notebook-linear'],
        ['title' => 'Nilai', 'url' => url('guru/nilai'), 'icon' => 'solar:clipboard-list-linear'],
    ];
} else {
    $nav_items = [
        ['title' => 'Beranda', 'url' => url('dashboard'), 'icon' => 'solar:home-angle-2-linear'],
        ['title' => 'Siswa', 'url' => url('siswa'), 'icon' => 'solar:users-group-two-rounded-linear'],
        ['title' => 'Presensi', 'url' => url('presensi/v_siswa'), 'icon' => 'solar:calendar-check-linear'],
        ['title' => 'Jadwal', 'url' => url('jadwal_pelajaran'), 'icon' => 'akar-icons:schedule'],
    ];
}
?>
<!-- Mobile Bottom Navigation Bar -->
<nav class="mobile-bottom-nav">
    <ul class="mobile-bottom-nav-items">
        <?php foreach ($nav_items as $item): 
            $is_active = (rtrim($current_url, '/') == rtrim($item['url'], '/'));
        ?>
            <li class="mobile-bottom-nav-item">
                <a href="<?php echo $item['url']; ?>" class="mobile-bottom-nav-link <?php echo $is_active ? 'active' : ''; ?>">
                    <iconify-icon icon="<?php echo $item['icon']; ?>"></iconify-icon>
                    <span><?php echo $item['title']; ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <li class="mobile-bottom-nav-item">
            <a href="javascript:void(0)" class="mobile-bottom-nav-link sidebar-mobile-toggle">
                <iconify-icon icon="heroicons:bars-3-solid"></iconify-icon>
                <span>Menu</span>
            </a>
        </li>
    </ul>
</nav>

<footer class="d-footer">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <p class="mb-0">© 2025 Miftahul Khoer Boarding School. All Rights Reserved.</p>
        </div>
        <div class="col-auto">
            <p class="mb-0 d-flex gap-3 align-items-center">
                <a href="<?php echo url('policies/privacy_policy') ?>" class="text-secondary-light text-xs hover-text-primary">Kebijakan Privasi</a>
                <span class="text-neutral-300">|</span>
                <a href="<?php echo url('policies/terms_of_service') ?>" class="text-secondary-light text-xs hover-text-primary">Syarat Layanan</a>
                <span class="text-neutral-300">|</span>
                <span>Made by <span class="text-primary-600">Zakaria Zulkarnain</span></span>
            </p>
        </div>
    </div>
</footer>
</main>
<!-- jQuery library js -->
<script src="<?php echo $url->assets ?>js/lib/jquery-3.7.1.min.js"></script>
<!-- Bootstrap js -->
<script src="<?php echo $url->assets ?>js/lib/bootstrap.bundle.min.js"></script>
<!-- Apex Chart js -->
<script src="<?php echo $url->assets ?>js/lib/apexcharts.min.js"></script>
<!-- Data Table js -->
<script src="<?php echo $url->assets ?>js/lib/dataTables.min.js"></script>
<!-- Iconify Font js -->
<script src="<?php echo $url->assets ?>js/lib/iconify-icon.min.js"></script>
<!-- jQuery UI js -->
<script src="<?php echo $url->assets ?>js/lib/jquery-ui.min.js"></script>
<!-- Vector Map js -->
<script src="<?php echo $url->assets ?>js/lib/jquery-jvectormap-2.0.5.min.js"></script>
<script src="<?php echo $url->assets ?>js/lib/jquery-jvectormap-world-mill-en.js"></script>
<!-- Popup js -->
<script src="<?php echo $url->assets ?>js/lib/magnifc-popup.min.js"></script>
<!-- Slick Slider js -->
<script src="<?php echo $url->assets ?>js/lib/slick.min.js"></script>
<!-- prism js -->
<script src="<?php echo $url->assets ?>js/lib/prism.js"></script>
<!-- file upload js -->
<script src="<?php echo $url->assets ?>js/lib/file-upload.js"></script>
<!-- audioplayer -->
<script src="<?php echo $url->assets ?>js/lib/audioplayer.js"></script>

<!-- main js -->
<script src="<?php echo $url->assets ?>js/app.js"></script>

<script src=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.2/dist/sweetalert2.all.min.js "></script>

<!-- scanner plugin -->
<script src="<?php echo $url->assets ?>js/scanner-plugin.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const myAlert = document.getElementById('myAlert');
        if (myAlert) {
            setTimeout(function() {
                const alertInstance = bootstrap.Alert.getInstance(myAlert); // Get the Bootstrap Alert instance
                if (alertInstance) {
                    alertInstance.close(); // Close the alert
                } else {
                    // If no instance exists (e.g., alert already dismissed manually), create one and close
                    new bootstrap.Alert(myAlert).close();
                }
            }, 7000); // 3000 milliseconds = 3 seconds
        }
    });

    // Toggle fade in/out
    $('#myAlert').fadeToggle(8000); // 500 milliseconds duration

    // Auto scroll ke menu sidebar yang aktif
    $(document).ready(function() {
        setTimeout(function() {
            // Mencari elemen menu yang memiliki class 'active' atau 'show' (umum digunakan di template ini)
            var activeMenu = $('.sidebar-menu .active, .sidebar-menu .show, .sidebar-menu .sidebar-menu-item.active').last();
            if (activeMenu.length) {
                activeMenu[0].scrollIntoView({
                    behavior: 'auto',
                    block: 'center'
                });
            }
        }, 300); // Menggunakan sedikit timeout agar script inisialisasi sidebar di app.js selesai berjalan lebih dulu
    });
</script>
</body>

</html>