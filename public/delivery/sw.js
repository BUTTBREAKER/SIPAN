// sipan-delivery-v1
const CACHE_NAME = 'sipan-delivery-cache-v1';
const urlsToCache = [
  '/delivery/',
  '/delivery/login',
  '/delivery/assets/css/delivery.css',
  '/delivery/assets/js/delivery.js',
  '/delivery/manifest.json',
  '/delivery/assets/icons/icon-192x192.png',
  '/delivery/assets/icons/icon-512x512.png',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  // Para las llamadas a la API o al backend, no usamos cache primero, 
  // usamos network falling back to cache
  if (event.request.method !== 'GET') {
      return;
  }
  
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
