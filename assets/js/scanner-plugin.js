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
                html: `Aplikasi bridge scanner belum dijalankan atau tidak terdeteksi.<br><br>
                       Silakan buka folder <strong>scanner_bridge</strong> di komputer Anda dan jalankan file <strong>start.bat</strong> terlebih dahulu.`
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

        // 3. Tampilkan form pilih scanner
        let deviceOptionsHtml = '';
        devices.forEach((dev, idx) => {
            const id   = dev.id   || '';
            const name = dev.name || 'Scanner ' + (idx + 1);
            const desc = dev.description || 'WIA Scanner';
            deviceOptionsHtml += `<option value="${id}" ${idx === 0 ? 'selected' : ''}>${name} (${desc})</option>`;
        });

        const { value: params, isConfirmed } = await Swal.fire({
            title: 'Mulai Memindai (Scan)',
            html: `
                <div class="text-start mb-3">
                    <label class="form-label fw-semibold mb-1">Pilih Alat Scanner:</label>
                    <select id="swal-scanner-device" class="form-select">
                        ${deviceOptionsHtml}
                    </select>
                </div>
                <div class="text-start mb-3">
                    <label class="form-label fw-semibold mb-1">Format &amp; Metode Scan:</label>
                    <select id="swal-scanner-format" class="form-select">
                        <option value="jpg" selected>JPEG (Gambar Tunggal)</option>
                        <option value="png">PNG (Gambar Tunggal - Kualitas Tinggi)</option>
                        <option value="pdf">PDF (Dokumen 1 Halaman)</option>
                        <option value="pdf_multi">PDF Multi Halaman (Scan Berturut-turut)</option>
                    </select>
                </div>
                <div class="alert alert-info text-start text-xs px-3 py-2 mb-0">
                    <strong>Info:</strong> Ukuran diatur otomatis ke A4. Kontras &amp; Gamma ditingkatkan +10% untuk mencerahkan dokumen.
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Mulai Scan',
            cancelButtonText: 'Batal',
            didOpen: () => {
                // Paksa render elemen setelah SweetAlert2 membuka popup
                const selDevice = document.getElementById('swal-scanner-device');
                const selFormat = document.getElementById('swal-scanner-format');
                if (selDevice) selDevice.dispatchEvent(new Event('change'));
                if (selFormat) selFormat.dispatchEvent(new Event('change'));
            },
            preConfirm: () => {
                const selDevice = document.getElementById('swal-scanner-device');
                const selFormat = document.getElementById('swal-scanner-format');
                if (!selDevice || !selFormat) {
                    Swal.showValidationMessage('Gagal membaca form pilihan scanner.');
                    return false;
                }
                const formatVal = selFormat.value;
                return {
                    deviceId:      selDevice.value,
                    format:        formatVal,
                    convertToPdf:  formatVal === 'pdf' || formatVal === 'pdf_multi',
                    isMultiPage:   formatVal === 'pdf_multi'
                };
            }
        });

        if (!isConfirmed || !params) return;

        // 4. Loop scan (support multi-halaman)
        const scannedPages = [];
        let scanMore = true;
        let pageNum  = 1;

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
    // Penyesuaian kontras (+10%) & gamma (+10%) menggunakan Canvas
    // -----------------------------------------------------------------------
    function adjustContrastAndGamma(dataUrl) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width  = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data    = imgData.data;

                const contrast = 25.5;
                const factor   = (259 * (contrast + 255)) / (255 * (259 - contrast));
                const gammaCorr = 1 / 1.1;

                for (let i = 0; i < data.length; i += 4) {
                    let r = factor * (data[i]   - 128) + 128;
                    let g = factor * (data[i+1] - 128) + 128;
                    let b = factor * (data[i+2] - 128) + 128;

                    r = 255 * Math.pow(Math.max(0, r) / 255, gammaCorr);
                    g = 255 * Math.pow(Math.max(0, g) / 255, gammaCorr);
                    b = 255 * Math.pow(Math.max(0, b) / 255, gammaCorr);

                    data[i]   = Math.min(255, Math.max(0, r));
                    data[i+1] = Math.min(255, Math.max(0, g));
                    data[i+2] = Math.min(255, Math.max(0, b));
                }

                ctx.putImageData(imgData, 0, 0);
                resolve(canvas.toDataURL('image/png'));
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
            const script    = document.createElement('script');
            script.src      = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload   = () => resolve();
            script.onerror  = () => reject(new Error('Gagal memuat pustaka jsPDF'));
            document.head.appendChild(script);
        });
    }

    // Gabungkan beberapa gambar menjadi satu PDF multi-halaman
    function combineImagesToPdfBlob(pages) {
        return new Promise(async (resolve) => {
            const { jsPDF } = window.jspdf;
            let pdf = null;

            for (let i = 0; i < pages.length; i++) {
                const imgData    = pages[i];
                const dimensions = await getImageDimensions(imgData);

                if (i === 0) {
                    pdf = new jsPDF({
                        orientation: dimensions.width > dimensions.height ? 'landscape' : 'portrait',
                        unit: 'px',
                        format: [dimensions.width, dimensions.height]
                    });
                } else {
                    pdf.addPage([dimensions.width, dimensions.height], dimensions.width > dimensions.height ? 'l' : 'p');
                }
                pdf.addImage(imgData, 'PNG', 0, 0, dimensions.width, dimensions.height);
            }

            resolve(pdf.output('blob'));
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
        const arr   = dataurl.split(',');
        const bstr  = atob(arr[1]);
        let n       = bstr.length;
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
