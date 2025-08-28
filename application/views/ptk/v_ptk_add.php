<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-success" >
                    <div class="d-flex flex-wrap align-items-center gap-3">
                      <h6 class="text-light">Formulir Tambah PTK</h6>
                    </div>
                    
                </div>
                <div class="card-body">
                    
                    <!-- Upload Image End -->
                    <form action="#">
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan nama lengkap tanpa gelar">
                                </div>
                            </div>

                             <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="depart"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Kelamin
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="depart">
                                        <option>Laki-laki</option>
                                        <option>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Depan</label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan gelar depan, Contoh : Dr. / Drs. / Ir. / Prof.">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Belakang</label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan gelar depan, Contoh : S.Pd. / M.Pd. / M.Si.">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tempat Lahir
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan tempat lahir">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lahir
                                        <span class="text-danger-600">*</span></label>
                                    <input type="date" class="form-control radius-8" id="name"
                                        placeholder="Masukan nama lengkap tanpa gelar">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="desig"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Agama
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="desig">
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
                                    <label for="desig"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status Perkawinan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="desig">
                                        <option>Belum Kawin</option>
                                        <option>Kawin</option>
                                        <option>Cerai Hidup</option>
                                        <option>Cerai Mati</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ibu Kandung
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan Nama Ibu Kandung">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NIK
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan NIK">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NIY
                                        <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan NIY">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">NUPTK
                                        </label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan NUPTK">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor SK Pengangkatan
                                        </label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        placeholder="Masukan Nomor SK Pengangkatan">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl SK Pengangkatan</label>
                                    <input type="date" class="form-control radius-8" id="name"
                                        placeholder="Masukan Tgl SK Pengangkatan">
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="email"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Email
                                        <span class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="email"
                                        placeholder="Enter email address">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="number"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">No Ponsel</label>
                                    <input type="email" class="form-control radius-8" id="number"
                                        placeholder="Enter phone number">
                                </div>
                            </div>
                           
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="desig"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status Pegawai
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="desig">
                                        <option>GTY/PTY </option>
                                        <option>ASN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Language"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Penugasan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="Language">
                                        <option> Guru</option>
                                        <option> Guru & TAS </option>
                                        <option> TAS </option>
                                        <option> Kepala Sekolah</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="mb-20">
                                    <label for="number"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                                    <input type="email" class="form-control radius-8" id="number"
                                        placeholder="Masukan alamat Jalan/Dusun/Kampung">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="number"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label>
                                    <input type="email" class="form-control radius-8" id="number"
                                        placeholder="Masukan RT">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="number"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label>
                                    <input type="email" class="form-control radius-8" id="number"
                                        placeholder="Masukan RT">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Language"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="Language">
                                        <option> Jawa Barat</option>
                                    </select>
                                </div>
                            </div>

                             <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Language"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="Language">
                                        <option> Ciamis</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Language"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="Language">
                                        <option> Panjalu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Language"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan/Desa
                                        <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="Language">
                                        <option> Kertamandala</option>
                                    </select>
                                </div>
                            </div>
                           
                        </div>
                        
                        <div class="mb-20 mt-2">
                            <label for="your-password"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Password Baru<span
                                    class="text-danger-600">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control radius-8" id="your-password"
                                    placeholder="Masukan Password Baru*">
                                <span
                                    class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                    data-toggle="#your-password"></span>
                            </div>
                        </div>
                        <div class="mb-20">
                            <label for="confirm-password"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Konfirmasi Password
                                <span class="text-danger-600">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control radius-8" id="confirm-password"
                                    placeholder="Konfirmasi Password*">
                                <span
                                    class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                    data-toggle="#confirm-password"></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button"
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
  let table = new DataTable('#dataTable');
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]'); 
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)); 

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-button').each(function () {
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
