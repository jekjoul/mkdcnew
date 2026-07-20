const fs = require('fs');
const path = require('path');
const http = require('http');
const https = require('https');
const querystring = require('querystring');

const CONFIG_PATH = path.join(__dirname, 'config.json');

// Membaca file konfigurasi
let config = {};
try {
    const rawConfig = fs.readFileSync(CONFIG_PATH, 'utf-8');
    config = JSON.parse(rawConfig);
} catch (err) {
    console.error('\x1b[31m[ERROR] Gagal membaca config.json:\x1b[0m', err.message);
    process.exit(1);
}

const {
    device_ip,
    device_port,
    device_sn,
    server_url,
    api_token,
    sync_interval_seconds,
    delete_device_log_after_sync
} = config;

// Helper untuk log dengan waktu
function log(message, type = 'INFO') {
    const now = new Date().toISOString().replace('T', ' ').substring(0, 19);
    let color = '\x1b[37m'; // White
    if (type === 'SUCCESS') color = '\x1b[32m'; // Green
    if (type === 'ERROR') color = '\x1b[31m'; // Red
    if (type === 'WARN') color = '\x1b[33m'; // Yellow
    console.log(`[${now}] [${type}] ${color}${message}\x1b[0m`);
}

// Helper generic untuk request HTTP/HTTPS
function request(urlStr, options = {}, postData = null) {
    return new Promise((resolve, reject) => {
        const client = urlStr.startsWith('https') ? https : http;
        
        // Parsing URL manual agar kompatibel dengan Node.js versi lama
        const urlObj = new URL(urlStr);
        const requestOptions = {
            hostname: urlObj.hostname,
            port: urlObj.port || (urlStr.startsWith('https') ? 443 : 80),
            path: urlObj.pathname + urlObj.search,
            method: options.method || 'GET',
            headers: options.headers || {}
        };

        if (postData) {
            requestOptions.headers['Content-Length'] = Buffer.byteLength(postData);
        }

        const req = client.request(requestOptions, (res) => {
            let body = '';
            res.on('data', (chunk) => body += chunk);
            res.on('end', () => {
                if (res.statusCode >= 200 && res.statusCode < 300) {
                    resolve(body);
                } else {
                    reject(new Error(`HTTP Status ${res.statusCode}: ${body}`));
                }
            });
        });

        req.on('error', (err) => reject(err));

        if (postData) {
            req.write(postData);
        }
        req.end();
    });
}

// 1. Tarik log presensi baru dari mesin lokal dan kirim ke server online
async function syncScanlog() {
    log('Mulai sinkronisasi scanlog dari mesin...', 'INFO');
    
    const deviceUrl = `http://${device_ip}:${device_port}/scanlog/new`;
    const devicePostData = querystring.stringify({ sn: device_sn });

    try {
        // Ambil log dari mesin
        const deviceRes = await request(deviceUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }, devicePostData);

        const content = JSON.parse(deviceRes);
        if (!content || !content.Data) {
            log('Respons dari mesin tidak valid atau kosong.', 'WARN');
            return;
        }

        const logs = content.Data;
        if (logs.length === 0) {
            log('Tidak ada data scanlog baru di mesin.', 'INFO');
            return;
        }

        log(`Mendapatkan ${logs.length} data scanlog baru dari mesin. Mengirim ke server online...`, 'INFO');

        // Petakan log mesin ke format server
        const serverLogs = logs.map(l => ({
            pin: l.PIN,
            scan_date: l.ScanDate,
            sn: l.SN
        }));

        // Kirim ke server online
        const serverUrl = `${server_url.replace(/\/$/, '')}/api/presensi/sync`;
        const serverPayload = JSON.stringify({
            token: api_token,
            logs: serverLogs
        });

        const serverRes = await request(serverUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }, serverPayload);

        const serverResponse = JSON.parse(serverRes);
        if (serverResponse.status === 'success') {
            log(`Berhasil mengirimkan data presensi ke server online! Terproses: ${serverResponse.processed}, Diabaikan: ${serverResponse.ignored}`, 'SUCCESS');

            // Opsional: Hapus log di mesin setelah berhasil dikirim agar memori tidak penuh
            if (delete_device_log_after_sync) {
                log('Menghapus log yang tersinkron di mesin...', 'INFO');
                const deleteUrl = `http://${device_ip}:${device_port}/scanlog/del`;
                await request(deleteUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                }, devicePostData);
                log('Log berhasil dibersihkan dari mesin.', 'SUCCESS');
            }
        } else {
            log('Gagal menyimpan data di server online: ' + serverResponse.message, 'ERROR');
        }

    } catch (err) {
        log('Error pada sinkronisasi scanlog: ' + err.message, 'ERROR');
    }
}

