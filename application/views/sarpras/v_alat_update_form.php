<form action="<?php echo url('sarpras/alatUpdate/' . $sarana->id_sarana) ?>" method="post">
    <div class="row">

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Sarana</label>
            <input type="text" class="form-control radius-8" id="editname" name="nama_sarana" value="<?= $sarana->nama_sarana ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Nama Sarana.
            </div>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Sarana</label>
            <input type="text" class="form-control radius-8" id="editname" name="kode_sarana" value="<?= $sarana->kode_sarana ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Nama Sarana.
            </div>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sarana</label>
            <select class="form-control radius-8 form-select" id="editcountry" name="id_jenis_sarana">
                <option value=" <?= $sarana->id_jenis_sarana ?>"><?= $sarana->nama_jenis_sarana ?></option>
                <?php foreach ($jenis_sarana as $rowt): ?>
                    <option value=" <?= $rowt->id_jenis_sarana ?>"><?= $rowt->nama_jenis_sarana ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Spesifikasi</label>
            <input type="text" class="form-control radius-8" id="editname" name="spesifikasi_sarana" value="<?= $sarana->spesifikasi_sarana ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Spesifikasi Sarana.
            </div>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pengadaan</label>
            <input type="date" class="form-control radius-8" id="editname" name="tgl_pengadaan" value="<?= $sarana->tgl_pengadaan ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Tanggal Pengadaan.
            </div>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Sumber Pengadaan</label>
            <input type="text" class="form-control radius-8" id="editname" name="sumber_pengadaan" value="<?= $sarana->sumber_pengadaan ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Sumber Pengadaan.
            </div>
        </div>


        <div class="col-md-6 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah</label>
            <input type="text" class="form-control radius-8" id="editname" name="jumlah_sarana" value="<?= $sarana->jumlah_sarana ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Jumlah Sarana.
            </div>
        </div>

        <div class="col-md-6 mb-20 was-validated">
            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Laik</label>
            <input type="text" class="form-control radius-8" id="editname" name="jumlah_laik" value="<?= $sarana->jumlah_laik ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Jumlah Sarana Laik.
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
            <button type="submit"
                class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                Simpan
            </button>
        </div>
    </div>
</form>