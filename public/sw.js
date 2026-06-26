/* Family Quran Tracker — minimal offline shell service worker */
const CACHE = 'fqt-v1';
const SHELL = ['/offline.html'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((c) => c.addAll(SHELL)).catch(() => {}));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET navigations for offline fallback; let everything else pass through.
    if (request.method !== 'GET') return;

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Cache-first for static build assets.
    if (request.url.includes('/build/') || request.url.includes('/icons/')) {
        event.respondWith(
            caches.match(request).then((cached) =>
                cached || fetch(request).then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                    return res;
                })
            )
        );
    }
});
