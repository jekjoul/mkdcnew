<form action="<?php echo url('master/jenisRuanganUpdate/' . $jenis_ruangan->id_jenis_ruangan) ?>" method="post">
    <div class="row">
        <div class="col-md-12 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Jenis Ruangan</label>
            <input type="text" class="form-control radius-8" id="editname" name="nama_jenis_ruangan" value="<?= $jenis_ruangan->nama_jenis_ruangan ?>" required>
            <div class="invalid-feedback">
                Silahkan masukan Nama Jenis Ruangan.
            </div>
        </div>

        <div class="col-md-12 mb-20 was-validated">
            <label for="editname"
                class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
            <select class="form-control radius-8 form-select" id="editcountry" name="status">
                <option value="<?= $jenis_ruangan->status ?>"><?= $jenis_ruangan->status ?></option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>

        <div class="w-50-px h-50-px mx-auto my-4 d-flex justify-content-center align-items-center gap-3">
            <button type="submit" class="btn btn-sm btn-info-500 px-20 py-11"> Simpan</button>
        </div>
    </div>
</form>