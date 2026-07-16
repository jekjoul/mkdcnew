// Scan Plugin for MKDC Web
$(document).ready(function() {
    const SCANNER_API_URL = 'http://127.0.0.1:7999';

    // Check if scanner bridge is running
    function checkScannerBridge() {
        return fetch(SCANNER_API_URL)
            .then(res => res.json())
            .then(data => data.status === 'running')
            .catch(() => false);
    }

    // Attach "Scan Direct" button to input[type="file"]
    function attachScanButtons() {
        $('input[type="file"].scan-enabled').each(function() {
            const input = $(this);
            // Don't add twice
            if (input.parent().find('.btn-direct-scan').length > 0) return;
            
            // We only attach to inputs that accept images or pdf/documents
            const accept = input.attr('accept') || '';
            if (accept && !accept.includes('image') && !accept.includes('pdf') && !accept.includes('*')) {
                return; 
            }

            // Create a scan button next to the file input
            const scanBtn = $('<button type="button" class="btn btn-sm btn-info-600 radius-8 px-12 py-6 ms-2 btn-direct-scan d-inline-flex align-items-center gap-1"><iconify-icon icon="lucide:scan"></iconify-icon> Scan Langsung</button>');
            
            // Insert button after input
            input.after(scanBtn);

            scanBtn.on('click', async function(e) {
                e.preventDefault();
                
                // Show checking alert
                Swal.fire({
                    title: 'Menghubungkan ke Scanner...',
                    text: 'Pastikan software MKDC Scanner Bridge dan scanner Epson L3210 Anda menyala.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
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

                // Bridge is running! Fetch scanner devices
                try {
                    const devicesRes = await fetch(`${SCANNER_API_URL}/devices`);
                    let devices = await devicesRes.json();

                    // PowerShell ConvertTo-Json quirk: single object returned instead of array if there is only 1 device
                    if (devices && !Array.isArray(devices)) {
                        devices = [devices];
                    }

                    if (!devices || devices.length === 0 || (devices.length === 1 && !devices[0])) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alat Scanner Tidak Ditemukan',
                            text: 'Bridge berjalan, tetapi tidak ada scanner fisik yang terhubung ke komputer. Pastikan kabel USB scanner Epson Anda terpasang.'
                        });
                        return;
                    }

                    // Open scanner selection and start scan modal
                    let deviceOptionsHtml = '';
                    devices.forEach((dev, idx) => {
                        deviceOptionsHtml += `<option value="${dev.id}" ${idx === 0 ? 'selected' : ''}>${dev.name} (${dev.description || 'WIA Scanner'})</option>`;
                    });

                    Swal.fire({
                        title: 'Mulai Memindai (Scan)',
                        html: `
                            <div class="text-start mb-3">
                                <label class="form-label fw-semibold">Pilih Alat Scanner:</label>
                                <select id="swal-scanner-device" class="form-select">${deviceOptionsHtml}</select>
                            </div>
                            <div class="text-start mb-3">
                                <label class="form-label fw-semibold">Format & Metode Scan:</label>
                                <select id="swal-scanner-format" class="form-select">
                                    <option value="jpg" selected>JPEG (Gambar Tunggal)</option>
                                    <option value="png">PNG (Gambar Tunggal - Kualitas Tinggi)</option>
                                    <option value="pdf">PDF (Dokumen 1 Halaman)</option>
                                    <option value="pdf_multi">PDF Multi Halaman (Scan Berturut-turut)</option>
                                </select>
                            </div>
                            <div class="alert bg-info-focus text-info-main border border-info-200 px-12 py-8 radius-8 text-start text-xs">
                                <iconify-icon icon="lucide:info" class="me-1"></iconify-icon>
                                Ukuran diatur otomatis ke <strong>A4</strong>. Kontras & Gamma ditingkatkan <strong>+10%</strong> untuk mencerahkan dokumen.
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Mulai Scan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const formatVal = document.getElementById('swal-scanner-format').value;
                            return {
                                deviceId: document.getElementById('swal-scanner-device').value,
                                format: formatVal,
                                convertToPdf: formatVal === 'pdf' || formatVal === 'pdf_multi',
                                isMultiPage: formatVal === 'pdf_multi'
                            }
                        }
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            const params = result.value;
                            const scannedPages = []; // Array of Base64 (PNG data URLs)
                            
                            // Start scanning loop
                            let scanMore = true;
                            let pageNum = 1;

                            while (scanMore) {
                                Swal.fire({
                                    title: params.isMultiPage ? `Memindai Halaman ${pageNum}...` : 'Memindai...',
                                    text: 'Mohon tunggu, scanner sedang memproses dokumen Anda...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                try {
                                    // Send scan command (bridge always scans as PNG for high-quality intermediate)
                                    const scanRes = await fetch(`${SCANNER_API_URL}/scan`, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            deviceId: params.deviceId,
                                            format: 'png' // High quality format for processing
                                        })
                                    });

                                    const scanData = await scanRes.json();
                                    if (!scanData.success || !scanData.base64) {
                                        throw new Error(scanData.details || 'Gagal memproses gambar dari scanner.');
                                    }

                                    // Apply +10% Contrast and +10% Gamma adjustment
                                    const rawDataUrl = `data:${scanData.mime};base64,${scanData.base64}`;
                                    const adjustedDataUrl = await adjustContrastAndGamma(rawDataUrl);
                                    
                                    // Save page
                                    scannedPages.push(adjustedDataUrl);

                                    if (params.isMultiPage) {
                                        // Ask for next page
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
                                        // Single page scan completes immediately
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

                            // Show Preview Modal
                            if (scannedPages.length > 0) {
                                showPreviewModal(scannedPages, params, input);
                            }
                        }
                    });

                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Proses Pindai Gagal',
                        text: 'Terjadi kesalahan saat memproses scan: ' + err.message
                    });
                }
            });
        });
    }

    // Adjust Contrast (+10%) & Gamma (+10%) of scanned image using Canvas
    function adjustContrastAndGamma(dataUrl) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imgData.data;

                // Contrast: +10% is 25.5 on a scale of -255 to 255
                const contrast = 25.5; 
                const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));

                // Gamma: +10% (1.1)
                const gamma = 1.1;
                const gammaCorrection = 1 / gamma;

                for (let i = 0; i < data.length; i += 4) {
                    // Contrast
                    let r = factor * (data[i] - 128) + 128;
                    let g = factor * (data[i+1] - 128) + 128;
                    let b = factor * (data[i+2] - 128) + 128;

                    // Gamma
                    r = 255 * Math.pow(r / 255, gammaCorrection);
                    g = 255 * Math.pow(g / 255, gammaCorrection);
                    b = 255 * Math.pow(b / 255, gammaCorrection);

                    // Clamp values between 0 and 255
                    data[i] = Math.min(255, Math.max(0, r));
                    data[i+1] = Math.min(255, Math.max(0, g));
                    data[i+2] = Math.min(255, Math.max(0, b));
                }

                ctx.putImageData(imgData, 0, 0);
                resolve(canvas.toDataURL('image/png'));
            };
        });
    }

    // Show Preview Modal with Upload and Re-scan actions
    async function showPreviewModal(pages, params, fileInput) {
        let previewHtml = '';
        
        if (pages.length === 1) {
            previewHtml = `
                <div class="text-center border p-2 radius-8 bg-light mb-3" style="max-height: 400px; overflow-y: auto;">
                    <img src="${pages[0]}" class="img-fluid border radius-4 shadow-sm" style="max-height: 380px;">
                </div>
            `;
        } else {
            // Multi-page preview with thumbnails
            let thumbs = '';
            pages.forEach((page, idx) => {
                thumbs += `
                    <div class="col-md-4 col-6 mb-3">
                        <div class="border p-2 radius-8 bg-light text-center h-100 position-relative">
                            <span class="badge bg-primary text-light position-absolute top-0 start-0 m-2">Hal ${idx+1}</span>
                            <img src="${page}" class="img-fluid border radius-4 shadow-sm" style="max-height: 120px;">
                        </div>
                    </div>
                `;
            });
            previewHtml = `
                <div class="row" style="max-height: 400px; overflow-y: auto;">
                    ${thumbs}
                </div>
            `;
        }

        Swal.fire({
            title: 'Preview Hasil Scan',
            html: `
                <div class="text-start mb-3 text-sm text-secondary-light">
                    Jumlah Halaman: <strong>${pages.length} Halaman</strong><br>
                    Format Output Akhir: <strong>${params.isMultiPage ? 'PDF' : params.format.toUpperCase()}</strong>
                </div>
                ${previewHtml}
            `,
            showCancelButton: true,
            confirmButtonText: '<iconify-icon icon="lucide:upload" class="me-1"></iconify-icon> Unggah Berkas',
            cancelButtonText: 'Ulangi / Batal',
            confirmButtonColor: '#10b981', // green
            cancelButtonColor: '#ef4444', // red
            allowOutsideClick: false
        }).then(async (res) => {
            if (res.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Berkas...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let file;
                    if (params.convertToPdf) {
                        // Load jsPDF from CDN
                        await loadJsPDF();
                        
                        // Combine all page images into a single PDF Blob
                        const pdfBlob = await combineImagesToPdfBlob(pages);
                        const filename = `scanned_document_${Date.now()}.pdf`;
                        file = new File([pdfBlob], filename, { type: 'application/pdf' });
                    } else {
                        // Single Image (JPEG/PNG)
                        const mime = params.format === 'png' ? 'image/png' : 'image/jpeg';
                        file = dataURLtoFile(pages[0], `scanned_document_${Date.now()}.${params.format}`, mime);
                    }

                    // Put the final file into the HTML input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput[0].files = dataTransfer.files;
                    
                    // Trigger change event
                    fileInput.trigger('change');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berkas Berhasil Diunggah',
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
        });
    }

    // Dynamic jsPDF Loader
    function loadJsPDF() {
        return new Promise((resolve, reject) => {
            if (window.jspdf) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Gagal memuat pustaka jsPDF'));
            document.head.appendChild(script);
        });
    }

    // Combine multiple page images into a single multi-page PDF Blob
    function combineImagesToPdfBlob(pages) {
        return new Promise(async (resolve) => {
            const { jsPDF } = window.jspdf;
            let pdf = null;

            for (let i = 0; i < pages.length; i++) {
                const imgData = pages[i];
                const dimensions = await getImageDimensions(imgData);

                if (i === 0) {
                    // Initialize PDF with the first page dimensions
                    pdf = new jsPDF({
                        orientation: dimensions.width > dimensions.height ? 'landscape' : 'portrait',
                        unit: 'px',
                        format: [dimensions.width, dimensions.height]
                    });
                } else {
                    // Add subsequent pages
                    pdf.addPage([dimensions.width, dimensions.height], dimensions.width > dimensions.height ? 'l' : 'p');
                }
                
                pdf.addImage(imgData, 'PNG', 0, 0, dimensions.width, dimensions.height);
            }
            
            resolve(pdf.output('blob'));
        });
    }

    // Helper to get image width and height
    function getImageDimensions(dataUrl) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function() {
                resolve({ width: img.width, height: img.height });
            };
        });
    }

    // Helper to convert dataURL to File object
    function dataURLtoFile(dataurl, filename, mimeType) {
        var arr = dataurl.split(','),
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, {type: mimeType});
    }

    // Run on startup
    setTimeout(attachScanButtons, 1000);
    
    // Re-run on modal events
    $(document).on('shown.bs.modal', function () {
        attachScanButtons();
    });
});
