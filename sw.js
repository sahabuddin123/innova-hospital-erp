// LifeCare Diagnostic Center - Service Worker (PWA & Offline Engine)
const CACHE_NAME = 'lifecare-pwa-v2.0';

const PRECACHE_ASSETS = [
    './offline.html',
    './manifest.json',
    './uploads/pwa-icon-192.png',
    './uploads/pwa-icon-512.png',
    './uploads/lifecare_logo_circle.png',
    './adminlte/dist/css/adminlte.min.css',
    './adminlte/plugins/fontawesome-free/css/all.min.css',
    './application/assets/css/enhanced-components-styles.css',
    './common/assets/tinymce/tinymce.min.js',
    './common/extranal/css/lab/add_template.css',
    './common/extranal/css/lab/invoice.css',
    './common/extranal/js/lab/add_lab_view.js',
    './common/extranal/js/lab/add_template_view.js',
    './common/extranal/js/lab/template.js',
    './common/extranal/css/finance/expense_invoice.css',
    './common/extranal/js/pwa_offline.js'
];

// Install Event: Pre-cache core shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA SW] Pre-caching partial items:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event: Clean old cache versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event: Network-first for pages, Cache-first for assets
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Ignore cross-origin non-http schemes (e.g. chrome-extension)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // 1. Navigation Requests (HTML Pages)
    if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Update cache with fresh copy of the page
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Offline fallback: try cache, else offline.html
                    return caches.match(request).then((cachedResponse) => {
                        return cachedResponse || caches.match('./offline.html');
                    });
                })
        );
        return;
    }

    // 2. Static Assets (CSS, JS, Fonts, Images)
    const isStaticAsset = url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|gif|woff|woff2|ttf|eot|ico)$/i);
    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Return cached asset & fetch in background to update cache (Stale-While-Revalidate)
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, networkResponse);
                            });
                        }
                    }).catch(() => {/* Ignore network error for background refresh */});
                    return cachedResponse;
                }
                // Not in cache: fetch from network & put in cache
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // 3. All other requests: Network first with cache fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});

// Background Sync Listener (Triggered automatically when online)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-offline-invoices') {
        event.waitUntil(
            self.clients.matchAll().then((clients) => {
                clients.forEach((client) => {
                    client.postMessage({ type: 'TRIGGER_BACKGROUND_SYNC' });
                });
            })
        );
    }
});
