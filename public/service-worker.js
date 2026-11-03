const CACHE_NAME = 'korteo-v3'; // Versión nueva para forzar limpieza en los navegadores

self.addEventListener('install', (event) => {
    self.skipWaiting(); // Fuerza la actualización inmediata
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName); // Borra todas las cachés viejas (incluyendo las que dan error 419)
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', (event) => {
    // REGLA DE ORO: Si la petición va al sistema (/app), es un POST, o NO es GET... NO HACER NADA.
    if (event.request.method !== 'GET' || event.request.url.includes('/app/')) {
        return; // Deja que el navegador haga la petición directa a Laravel sin caché
    }

    // Solo guardamos en caché el manifest y archivos estáticos
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});