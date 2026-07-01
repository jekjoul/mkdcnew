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
        $('input[type="file"]').each(function() {
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
                                <label class="form-label fw-semibold">Format Output:</label>
                                <select id="swal-scanner-format" class="form-select">
                                    <option value="jpg" selected>JPEG (Rekomendasi - Lebih Ringan)</option>
                                    <option value="png">PNG (Transparansi & Kualitas Tinggi)</option>
                                    <option value="pdf">PDF (Dokumen Portable Document Format)</option>
                                </select>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Mulai Scan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const formatVal = document.getElementById('swal-scanner-format').value;
                            return {
                                deviceId: document.getElementById('swal-scanner-device').value,
                                format: formatVal === 'pdf' ? 'png' : formatVal,
                                convertToPdf: formatVal === 'pdf'
                            }
                        }
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memindai...',
                                text: 'Mohon tunggu, scanner sedang memproses dokumen Anda...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const scanRes = await fetch(`${SCANNER_API_URL}/scan`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    deviceId: result.value.deviceId,
                                    format: result.value.format
                                })
                            });

                            const scanData = await scanRes.json();
                            if (scanData.success && scanData.base64) {
                                let file;
                                
                                if (result.value.convertToPdf) {
                                    // Load jsPDF dynamically from CDN
                                    await loadJsPDF();
                                    
                                    // Convert scanned image to PDF Blob
                                    const pdfBlob = await imageToPdfBlob(`data:${scanData.mime};base64,${scanData.base64}`, scanData.format);
                                    const filename = `scanned_document_${Date.now()}.pdf`;
                                    file = new File([pdfBlob], filename, { type: 'application/pdf' });
                                } else {
                                    // Convert base64 to image File object
                                    const mime = scanData.mime;
                                    const filename = `scanned_document_${Date.now()}.${scanData.format}`;
                                    file = dataURLtoFile(`data:${mime};base64,${scanData.base64}`, filename);
                                }

                                // Put the file into the file input
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                input[0].files = dataTransfer.files;
                                
                                // Trigger change event on input so other scripts know a file is uploaded
                                input.trigger('change');

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Scan Berhasil',
                                    text: 'Dokumen berhasil dipindai dan dimasukkan ke form upload.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                throw new Error(scanData.details || 'Gagal memproses gambar');
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

    // Convert Image Data URL to PDF Blob
    function imageToPdfBlob(dataUrl, imgFormat) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = dataUrl;
            img.onload = function() {
                const { jsPDF } = window.jspdf;
                // Create PDF matches image orientation & dimension
                const pdf = new jsPDF({
                    orientation: img.width > img.height ? 'landscape' : 'portrait',
                    unit: 'px',
                    format: [img.width, img.height]
                });
                pdf.addImage(dataUrl, imgFormat.toUpperCase(), 0, 0, img.width, img.height);
                resolve(pdf.output('blob'));
            };
        });
    }

    // Helper to convert base64 to File object
    function dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, {type:mime});
    }

    // Run on startup
    setTimeout(attachScanButtons, 1000);
    
    // Also re-run when bootstrap modals open or dynamic forms load
    $(document).on('shown.bs.modal', function () {
        attachScanButtons();
    });
});
