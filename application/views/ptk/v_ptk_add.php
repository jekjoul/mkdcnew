<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-success">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light">Formulir Tambah PTK</h6>
                    </div>

                </div>
                <div class="card-body">

                    <!-- Upload Image End -->
                    <form action="<?php echo url('ptk/ptkSimpan') ?>" method="post" id="formTambahPtk">
                        <div class="row">

                            <div class="col-sm-4">
                                <div class="mb-20">
                                    <label for="nama_ptk"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="nama_ptk" name="nama_ptk" required
                                        placeholder="Masukan nama lengkap tanpa gelar">
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="mb-20">
                                    <label for="pin_fingerprint"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">PIN Sidik Jari</label>
                                    <input type="number" class="form-control radius-8" id="pin_fingerprint" name="pin_fingerprint"
                                        placeholder="PIN">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="jenis_kelamin"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Kelamin
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option>Laki-laki</option>
                                        <option>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="gelar_depan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Depan</label>
                                    <input type="text" class="form-control radius-8" id="gelar_depan" name="gelar_depan"
                                        placeholder="Masukan gelar depan, Contoh : Dr. / Drs. / Ir. / Prof.">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="gelar_belakang"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Belakang</label>
                                    <input type="text" class="form-control radius-8" id="gelar_belakang" name="gelar_belakang"
                                        placeholder="Masukan gelar depan, Contoh : S.Pd. / M.Pd. / M.Si.">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="tempat_lahir"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tempat Lahir
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="tempat_lahir" name="tempat_lahir" required
                                        placeholder="Masukan tempat lahir">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="tanggal_lahir"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lahir
                                        <span class="text-danger-600">*</span></label>
                                    <input type="date" class="form-control radius-8" id="tanggal_lahir" name="tanggal_lahir" required>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="agama"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Agama
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="agama" name="agama">
                                        <option>Islam</option>
                                        <option>Katolik</option>
                                        <option>Protestan</option>
                                        <option>Hindu</option>
                                        <option>Budha</option>
                                        <option>Konghuchu</option>
                                        <option>Kepercayaan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="status_perkawinan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status Perkawinan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="status_perkawinan" name="status_perkawinan">
                                        <option>Belum Kawin</option>
                                        <option>Kawin</option>
                                        <option>Cerai Hidup</option>
                                        <option>Cerai Mati</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="nama_ibu_kandung"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ibu Kandung
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="nama_ibu_kandung" name="nama_ibu_kandung" required
                                        placeholder="Masukan Nama Ibu Kandung">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="nik"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NIK
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="nik" name="nik" required
                                        placeholder="Masukan NIK">
                                    <div class="invalid-feedback" id="nik-feedback">NIK sudah terdaftar.</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="niy"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NIY
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="niy" name="niy" required
                                        placeholder="Masukan NIY">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="nuptk"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NUPTK
                                    </label>
                                    <input type="text" class="form-control radius-8" id="nuptk" name="nuptk"
                                        placeholder="Masukan NUPTK">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="no_sk_pengangkatan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor SK Pengangkatan
                                    </label>
                                    <input type="text" class="form-control radius-8" id="no_sk_pengangkatan" name="no_sk_pengangkatan"
                                        placeholder="Masukan Nomor SK Pengangkatan">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="tgl_sk_pengangkatan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl SK Pengangkatan</label>
                                    <input type="date" class="form-control radius-8" id="tgl_sk_pengangkatan" name="tgl_sk_pengangkatan"
                                        placeholder="Masukan Tgl SK Pengangkatan">
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="email"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                                    <span class="text-danger-600">*</span> </label>
                                    <input type="email" class="form-control radius-8" id="email" name="email"
                                        placeholder="Enter email address">
                                    <div class="invalid-feedback" id="email-feedback">Email sudah terdaftar.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="telepon"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">No Ponsel</label>
                                    <input type="text" class="form-control radius-8" id="telepon" name="telepon"
                                        placeholder="Enter phone number">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="status_pegawai"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status Pegawai
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="status_pegawai" name="status_pegawai">
                                        <option>GTY/PTY </option>
                                        <option>ASN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="penugasan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Penugasan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="penugasan" name="penugasan">
                                        <option> Guru</option>
                                        <option> Guru & TAS </option>
                                        <option> TAS </option>
                                        <option> Kepala Sekolah</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="mb-20">
                                    <label for="alamat"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                                    <input type="text" class="form-control radius-8" id="alamat" name="alamat"
                                        placeholder="Masukan alamat Jalan/Dusun/Kampung">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="rt"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label>
                                    <input type="text" class="form-control radius-8" id="rt" name="rt"
                                        placeholder="Masukan RT">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="rw"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label>
                                    <input type="text" class="form-control radius-8" id="rw" name="rw"
                                        placeholder="Masukan RT">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="provinsi"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="provinsi" name="provinsi" required>
                                        <option value="">Pilih Provinsi</option>
                                        <?php foreach ($provinsi as $p) : ?>
                                            <option value="<?php echo $p->id_prov ?>"><?php echo $p->nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="kabupaten"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="kabupaten" name="kabupaten" required disabled>
                                        <option value="">Pilih Kabupaten</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="kecamatan"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="kecamatan" name="kecamatan" required disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="kelurahan_desa"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan/Desa
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="kelurahan_desa" name="kelurahan_desa" required disabled>
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="mb-20 mt-2">
                            <label for="password"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Password Baru<span
                                    class="text-danger-600">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control radius-8" id="password" name="password" required
                                    placeholder="Masukan Password Baru*">
                                <span
                                    class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                    data-toggle="#password"></span>
                            </div>
                        </div>
                        <div class="mb-20">
                            <label for="confirm-password"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Konfirmasi Password
                                <span class="text-danger-600">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control radius-8" id="confirm-password" required
                                    placeholder="Konfirmasi Password*">
                                <span
                                    class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                    data-toggle="#confirm-password"></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="submit" id="btnSimpanPtk"
                                class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8">
                                Simpan PTK
                            </button>
                        </div>
                    </form>




                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>


