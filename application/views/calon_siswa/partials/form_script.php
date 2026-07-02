<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $('.wilayah-provinsi').on('change', function() {
        const value = $(this).val();
        $.post('<?php echo url('calon_siswa/getKabupaten') ?>', { id: value }, function(data) {
            $('#id_kabupaten').html('<option value="">Pilih Kabupaten</option>');
            $('#id_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $('#id_kelurahan').html('<option value="">Pilih Kelurahan</option>');
            $.each(JSON.parse(data), function(_, item) {
                $('#id_kabupaten').append('<option value="' + item.id_kab + '">' + item.nama + '</option>');
            });
        });
    });
    $('.wilayah-kabupaten').on('change', function() {
        $.post('<?php echo url('calon_siswa/getKecamatan') ?>', { id: $(this).val() }, function(data) {
            $('#id_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $('#id_kelurahan').html('<option value="">Pilih Kelurahan</option>');
            $.each(JSON.parse(data), function(_, item) {
                $('#id_kecamatan').append('<option value="' + item.id_kec + '">' + item.nama + '</option>');
            });
        });
    });
    $('.wilayah-kecamatan').on('change', function() {
        $.post('<?php echo url('calon_siswa/getKelurahan') ?>', { id: $(this).val() }, function(data) {
            $('#id_kelurahan').html('<option value="">Pilih Kelurahan</option>');
            $.each(JSON.parse(data), function(_, item) {
                $('#id_kelurahan').append('<option value="' + item.id_kel + '">' + item.nama + '</option>');
            });
        });
    });

    (function() {
        const mapEl = document.getElementById('map-koordinat');
        const koordinatInput = document.getElementById('koordinat');
        const jarakInput = document.getElementById('jarak_ke_sekolah');
        const schoolCoordinate = '<?php echo isset($school_coordinate) ? $school_coordinate : '-7.1454257,108.2664001' ?>';

        if (!mapEl || !koordinatInput || !jarakInput || typeof L === 'undefined') {
            return;
        }

        function parseCoordinate(value) {
            const parts = (value || '').split(',').map(function(item) {
                return parseFloat(item.trim());
            });
            return parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1]) ? parts : null;
        }

        function distanceKm(from, to) {
            const radius = 6371;
            const dLat = (to[0] - from[0]) * Math.PI / 180;
            const dLng = (to[1] - from[1]) * Math.PI / 180;
            const lat1 = from[0] * Math.PI / 180;
            const lat2 = to[0] * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1) * Math.cos(lat2) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            return radius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
        }

        const school = parseCoordinate(schoolCoordinate) || [-7.1454257, 108.2664001];
        const selected = parseCoordinate(koordinatInput.value);
        const start = selected || school;
        const map = L.map(mapEl).setView(start, selected ? 16 : 15);
        let studentMarker = null;

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        }).addTo(map);

        L.marker(school).addTo(map).bindPopup('Lokasi Sekolah');

        function setStudentLocation(latlng) {
            const coordinate = [latlng.lat, latlng.lng];
            const value = latlng.lat.toFixed(7) + ',' + latlng.lng.toFixed(7);
            koordinatInput.value = value;
            jarakInput.value = distanceKm(school, coordinate).toFixed(2) + ' km';

            if (studentMarker) {
                studentMarker.setLatLng(latlng);
            } else {
                studentMarker = L.marker(latlng).addTo(map);
            }
        }

        if (selected) {
            setStudentLocation({ lat: selected[0], lng: selected[1] });
        }

        map.on('click', function(event) {
            setStudentLocation(event.latlng);
        });
    })();
</script>