// 2. Tarik tugas pending dari server online dan terapkan ke mesin lokal
async function syncTasks() {
    log('Memeriksa tugas sinkronisasi dari server online...', 'INFO');

    try {
        const getTasksUrl = `${server_url.replace(/\/$/, '')}/api/presensi/pending_tasks?token=${api_token}`;
        const serverRes = await request(getTasksUrl, { method: 'GET' });

        const serverResponse = JSON.parse(serverRes);
        if (serverResponse.status !== 'success') {
            log('Gagal mengambil tugas dari server: ' + serverResponse.message, 'ERROR');
            return;
        }

        const tasks = serverResponse.tasks || [];
        if (tasks.length === 0) {
            log('Tidak ada tugas sinkronisasi pending.', 'INFO');
            return;
        }

        log(`Mendapatkan ${tasks.length} tugas pending. Mulai memproses...`, 'INFO');

        for (const task of tasks) {
            const taskId = task.id;
            const action = task.action;
            const pin = task.pin;
            const nama = task.nama;

            let success = false;
            let errorMessage = null;

            try {
                if (action === 'SET_USER') {
                    // Daftarkan/update user ke mesin lokal
                    const setUrl = `http://${device_ip}:${device_port}/user/set`;
                    const setPayload = querystring.stringify({
                        sn: device_sn,
                        pin: pin,
                        nama: nama,
                        pwd: '',
                        rfid: '',
                        priv: 0,
                        tmp: '[]' // Kirim array kosong untuk sidik jari, nanti didaftarkan di mesin fisik
                    });

                    log(`Menjalankan SET_USER ke mesin (PIN: ${pin}, Nama: ${nama})...`, 'INFO');
                    const res = await request(setUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    }, setPayload);

                    const content = JSON.parse(res);
                    if (content && content.Result === true) {
                        success = true;
                        log(`Berhasil mendaftarkan user ${nama} (PIN: ${pin}) ke mesin.`, 'SUCCESS');
                    } else {
                        throw new Error(JSON.stringify(content));
                    }

                } else if (action === 'DEL_USER') {
                    // Hapus user dari mesin lokal
                    const delUrl = `http://${device_ip}:${device_port}/user/del`;
                    const delPayload = querystring.stringify({
                        sn: device_sn,
                        pin: pin
                    });

                    log(`Menjalankan DEL_USER ke mesin (PIN: ${pin})...`, 'INFO');
                    const res = await request(delUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    }, delPayload);

                    const content = JSON.parse(res);
                    if (content && content.Result === true) {
                        success = true;
                        log(`Berhasil menghapus user PIN ${pin} dari mesin.`, 'SUCCESS');
                    } else {
                        throw new Error(JSON.stringify(content));
                    }
                }
            } catch (err) {
                errorMessage = err.message;
                log(`Gagal memproses tugas ${action} (PIN: ${pin}): ${errorMessage}`, 'ERROR');
            }

            // Kirim report hasil eksekusi kembali ke server online
            try {
                const reportUrl = `${server_url.replace(/\/$/, '')}/api/presensi/task_result`;
                const reportPayload = JSON.stringify({
                    token: api_token,
                    task_id: taskId,
                    status: success ? 'success' : 'failed',
                    error_message: errorMessage
                });

                await request(reportUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                }, reportPayload);
                log(`Laporan tugas ${taskId} berhasil dikirim ke server.`, 'INFO');
            } catch (err) {
                log(`Gagal mengirim laporan tugas ${taskId} ke server: ${err.message}`, 'ERROR');
            }
        }

    } catch (err) {
        log('Error pada sinkronisasi tugas: ' + err.message, 'ERROR');
    }
}

// Fungsi utama loop scheduler
async function main() {
    console.log('=====================================================');
    console.log('    MKDC FINGERPRINT BRIDGE RUNNING (2-WAY SYNC)    ');
    console.log(`    IP Mesin    : ${device_ip}:${device_port}`);
    console.log(`    SN Mesin    : ${device_sn}`);
    console.log(`    URL Server  : ${server_url}`);
    console.log(`    Interval    : ${sync_interval_seconds} detik`);
    console.log('=====================================================');

    // Jalankan sekali saat start
    await syncScanlog();
    await syncTasks();

    // Jalankan periodik
    setInterval(async () => {
        await syncScanlog();
        await syncTasks();
    }, sync_interval_seconds * 1000);
}

main();
