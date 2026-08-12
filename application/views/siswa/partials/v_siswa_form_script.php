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

    // Leaflet Map Picker for Siswa
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
        let routePolyline = null;

        // Use Google Hybrid Map (Satellite + Roads/Labels overlay)
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: 'Map data &copy; Google'
        }).addTo(map);

        let schoolMarker = L.marker(school).addTo(map).bindPopup('Lokasi Sekolah');

        // Handle school coordinate change based on chosen Lembaga
        const lembagaSelect = document.getElementById('id_lembaga_tujuan');
        if (lembagaSelect) {
            lembagaSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const coordinateStr = selectedOption ? selectedOption.getAttribute('data-coordinate') : null;
                const newSchoolCoord = parseCoordinate(coordinateStr);
                if (newSchoolCoord) {
                    school[0] = newSchoolCoord[0];
                    school[1] = newSchoolCoord[1];
                    schoolMarker.setLatLng(school);
                    
                    // Recalculate route if student marker already exists
                    if (studentMarker) {
                        setStudentLocation(studentMarker.getLatLng());
                    }
                }
            });
            // Trigger initial coordination alignment if pre-selected
            const initialCoord = lembagaSelect.options[lembagaSelect.selectedIndex]?.getAttribute('data-coordinate');
            const parsedInitial = parseCoordinate(initialCoord);
            if (parsedInitial) {
                school[0] = parsedInitial[0];
                school[1] = parsedInitial[1];
                schoolMarker.setLatLng(school);
            }
        }

        function setStudentLocation(latlng) {
            const coordinate = [latlng.lat, latlng.lng];
            const value = latlng.lat.toFixed(7) + ',' + latlng.lng.toFixed(7);
            koordinatInput.value = value;

            if (studentMarker) {
                studentMarker.setLatLng(latlng);
            } else {
                studentMarker = L.marker(latlng).addTo(map);
            }

            // Fetch actual road routing distance using OSRM
            const url = `https://router.project-osrm.org/route/v1/driving/${school[1]},${school[0]};${latlng.lng},${latlng.lat}?overview=full&geometries=geojson`;
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.routes && data.routes[0]) {
                        const route = data.routes[0];
                        const distanceInKm = (route.distance / 1000).toFixed(2);
                        jarakInput.value = distanceInKm + ' km';

                        // Draw driving path on map
                        if (routePolyline) {
                            map.removeLayer(routePolyline);
                        }
                        routePolyline = L.geoJSON(route.geometry, {
                            style: {
                                color: '#e11d48',
                                weight: 5,
                                opacity: 0.85
                            }
                        }).addTo(map);
                    } else {
                        fallbackStraight();
                    }
                })
                .catch(err => {
                    fallbackStraight();
                });

            function fallbackStraight() {
                const dist = distanceKm(school, coordinate).toFixed(2);
                jarakInput.value = dist + ' km (Garis Lurus)';
                if (routePolyline) {
                    map.removeLayer(routePolyline);
                }
            }
        }

        if (selected) {
            setStudentLocation({ lat: selected[0], lng: selected[1] });
        }

        map.on('click', function(event) {
            setStudentLocation(event.latlng);
        });

        // Handler untuk mereset ukuran Leaflet map saat tab Setting aktif / terlihat
        function refreshMapSize() {
            setTimeout(function() {
                if (map) {
                    map.invalidateSize();
                    if (selected) {
                        map.setView([selected[0], selected[1]], 16);
                    } else {
                        map.setView(start, 15);
                    }
                }
            }, 200);
        }

        // 1. Dengarkan event perpindahan tab di Bootstrap (Tab Setting)
        const tabBtns = document.querySelectorAll('button[data-bs-toggle="pill"], a[data-bs-toggle="pill"], a[data-toggle="tab"], [data-bs-target="#pills-setting"], a[href="#pills-setting"]');
        tabBtns.forEach(function(btn) {
            btn.addEventListener('shown.bs.tab', refreshMapSize);
            btn.addEventListener('click', refreshMapSize);
        });

        // 2. Gunakan IntersectionObserver untuk mendeteksi saat elemen map-koordinat muncul di viewport
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        setTimeout(function() {
                            if (map) map.invalidateSize();
                        }, 150);
                    }
                });
            }, { threshold: 0.1 });
            observer.observe(mapEl);
        }

        // 3. Panggil saat window di-resize
        window.addEventListener('resize', refreshMapSize);
        setTimeout(refreshMapSize, 300);
    })();
</script>
