<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<!-- meta tags and other links -->

<div class="dashboard-main-body">
    <div class="card basic-data-table">
        <div class="card-header p-0 border-0">
            <div class="responsive-padding-40-150 bg-success-50">
                <div class="row gy-4 align-items-center">
                    <div class="col-xl-7">
                        <h4 class="mb-20"><?= $lembaga->nama_lembaga ?></h4>
                        <table>
                            <tr>
                                <td>Kepala Sekolah </td>
                                <td style="width: 20px; text-align: center;">:</td>
                                <td class="fw-semibold"><?= $lembaga->nama_kepsek ?></td>
                            </tr>
                            <tr>
                                <td>NPSN</td>
                                <td style="width: 20px; text-align: center;">:</td>
                                <td class="fw-semibold"><?= $lembaga->npsn ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xl-5 d-xl-block d-none text-end">
                        <?php if (!empty($lembaga->logo)): ?>
                            <img src="<?= urlUpload('logo_lembaga/' . $lembaga->logo) ?>" alt="Logo Lembaga" style="max-height: 120px;" class="radius-8">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-100 text-success-600 rounded-circle" style="width: 100px; height: 100px;">
                                <iconify-icon icon="solar:home-linear" style="font-size: 50px;"></iconify-icon>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body bg-base responsive-padding-40-150">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="active-text-tab nav flex-column nav-pills bg-base shadow py-0 px-24 radius-12 border"
                        id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button
                            class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16 border-bottom active"
                            id="v-pills-about-us-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profil"
                            type="button" role="tab" aria-controls="v-pills-about-us" aria-selected="true">Profil</button>
                        <button class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16 border-bottom"
                            id="v-pills-ux-ui-tab" data-bs-toggle="pill" data-bs-target="#v-pills-ux-ui" type="button"
                            role="tab" aria-controls="v-pills-ux-ui" aria-selected="false">Kepegawaian</button>
                        <button class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16 border-bottom"
                            id="v-pills-development-tab" data-bs-toggle="pill" data-bs-target="#v-pills-development"
                            type="button" role="tab" aria-controls="v-pills-development"
                            aria-selected="false">Siswa</button>
                        <button class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16 border-bottom"
                            id="v-pills-use-case-tab" data-bs-toggle="pill" data-bs-target="#v-pills-use-case"
                            type="button" role="tab" aria-controls="v-pills-use-case" aria-selected="false">Sarana & Prasarana</button>
                        <button class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16"
                            id="v-pills-use-agency-tab" data-bs-toggle="pill" data-bs-target="#v-pills-use-agency"
                            type="button" role="tab" aria-controls="v-pills-use-agency" aria-selected="false">Alumni</button>
                        <?php if (hasPermissions('lembaga_edit')): ?>
                        <button class="nav-link text-secondary-light fw-semibold text-xl px-0 py-16 border-top"
                            id="v-pills-edit-tab" data-bs-toggle="pill" data-bs-target="#v-pills-edit"
                            type="button" role="tab" aria-controls="v-pills-edit" aria-selected="false">Edit Lembaga</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-profil" role="tabpanel"
                            aria-labelledby="v-pills-about-us-tab" tabindex="0">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            Identitas Sekolah
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <table>
                                                <tr>
                                                    <td style="min-width: 150px;">NPSN</td>
                                                    <td style="min-width: 15px;">:</td>
                                                    <td><?= $lembaga->npsn ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Bentuk Pendidikan</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->bentuk_pendidikan ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->status ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Akreditasi</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->akreditasi ?></td>
                                                </tr>
                                                <tr>
                                                    <td>SK Akreditasi</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->no_sk_akreditasi ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            Alamat Sekolah
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <table>
                                                <tr>
                                                    <td style="min-width: 150px;">Alamat</td>
                                                    <td style="min-width: 15px;">:</td>
                                                    <td><?= $lembaga->alamat ?> RT <?= $lembaga->rt ?> RW <?= $lembaga->rw ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Desa/Kel.</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->kelurahan ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Kecamatan</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->kecamatan ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Kabupaten</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->kabupaten ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Provinsi</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->provinsi ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            Kontak Sekolah
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <table>
                                                <tr>
                                                    <td style="min-width: 150px;">Telp./Whatsapp</td>
                                                    <td style="min-width: 15px;">:</td>
                                                    <td><?= $lembaga->telepon ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->email ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Website</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->website ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Instagram</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->instagram ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tiktok</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->tiktok ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Youtube</td>
                                                    <td>:</td>
                                                    <td><?= $lembaga->youtube ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour">
                                            Can other info be added to an invoice?
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive">
                                            How does billing work?
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                            aria-expanded="false" aria-controls="collapseSix">
                                            How do I change my account email?
                                        </button>
                                    </h2>
                                    <div id="collapseSix" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-ux-ui" role="tabpanel"
                            aria-labelledby="v-pills-ux-ui-tab" tabindex="0">
                            <div class="accordion" id="accordionExampleTwo">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#c-1" aria-expanded="true"
                                            aria-controls="c-1">
                                            Is there a free trial available?
                                        </button>
                                    </h2>
                                    <div id="c-1" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-2"
                                            aria-expanded="false" aria-controls="c-2">
                                            Can I change my plan later?
                                        </button>
                                    </h2>
                                    <div id="c-2" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-3"
                                            aria-expanded="false" aria-controls="c-3">
                                            What is your cancellation policy?
                                        </button>
                                    </h2>
                                    <div id="c-3" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-4"
                                            aria-expanded="false" aria-controls="c-4">
                                            Can other info be added to an invoice?
                                        </button>
                                    </h2>
                                    <div id="c-4" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-5"
                                            aria-expanded="false" aria-controls="c-5">
                                            How does billing work?
                                        </button>
                                    </h2>
                                    <div id="c-5" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-6"
                                            aria-expanded="false" aria-controls="c-6">
                                            How do I change my account email?
                                        </button>
                                    </h2>
                                    <div id="c-6" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleTwo">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-development" role="tabpanel"
                            aria-labelledby="v-pills-development-tab" tabindex="0">
                            <div class="accordion" id="accordionExampleThree">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#c-7" aria-expanded="true"
                                            aria-controls="c-7">
                                            Is there a free trial available?
                                        </button>
                                    </h2>
                                    <div id="c-7" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-8"
                                            aria-expanded="false" aria-controls="c-8">
                                            Can I change my plan later?
                                        </button>
                                    </h2>
                                    <div id="c-8" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-9"
                                            aria-expanded="false" aria-controls="c-9">
                                            What is your cancellation policy?
                                        </button>
                                    </h2>
                                    <div id="c-9" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-10"
                                            aria-expanded="false" aria-controls="c-10">
                                            Can other info be added to an invoice?
                                        </button>
                                    </h2>
                                    <div id="c-10" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-11"
                                            aria-expanded="false" aria-controls="c-11">
                                            How does billing work?
                                        </button>
                                    </h2>
                                    <div id="c-11" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-12"
                                            aria-expanded="false" aria-controls="c-12">
                                            How do I change my account email?
                                        </button>
                                    </h2>
                                    <div id="c-12" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleThree">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-use-case" role="tabpanel"
                            aria-labelledby="v-pills-use-case-tab" tabindex="0">
                            <div class="accordion" id="accordionExampleFour">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#c-13" aria-expanded="true"
                                            aria-controls="c-13">
                                            Is there a free trial available?
                                        </button>
                                    </h2>
                                    <div id="c-13" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-14"
                                            aria-expanded="false" aria-controls="c-14">
                                            Can I change my plan later?
                                        </button>
                                    </h2>
                                    <div id="c-14" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-15"
                                            aria-expanded="false" aria-controls="c-15">
                                            What is your cancellation policy?
                                        </button>
                                    </h2>
                                    <div id="c-15" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-16"
                                            aria-expanded="false" aria-controls="c-16">
                                            Can other info be added to an invoice?
                                        </button>
                                    </h2>
                                    <div id="c-16" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-17"
                                            aria-expanded="false" aria-controls="c-17">
                                            How does billing work?
                                        </button>
                                    </h2>
                                    <div id="c-17" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-18"
                                            aria-expanded="false" aria-controls="c-18">
                                            How do I change my account email?
                                        </button>
                                    </h2>
                                    <div id="c-18" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFour">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-use-agency" role="tabpanel"
                            aria-labelledby="v-pills-use-agency-tab" tabindex="0">
                            <div class="accordion" id="accordionExampleFIve">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#c-19" aria-expanded="true"
                                            aria-controls="c-19">
                                            Is there a free trial available?
                                        </button>
                                    </h2>
                                    <div id="c-19" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-20"
                                            aria-expanded="false" aria-controls="c-20">
                                            Can I change my plan later?
                                        </button>
                                    </h2>
                                    <div id="c-20" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-21"
                                            aria-expanded="false" aria-controls="c-21">
                                            What is your cancellation policy?
                                        </button>
                                    </h2>
                                    <div id="c-21" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-22"
                                            aria-expanded="false" aria-controls="c-22">
                                            Can other info be added to an invoice?
                                        </button>
                                    </h2>
                                    <div id="c-22" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-23"
                                            aria-expanded="false" aria-controls="c-23">
                                            How does billing work?
                                        </button>
                                    </h2>
                                    <div id="c-23" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button text-primary-light text-xl collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#c-24"
                                            aria-expanded="false" aria-controls="c-24">
                                            How do I change my account email?
                                        </button>
                                    </h2>
                                    <div id="c-24" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExampleFIve">
                                        <div class="accordion-body">
                                            Yes, you can try us for free for 30 days. If you want, we’ll provide you
                                            with a free, personalized 30-minute onboarding call to get you up and
                                            running as soon as possible.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (hasPermissions('lembaga_edit')): ?>
                        <div class="tab-pane fade" id="v-pills-edit" role="tabpanel" aria-labelledby="v-pills-edit-tab" tabindex="0">
                            <div class="card shadow">
                                <div class="card-header py-16 px-24 bg-base border-bottom">
                                    <h6 class="text-lg mb-0 text-primary">Edit Data Lembaga</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?= url('lembaga/update/' . $lembaga->id_lembaga) ?>" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lembaga <span class="text-danger-600">*</span></label>
                                                <input type="text" class="form-control radius-8" name="nama_lembaga" required value="<?= htmlspecialchars($lembaga->nama_lembaga) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Singkat Lembaga</label>
                                                <input type="text" class="form-control radius-8" name="nama_lembaga_singkat" value="<?= htmlspecialchars($lembaga->nama_lembaga_singkat) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">NPSN</label>
                                                <input type="text" class="form-control radius-8" name="npsn" value="<?= htmlspecialchars($lembaga->npsn) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bentuk Pendidikan</label>
                                                <input type="text" class="form-control radius-8" name="bentuk_pendidikan" value="<?= htmlspecialchars($lembaga->bentuk_pendidikan) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                                                <input type="text" class="form-control radius-8" name="status" value="<?= htmlspecialchars($lembaga->status) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Akreditasi</label>
                                                <input type="text" class="form-control radius-8" name="akreditasi" value="<?= htmlspecialchars($lembaga->akreditasi) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">No SK Akreditasi</label>
                                                <input type="text" class="form-control radius-8" name="no_sk_akreditasi" value="<?= htmlspecialchars($lembaga->no_sk_akreditasi) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kepala Sekolah</label>
                                                <select class="form-control radius-8 form-select" name="id_ptk_kepsek">
                                                    <option value="">Pilih Kepala Sekolah</option>
                                                    <?php foreach ($ptk as $p): ?>
                                                        <option value="<?= $p->id_ptk ?>" <?= $lembaga->id_ptk_kepsek == $p->id_ptk ? 'selected' : '' ?>><?= htmlspecialchars($p->nama_ptk) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Telepon</label>
                                                <input type="text" class="form-control radius-8" name="telepon" value="<?= htmlspecialchars($lembaga->telepon) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                                                <input type="email" class="form-control radius-8" name="email" value="<?= htmlspecialchars($lembaga->email) ?>">
                                            </div>
                                            <div class="col-sm-6 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Website</label>
                                                <input type="text" class="form-control radius-8" name="website" value="<?= htmlspecialchars($lembaga->website) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Instagram</label>
                                                <input type="text" class="form-control radius-8" name="instagram" value="<?= htmlspecialchars($lembaga->instagram) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tiktok</label>
                                                <input type="text" class="form-control radius-8" name="tiktok" value="<?= htmlspecialchars($lembaga->tiktok) ?>">
                                            </div>
                                            <div class="col-sm-4 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Youtube</label>
                                                <input type="text" class="form-control radius-8" name="youtube" value="<?= htmlspecialchars($lembaga->youtube) ?>">
                                            </div>
                                            <div class="col-sm-12 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                                                <textarea class="form-control radius-8" name="alamat" rows="2"><?= htmlspecialchars($lembaga->alamat) ?></textarea>
                                            </div>
                                            <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label><input type="text" class="form-control radius-8" name="rt" value="<?= htmlspecialchars($lembaga->rt) ?>"></div>
                                            <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label><input type="text" class="form-control radius-8" name="rw" value="<?= htmlspecialchars($lembaga->rw) ?>"></div>
                                            <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan</label><input type="text" class="form-control radius-8" name="kelurahan" value="<?= htmlspecialchars($lembaga->kelurahan) ?>"></div>
                                            <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan</label><input type="text" class="form-control radius-8" name="kecamatan" value="<?= htmlspecialchars($lembaga->kecamatan) ?>"></div>
                                            <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten</label><input type="text" class="form-control radius-8" name="kabupaten" value="<?= htmlspecialchars($lembaga->kabupaten) ?>"></div>
                                            <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi</label><input type="text" class="form-control radius-8" name="provinsi" value="<?= htmlspecialchars($lembaga->provinsi) ?>"></div>
                                            <div class="col-sm-12 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Koordinat Lembaga</label>
                                                <input type="text" class="form-control radius-8 mb-8" name="koordinat" id="lembaga_koordinat" value="<?= htmlspecialchars($lembaga->koordinat) ?>" readonly>
                                                <div id="lembaga-map" class="radius-8 border" style="height: 320px; overflow: hidden;"></div>
                                            </div>
                                            <div class="col-sm-12 mb-20">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Logo Lembaga</label>
                                                <?php if (!empty($lembaga->logo)): ?>
                                                    <div class="mb-12">
                                                        <img src="<?= urlUpload('logo_lembaga/' . $lembaga->logo) ?>" alt="Logo Lembaga" class="radius-8 border p-4" style="max-height: 120px;">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control radius-8" name="logo" accept=".jpg,.jpeg,.png,.gif">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center mt-20">
                                            <button type="submit" class="btn btn-success text-md px-56 py-12 radius-8">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Institution Coordinate Map
    (function() {
        const mapEl = document.getElementById('lembaga-map');
        const koordinatInput = document.getElementById('lembaga_koordinat');
        if (!mapEl || !koordinatInput || typeof L === 'undefined') return;

        function parseCoordinate(value) {
            const parts = (value || '').split(',').map(function(item) {
                return parseFloat(item.trim());
            });
            return parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1]) ? parts : null;
        }

        const initialCoord = parseCoordinate(koordinatInput.value) || [-7.1454257, 108.2664001];
        const map = L.map(mapEl).setView(initialCoord, 16);
        let marker = L.marker(initialCoord).addTo(map);

        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: 'Map data &copy; Google'
        }).addTo(map);

        map.on('click', function(e) {
            const latlng = e.latlng;
            koordinatInput.value = latlng.lat.toFixed(7) + ',' + latlng.lng.toFixed(7);
            marker.setLatLng(latlng);
        });

        // Invalidate size when the tab is shown to fix rendering glitches
        const editTabButton = document.getElementById('v-pills-edit-tab');
        if (editTabButton) {
            editTabButton.addEventListener('shown.bs.tab', function() {
                map.invalidateSize();
                map.setView(marker.getLatLng(), 16);
            });
        }
    })();
</script>