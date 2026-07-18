<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-dark mb-0">Data Mata Pelajaran</h6>
                    </div>
                    <a href="<?php echo url('master/mapelTambah'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon> Tambah Mapel
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">Geser</th>
                                    <th>No</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Singkatan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($mapel as $m): ?>
                                    <tr data-id="<?php echo $m->id_mapel; ?>">
                                        <td class="text-center drag-handle" style="cursor: move;">
                                            <iconify-icon icon="lucide:grip-vertical" class="text-xl text-neutral-500"></iconify-icon>
                                        </td>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $m->nama_mapel; ?></td>
                                        <td><?php echo $m->mapel_singkat ?: '-'; ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $m->status == 'Aktif' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600'; ?>">
                                                <?php echo $m->status; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('master/mapelEdit/' . $m->id_mapel); ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Sunting">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/mapelDelete/' . $m->id_mapel); ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                                                </a>
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
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    let table = new DataTable('#dataTable', {
        paging: false,
        ordering: false,
        searching: true,
        info: false
    });
    
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Custom Toast Notification
    function showToast(message, type = 'success') {
        // Hapus toast sebelumnya jika ada
        const oldToast = document.getElementById('sort-toast');
        if (oldToast) oldToast.remove();

        const toast = document.createElement('div');
        toast.id = 'sort-toast';
        
        let bgClass = 'bg-success-600';
        let icon = 'lucide:check-circle';
        if (type === 'danger') {
            bgClass = 'bg-danger-600';
            icon = 'lucide:alert-circle';
        } else if (type === 'info') {
            bgClass = 'bg-info-600';
            icon = 'lucide:info';
        }

        toast.className = `position-fixed bottom-0 end-0 m-24 p-16 ${bgClass} text-white radius-8 shadow-lg d-flex align-items-center gap-2`;
        toast.style.zIndex = '9999';
        toast.style.transition = 'opacity 0.3s ease-in-out';
        toast.innerHTML = `
            <iconify-icon icon="${icon}" class="text-xl"></iconify-icon>
            <span class="text-sm fw-medium">${message}</span>
        `;
        document.body.appendChild(toast);
        
        if (type !== 'info') {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }
    }

    // Sortable JS Integration
    const tbody = document.querySelector('#dataTable tbody');
    new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-neutral-100',
        onEnd: function() {
            const order = [];
            tbody.querySelectorAll('tr').forEach((tr) => {
                const id = tr.getAttribute('data-id');
                if (id) {
                    order.push(id);
                }
            });

            showToast('Menyimpan urutan mata pelajaran...', 'info');

            fetch('<?php echo url("master/mapelUrutanUpdate"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    
                    // Rerender row numbers dynamically
                    let indexNo = 1;
                    tbody.querySelectorAll('tr').forEach((tr) => {
                        const noTd = tr.querySelector('td:nth-child(2)');
                        if (noTd) {
                            noTd.textContent = indexNo++;
                        }
                    });
                } else {
                    showToast(data.message || 'Gagal menyimpan urutan.', 'danger');
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan koneksi ke server.', 'danger');
            });
        }
    });
</script>