<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        var duplicateState = {
            nik: false,
            email: false,
            checking: false
        };
        var duplicateTimer = null;
        var duplicateRequest = null;

        function setDuplicateFeedback(field, isDuplicate) {
            var input = $('#' + field);
            var feedback = $('#' + field + '-feedback');
            var label = field === 'nik' ? 'NIK' : 'Email';

            duplicateState[field] = isDuplicate;
            input.toggleClass('is-invalid', isDuplicate);
            input[0].setCustomValidity(isDuplicate ? label + ' sudah terdaftar.' : '');
            feedback.text(label + ' sudah terdaftar.');
            $('#btnSimpanPtk').prop('disabled', duplicateState.nik || duplicateState.email || duplicateState.checking);
        }

        function cekDuplikatPtk() {
            var nik = $.trim($('#nik').val());
            var email = $.trim($('#email').val());

            if (!nik && !email) {
                setDuplicateFeedback('nik', false);
                setDuplicateFeedback('email', false);
                return;
            }

            if (duplicateRequest) {
                duplicateRequest.abort();
            }

            duplicateState.checking = true;
            $('#btnSimpanPtk').prop('disabled', true);

            duplicateRequest = $.ajax({
                url: "<?php echo url('ptk/ptkCekDuplikat') ?>",
                type: "POST",
                data: {
                    nik: nik,
                    email: email
                },
                dataType: "json",
                success: function(response) {
                    var duplicates = response && response.duplicates ? response.duplicates : {};
                    setDuplicateFeedback('nik', !!duplicates.nik);
                    setDuplicateFeedback('email', !!duplicates.email);
                },
                complete: function(xhr, status) {
                    if (status !== 'abort') {
                        duplicateState.checking = false;
                        $('#btnSimpanPtk').prop('disabled', duplicateState.nik || duplicateState.email);
                    }
                }
            });
        }

        function jadwalkanCekDuplikat() {
            clearTimeout(duplicateTimer);
            duplicateTimer = setTimeout(cekDuplikatPtk, 400);
        }

        $('#nik, #email').on('input blur', jadwalkanCekDuplikat);
        $('#formTambahPtk').on('submit', function(event) {
            if (duplicateState.nik || duplicateState.email || duplicateState.checking) {
                event.preventDefault();
                cekDuplikatPtk();
                this.reportValidity();
            }
        });

        // Provinsi ke Kabupaten
        $('#provinsi').on('change', function() {
            var id_prov = $(this).val();
            if (id_prov) {
                $.ajax({
                    url: "<?php echo url('ptk/getKabupaten') ?>",
                    type: "POST",
                    data: {
                        id: id_prov
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#kabupaten').html('<option value="">Pilih Kabupaten</option>');
                        $.each(data, function(key, value) {
                            $('#kabupaten').append('<option value="' + value.id_kab + '">' + value.nama + '</option>');
                        });
                        $('#kabupaten').removeAttr('disabled');
                        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').attr('disabled', 'disabled');
                        $('#kelurahan_desa').html('<option value="">Pilih Kelurahan</option>').attr('disabled', 'disabled');
                    }
                });
            } else {
                $('#kabupaten, #kecamatan, #kelurahan_desa').html('<option value="">Pilih</option>').attr('disabled', 'disabled');
            }
        });

        // Kabupaten ke Kecamatan
        $('#kabupaten').on('change', function() {
            var id_kab = $(this).val();
            if (id_kab) {
                $.ajax({
                    url: "<?php echo url('ptk/getKecamatan') ?>",
                    type: "POST",
                    data: {
                        id: id_kab
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#kecamatan').append('<option value="' + value.id_kec + '">' + value.nama + '</option>');
                        });
                        $('#kecamatan').removeAttr('disabled');
                        $('#kelurahan_desa').html('<option value="">Pilih Kelurahan</option>').attr('disabled', 'disabled');
                    }
                });
            }
        });

        // Kecamatan ke Kelurahan
        $('#kecamatan').on('change', function() {
            var id_kec = $(this).val();
            if (id_kec) {
                $.ajax({
                    url: "<?php echo url('ptk/getKelurahan') ?>",
                    type: "POST",
                    data: {
                        id: id_kec
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#kelurahan_desa').html('<option value="">Pilih Kelurahan</option>');
                        $.each(data, function(key, value) {
                            $('#kelurahan_desa').append('<option value="' + value.id_kel + '">' + value.nama + '</option>');
                        });
                        $('#kelurahan_desa').removeAttr('disabled');
                    }
                });
            }
        });
    });
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-button').each(function() {
            var tooltipButton = $(this);
            var tooltipContent = $(this).siblings('.my-tooltip').html();

            // Initialize the tooltip
            tooltipButton.tooltip({
                title: tooltipContent,
                trigger: 'hover',
                html: true
            });

            // Optionally, reinitialize the tooltip if the content might change dynamically
            tooltipButton.on('mouseenter', function() {
                tooltipButton.tooltip('dispose').tooltip({
                    title: tooltipContent,
                    trigger: 'hover',
                    html: true
                }).tooltip('show');
            });
        });
    });
</script>