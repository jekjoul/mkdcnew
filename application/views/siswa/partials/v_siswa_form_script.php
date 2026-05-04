<script>
    function selectId(prefix, name) {
        return '#id_' + name + prefix;
    }

    $('.wilayah-provinsi').on('change', function() {
        const prefix = $(this).data('prefix');
        const id = $(this).val();
        $(selectId(prefix, 'kabupaten')).html('<option value="">Pilih Kabupaten</option>');
        $(selectId(prefix, 'kecamatan')).html('<option value="">Pilih Kecamatan</option>');
        $(selectId(prefix, 'kelurahan')).html('<option value="">Pilih Kelurahan</option>');
        if (!id) return;
        $.post('<?php echo url('siswa/getKabupaten') ?>', { id: id }, function(data) {
            $.each(data, function(key, value) {
                $(selectId(prefix, 'kabupaten')).append('<option value="' + value.id_kab + '">' + value.nama + '</option>');
            });
        }, 'json');
    });

    $('.wilayah-kabupaten').on('change', function() {
        const prefix = $(this).data('prefix');
        const id = $(this).val();
        $(selectId(prefix, 'kecamatan')).html('<option value="">Pilih Kecamatan</option>');
        $(selectId(prefix, 'kelurahan')).html('<option value="">Pilih Kelurahan</option>');
        if (!id) return;
        $.post('<?php echo url('siswa/getKecamatan') ?>', { id: id }, function(data) {
            $.each(data, function(key, value) {
                $(selectId(prefix, 'kecamatan')).append('<option value="' + value.id_kec + '">' + value.nama + '</option>');
            });
        }, 'json');
    });

    $('.wilayah-kecamatan').on('change', function() {
        const prefix = $(this).data('prefix');
        const id = $(this).val();
        $(selectId(prefix, 'kelurahan')).html('<option value="">Pilih Kelurahan</option>');
        if (!id) return;
        $.post('<?php echo url('siswa/getKelurahan') ?>', { id: id }, function(data) {
            $.each(data, function(key, value) {
                $(selectId(prefix, 'kelurahan')).append('<option value="' + value.id_kel + '">' + value.nama + '</option>');
            });
        }, 'json');
    });

    function toggleAlamatOrtu(target) {
        $('.alamat-' + target).toggle(!$('[data-target="' + target + '"]').is(':checked'));
        $('[name="id_provinsi_' + target + '"], [name="id_kabupaten_' + target + '"], [name="id_kecamatan_' + target + '"], [name="id_kelurahan_' + target + '"]').closest('.col-sm-6').toggle(!$('[data-target="' + target + '"]').is(':checked'));
    }
    $('.alamat-sama').on('change', function() { toggleAlamatOrtu($(this).data('target')); });
    toggleAlamatOrtu('ayah');
    toggleAlamatOrtu('ibu');
</script>
