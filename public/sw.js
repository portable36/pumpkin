self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('pumpkin-v1').then(cache => {
            return cache.addAll([
                '/',
                '/css/app.css',
                '/js/app.js',
                '/offline.html',
            ]);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== 'pumpkin-v1') {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', event => {
    // Cache-first strategy for assets
    if (event.request.method === 'GET' && 
        (event.request.url.includes('.css') || 
         event.request.url.includes('.js') || 
         event.request.url.includes('.woff'))) {
        return event.respondWith(
            caches.match(event.request).then(response => {
                return response || fetch(event.request).then(response => {
                    return caches.open('pumpkin-v1').then(cache => {
                        cache.put(event.request, response.clone());
                        return response;
                    });
                });
            }).catch(() => caches.match('/offline.html'))
        );
    }

    // Network-first strategy for API
    event.respondWith(
        fetch(event.request)
            .then(response => {
                if (event.request.method === 'GET') {
                    caches.open('pumpkin-v1').then(cache => {
                        cache.put(event.request, response.clone());
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request) || caches.match('/offline.html');
            })
    );
});

// Handle push notifications
self.addEventListener('push', event => {
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/images/icon-192x192.png',
        badge: '/images/badge-72x72.png',
        tag: 'notification',
    };

    event.waitUntil(
        self.registration.showNotification('Pumpkin', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(clientList => {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === '/' && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow('/');
            }
        })
    );
});
