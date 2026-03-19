// sipan-delivery-v1.1
const CACHE_NAME = 'sipan-delivery-cache-v1.1';
const urlsToCache = [
  '/delivery/',
  '/delivery/login',
  '/assets/delivery/css/delivery.css',
  '/assets/delivery/js/delivery.js',
  '/delivery-manifest.json',
  '/assets/delivery/icons/icon-192x192.png',
  '/assets/delivery/icons/icon-512x512.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Cache each URL individually so one failure doesn't break everything
        return Promise.allSettled(
          urlsToCache.map(url => cache.add(url).catch(err => {
            console.warn('SW: Failed to cache', url, err);
          }))
        );
      })
  );
});

self.addEventListener('fetch', event => {
  // Solo interceptar peticiones GET
  if (event.request.method !== 'GET') {
      return; // El navegador maneja las peticiones POST, PUT, DELETE normalmente
  }
  
  // Ignorar URLs que no sean de nuestra app (ej. extensiones de Chrome)
  if (!event.request.url.startsWith('http')) {
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
