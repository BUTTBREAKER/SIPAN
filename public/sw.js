self.addEventListener('push', event => {
    if (event.data) {
        const data = event.data.json()
        const options = {
            badge: '/assets/img/badge-72.png',
            body: data.body,
            data: {
                dateOfArrival: Date.now(),
                primaryKey: '2',
            },
            icon: '/assets/img/icon-192.png', // Asegúrate de tener este icono
            vibrate: [100, 50, 100],
        }
        event.waitUntil(self.registration.showNotification(data.title, options))
    }
})

self.addEventListener('notificationclick', event => {
    event.notification.close()
    event.waitUntil(clients.openWindow('/'))
})
