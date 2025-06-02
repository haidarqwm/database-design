const CACHE_NAME = 'my-cache-v1';

const urlsToCache = [
    '/',
    '/index.html',
    '/styles.css',
    '/script.js',
    '/IMG_4283.jpg',
    'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap',
    'https://unpkg.com/@zxing/library@latest'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Membuka cache');
                return Promise.all(
                    urlsToCache.map(function(url) {
                        return fetch(url).then(function(response) {
                            if (!response.ok) {
                                throw new Error('Gagal mengambil ' + url);
                            }
                            return cache.put(url, response);
                        }).catch(function(error) {
                            console.error('Gagal mengambil dan menyimpan ke cache:', error);
                        });
                    })
                );
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                if (response) {
                    return response; // Jika ada di cache, kembalikan dari cache
                }
                return fetch(event.request); // Jika tidak, ambil dari jaringan
            })
    );
});

self.addEventListener('activate', function(event) {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
