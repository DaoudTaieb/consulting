// Basic Service Worker to satisfy PWA requirements
self.addEventListener('install', (event) => {
    console.log('Service Worker installed');
});

self.addEventListener('fetch', (event) => {
    // Standard fetch behavior
    event.respondWith(fetch(event.request));
});
