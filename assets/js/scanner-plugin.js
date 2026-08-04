// Scan Plugin for MKDC Web — v2.1
$(document).ready(function () {
    const SCANNER_API_URL = 'http://127.0.0.1:7999';

    // -----------------------------------------------------------------------
    // Cek apakah scanner bridge berjalan
    // -----------------------------------------------------------------------
    function checkScannerBridge() {
        return fetch(SCANNER_API_URL, { signal: AbortSignal.timeout(3000) })
            .then(res => res.json())
            .then(data => data.status === 'running')
            .catch(() => false);
    }

    // -----------------------------------------------------------------------
    // Pasang tombol "Scan Langsung" pada setiap input file .scan-enabled
    // -----------------------------------------------------------------------
    function attachScanButtons() {
        $('input[type="file"].scan-enabled').each(function () {
            const input = $(this);

            // Jangan tambahkan dua kali
            if (input.parent().find('.btn-direct-scan').length > 0) return;

            // Hanya pasang pada input yang menerima gambar atau PDF
            const accept = input.attr('accept') || '';
            if (accept && !accept.includes('image') && !accept.includes('pdf') && !accept.includes('jpg') && !accept.includes('jpeg') && !accept.includes('png') && !accept.includes('*')) {
                return;
            }

            // Buat tombol scan
            const scanBtn = $('<button type="button" class="btn btn-sm btn-info-600 radius-8 px-12 py-6 ms-2 btn-direct-scan d-inline-flex align-items-center gap-1 flex-shrink-0"><iconify-icon icon="lucide:scan"></iconify-icon> Scan Langsung</button>');

            input.after(scanBtn);

            scanBtn.on('click', async function (e) {
                e.preventDefault();
                await startScanFlow(input);
            });
        });
    }

    // -----------------------------------------------------------------------
    // Alur utama scan
    // -----------------------------------------------------------------------
    async function startScanFlow(fileInput) {
        // 1. Cek bridge
        Swal.fire({
            title: 'Menghubungkan ke Scanner...',
            text: 'Pastikan software MKDC Scanner Bridge sudah dijalankan.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const isBridgeRunning = await checkScannerBridge();
        if (!isBridgeRunning) {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                html: `Aplikasi Desktop MKDC Scanner Bridge belum dijalankan.<br><br>
                       Silakan buka <strong>Aplikasi Desktop MKDC Scanner Bridge</strong> atau jalankan file <strong>start.bat</strong> di folder scanner_bridge.`
            });
            return;
        }

        // 2. Ambil daftar device
        let devices = [];
        try {
            const devicesRes = await fetch(`${SCANNER_API_URL}/devices`);
            const raw = await devicesRes.json();

            if (raw && raw.error) {
                // Bridge mengembalikan error (misal: PowerShell gagal)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendaftar Scanner',
                    html: `Bridge berjalan, tetapi gagal mendeteksi perangkat scanner.<br>
                           <small class="text-muted">${raw.details || raw.error}</small><br><br>
                           Pastikan:<br>
                           1. Windows PowerShell tersedia<br>
                           2. Scanner USB terpasang dengan benar<br>
                           3. Driver scanner (WIA) sudah terinstal`
                });
                return;
            }

            // PowerShell ConvertTo-Json mengembalikan objek tunggal (bukan array) jika hanya 1 device
            if (raw && !Array.isArray(raw)) {
                devices = [raw];
            } else {
                devices = raw || [];
            }

            // Filter device yang tidak valid
            devices = devices.filter(d => d && (d.id || d.name));

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Proses Pindai Gagal',
                text: 'Terjadi kesalahan saat mengambil daftar scanner: ' + err.message
            });
            return;
        }

        if (devices.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Alat Scanner Tidak Ditemukan',
                html: `Bridge berjalan, tetapi tidak ada scanner fisik yang terhubung ke komputer.<br><br>
                       Pastikan:<br>
                       - Kabel USB scanner terpasang<br>
                       - Driver scanner sudah terinstal<br>
                       - Scanner sudah dinyalakan`
            });
            return;
        }

        // 3. Tampilkan form pilih scanner (radio button menghindari masalah z-index SweetAlert2)
        let deviceRadioHtml = '';
        devices.forEach((dev, idx) => {
            const id = dev.id || '';
            const name = dev.name || 'Scanner ' + (idx + 1);
            const desc = dev.description || 'WIA Scanner';
            const bg = idx === 0 ? '#e8f4fd' : '#fff';
            deviceRadioHtml += `<label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #dee2e6;border-radius:8px;margin-bottom:6px;cursor:pointer;background:${bg};" class="swal-device-label"><input type="radio" name="swal_device" value="${id}" ${idx === 0 ? 'checked' : ''} style="accent-color:#0d6efd;"><span style="font-size:13px;"><strong>${name}</strong><br><small style="color:#6c757d;">${desc}</small></span></label>`;
        });

        const { value: params, isConfirmed } = await Swal.fire({
            title: 'Mulai Memindai (Scan)',
            width: 480,
            html: `
                <div style="text-align:left;margin-bottom:16px;">
                    <div style="font-weight:600;margin-bottom:8px;font-size:13px;">&#128222; Pilih Alat Scanner:</div>
                    ${deviceRadioHtml}
                </div>
                <div style="text-align:left;margin-bottom:16px;">
                    <div style="font-weight:600;margin-bottom:8px;font-size:13px;">&#128196; Format &amp; Metode Scan:</div>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #dee2e6;border-radius:8px;margin-bottom:6px;cursor:pointer;background:#e8f4fd;" class="swal-format-label">
                        <input type="radio" name="swal_format" value="pdf" checked style="accent-color:#0d6efd;">
                        <span style="font-size:13px;"><strong>PDF</strong> &mdash; Dokumen 1 Halaman</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #dee2e6;border-radius:8px;margin-bottom:6px;cursor:pointer;background:#fff;" class="swal-format-label">
                        <input type="radio" name="swal_format" value="pdf_multi" style="accent-color:#0d6efd;">
                        <span style="font-size:13px;"><strong>PDF Multi Halaman</strong> &mdash; Scan Berturut-turut</span>
                    </label>
                </div>
                <div style="background:#e7f3ff;border:1px solid #b6d4fe;border-radius:8px;padding:8px 12px;font-size:12px;text-align:left;color:#0c63e4;">
                    &#8505;&#65039; Ukuran diatur otomatis ke <strong>A4</strong>. Kontras &amp; Gamma ditingkatkan +10%.
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Mulai Scan',
            cancelButtonText: 'Batal',
            didOpen: () => {
                // Highlight label aktif saat radio berubah
                document.querySelectorAll('input[name="swal_device"]').forEach(r => {
                    r.addEventListener('change', () => {
                        document.querySelectorAll('.swal-device-label').forEach(l => l.style.background = '#fff');
                        r.closest('.swal-device-label').style.background = '#e8f4fd';
                    });
                });
                document.querySelectorAll('input[name="swal_format"]').forEach(r => {
                    r.addEventListener('change', () => {
                        document.querySelectorAll('.swal-format-label').forEach(l => l.style.background = '#fff');
                        r.closest('.swal-format-label').style.background = '#e8f4fd';
                    });
                });
            },
            preConfirm: () => {
                const deviceRadio = document.querySelector('input[name="swal_device"]:checked');
                const formatRadio = document.querySelector('input[name="swal_format"]:checked');
                if (!deviceRadio) {
                    Swal.showValidationMessage('Pilih alat scanner terlebih dahulu.');
                    return false;
                }
                if (!formatRadio) {
                    Swal.showValidationMessage('Pilih format scan terlebih dahulu.');
                    return false;
                }
                const formatVal = formatRadio.value;
                return {
                    deviceId: deviceRadio.value,
                    format: formatVal,
                    convertToPdf: true,
                    isMultiPage: formatVal === 'pdf_multi'
                };
            }
        });

        if (!isConfirmed || !params) return;

        // 4. Loop scan (support multi-halaman)
        const scannedPages = [];
        let scanMore = true;
        let pageNum = 1;

        while (scanMore) {
            Swal.fire({
                title: params.isMultiPage ? `Memindai Halaman ${pageNum}...` : 'Memindai...',
                text: 'Mohon tunggu, scanner sedang memproses dokumen Anda...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const scanRes = await fetch(`${SCANNER_API_URL}/scan`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ deviceId: params.deviceId, format: 'png' })
                });

                const scanData = await scanRes.json();
                if (!scanData.success || !scanData.base64) {
                    throw new Error(scanData.details || scanData.error || 'Gagal memproses gambar dari scanner.');
                }

                // Penyesuaian kontras & gamma
                const rawDataUrl = `data:${scanData.mime};base64,${scanData.base64}`;
                const adjustedDataUrl = await adjustContrastAndGamma(rawDataUrl);
                scannedPages.push(adjustedDataUrl);

                if (params.isMultiPage) {
                    const nextAction = await Swal.fire({
                        title: `Halaman ${pageNum} Selesai`,
                        text: 'Apakah Anda ingin memindai halaman berikutnya untuk digabungkan?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan Halaman Berikutnya',
                        cancelButtonText: 'Selesai & Lihat Preview',
                        allowOutsideClick: false
                    });

                    if (nextAction.isConfirmed) {
                        pageNum++;
                    } else {
                        scanMore = false;
                    }
                } else {
                    scanMore = false;
                }

            } catch (loopErr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pindai Gagal',
                    text: loopErr.message
                });
                return;
            }
        }

        // 5. Tampilkan preview hasil scan
        if (scannedPages.length > 0) {
            await showPreviewModal(scannedPages, params, fileInput);
        }
    }

    // -----------------------------------------------------------------------
    // Penyesuaian kontras & gamma + kompresi JPEG untuk efisiensi ukuran file
    // Output: JPEG quality 88% (10-20x lebih kecil dari PNG, kualitas terjaga)
    // -----------------------------------------------------------------------
    function adjustContrastAndGamma(dataUrl) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');

                // Isi background putih (penting agar JPEG tidak ada artefak transparansi)
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imgData.data;

                const contrast = 30;
                const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));
                const gammaCorr = 1 / 1;

                for (let i = 0; i < data.length; i += 4) {
                    let r = factor * (data[i] - 128) + 128;
                    let g = factor * (data[i + 1] - 128) + 128;
                    let b = factor * (data[i + 2] - 128) + 128;

                    r = 255 * Math.pow(Math.max(0, r) / 255, gammaCorr);
                    g = 255 * Math.pow(Math.max(0, g) / 255, gammaCorr);
                    b = 255 * Math.pow(Math.max(0, b) / 255, gammaCorr);

                    data[i] = Math.min(255, Math.max(0, r));
                    data[i + 1] = Math.min(255, Math.max(0, g));
                    data[i + 2] = Math.min(255, Math.max(0, b));
                }

                ctx.putImageData(imgData, 0, 0);
                // Gunakan JPEG 88% — jauh lebih kecil dari PNG, kualitas dokumen tetap baik
                resolve(canvas.toDataURL('image/jpeg', 0.88));
            };
            img.onerror = () => resolve(dataUrl); // fallback tanpa penyesuaian
        });
    }

    // -----------------------------------------------------------------------
    // Preview & upload hasil scan
    // -----------------------------------------------------------------------
    async function showPreviewModal(pages, params, fileInput) {
        let previewHtml = '';

        if (pages.length === 1) {
            previewHtml = `
                <div class="text-center border p-2 radius-8 bg-light mb-3" style="max-height: 400px; overflow-y: auto;">
                    <img src="${pages[0]}" class="img-fluid border radius-4 shadow-sm" style="max-height: 380px;">
                </div>
            `;
        } else {
            let thumbs = '';
            pages.forEach((page, idx) => {
                thumbs += `
                    <div class="col-md-4 col-6 mb-3">
                        <div class="border p-2 radius-8 bg-light text-center h-100 position-relative">
                            <span class="badge bg-primary text-light position-absolute top-0 start-0 m-2">Hal ${idx + 1}</span>
                            <img src="${page}" class="img-fluid border radius-4 shadow-sm" style="max-height: 120px;">
                        </div>
                    </div>
                `;
            });
            previewHtml = `<div class="row" style="max-height: 400px; overflow-y: auto;">${thumbs}</div>`;
        }

        const result = await Swal.fire({
            title: 'Preview Hasil Scan',
            html: `
                <div class="text-start mb-3 text-sm text-secondary-light">
                    Jumlah Halaman: <strong>${pages.length} Halaman</strong><br>
                    Format Output: <strong>${params.isMultiPage ? 'PDF' : params.format.toUpperCase()}</strong>
                </div>
                ${previewHtml}
            `,
            showCancelButton: true,
            confirmButtonText: 'Unggah Berkas',
            cancelButtonText: 'Ulangi / Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#ef4444',
            allowOutsideClick: false
        });

        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Memproses Berkas...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            let file;
            if (params.convertToPdf) {
                await loadJsPDF();
                const pdfBlob = await combineImagesToPdfBlob(pages);
                file = new File([pdfBlob], `scanned_${Date.now()}.pdf`, { type: 'application/pdf' });
            } else {
                const mime = params.format === 'png' ? 'image/png' : 'image/jpeg';
                file = dataURLtoFile(pages[0], `scanned_${Date.now()}.${params.format}`, mime);
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput[0].files = dt.files;
            fileInput.trigger('change');

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File berhasil dimasukkan ke form upload berkas.',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Unggah Gagal',
                text: err.message
            });
        }
    }

    // -----------------------------------------------------------------------
    // Muat jsPDF secara dinamis
    // -----------------------------------------------------------------------
    function loadJsPDF() {
        return new Promise((resolve, reject) => {
            if (window.jspdf) { resolve(); return; }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Gagal memuat pustaka jsPDF'));
            document.head.appendChild(script);
        });
    }

    // Gabungkan beberapa gambar menjadi satu PDF multi-halaman
    // Gambar di-embed sebagai JPEG agar ukuran PDF minimal
    function combineImagesToPdfBlob(pages) {
        return new Promise(async (resolve) => {
            const { jsPDF } = window.jspdf;
            let pdf = null;

            for (let i = 0; i < pages.length; i++) {
                const imgData = pages[i];
                const dimensions = await getImageDimensions(imgData);

                // Konversi ke JPEG jika belum (misal halaman dari format lain)
                const jpegData = await ensureJpeg(imgData);

                if (i === 0) {
                    pdf = new jsPDF({
                        orientation: dimensions.width > dimensions.height ? 'landscape' : 'portrait',
                        unit: 'px',
                        format: [dimensions.width, dimensions.height]
                    });
                } else {
                    pdf.addPage([dimensions.width, dimensions.height], dimensions.width > dimensions.height ? 'l' : 'p');
                }
                // JPEG menghasilkan PDF 10-20x lebih kecil dibanding PNG
                pdf.addImage(jpegData, 'JPEG', 0, 0, dimensions.width, dimensions.height);
            }

            resolve(pdf.output('blob'));
        });
    }

    // Pastikan dataUrl dalam format JPEG (untuk konsistensi embed di PDF)
    function ensureJpeg(dataUrl) {
        return new Promise((resolve) => {
            if (dataUrl.startsWith('data:image/jpeg')) {
                resolve(dataUrl);
                return;
            }
            const img = new Image();
            img.src = dataUrl;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                resolve(canvas.toDataURL('image/jpeg', 0.88));
            };
            img.onerror = () => resolve(dataUrl);
        });
    }

    // Dapatkan dimensi gambar dari dataURL
    function getImageDimensions(dataUrl) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function () { resolve({ width: img.width, height: img.height }); };
        });
    }

    // Konversi dataURL ke objek File
    function dataURLtoFile(dataurl, filename, mimeType) {
        const arr = dataurl.split(',');
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) { u8arr[n] = bstr.charCodeAt(n); }
        return new File([u8arr], filename, { type: mimeType });
    }

    // -----------------------------------------------------------------------
    // Inisialisasi: pasang tombol saat halaman dan setiap kali modal dibuka
    // -----------------------------------------------------------------------
    setTimeout(attachScanButtons, 800);

    $(document).on('shown.bs.modal', function () {
        setTimeout(attachScanButtons, 100);
    });
});
