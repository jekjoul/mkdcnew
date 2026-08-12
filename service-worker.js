/**
 * MKDC Service Worker
 * Tujuan: Menampilkan halaman offline.html yang sudah di-cache
 *         ketika browser tidak bisa menjangkau server.
 *
 * Versi cache dinaikkan setiap kali ada perubahan signifikan.
 */

const CACHE_NAME   = 'mkdc-offline-v1';
const OFFLINE_URL  = '/mkdcnew/offline.html';

/* ── Assets yang di-cache saat install ───────────────────────────────── */
const PRECACHE_ASSETS = [
    OFFLINE_URL,
];

/* ── Install: cache halaman offline ─────────────────────────────────── */
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_ASSETS))
    );
    // Aktifkan SW segera tanpa menunggu halaman lama ditutup
    self.skipWaiting();
});

/* ── Activate: hapus cache lama ─────────────────────────────────────── */
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        )
    );
    // Klaim semua klien (tab) yang sudah terbuka
    self.clients.claim();
});

/* ── Fetch: intersep request navigasi ───────────────────────────────── */
self.addEventListener('fetch', event => {
    // Hanya tangani request navigasi (buka halaman baru / reload)
    // Bukan: fetch AJAX, gambar, CSS, JS, dll.
    if (event.request.mode !== 'navigate') return;

    event.respondWith(
        fetch(event.request)
            .catch(() =>
                // Server tidak bisa dijangkau → kembalikan halaman offline dari cache
                caches.match(OFFLINE_URL)
            )
    );
});
