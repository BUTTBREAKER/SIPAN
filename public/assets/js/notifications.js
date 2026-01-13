class NotificationManager {
    constructor() {
        this.permission = null;
    }

    async init() {
        if (!("Notification" in window)) {
            console.log(
                "Este navegador no soporta notificaciones de escritorio",
            );
            return;
        }

        if ("serviceWorker" in navigator) {
            try {
                const registration =
                    await navigator.serviceWorker.register("/sw.js");
                console.log(
                    "Service Worker registrado con éxito:",
                    registration,
                );
            } catch (error) {
                console.log("Fallo al registrar Service Worker:", error);
            }
        }

        this.permission = Notification.permission;
        if (this.permission === "default") {
            await this.requestPermission();
        }
    }

    async requestPermission() {
        const permission = await Notification.requestPermission();
        this.permission = permission;
        if (permission === "granted") {
            console.log("Permiso de notificación concedido");
            // Aquí podríamos suscribir al usuario a un Push Service real
        }
    }

    showNotification(title, body) {
        if (this.permission === "granted") {
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.ready.then((registration) => {
                    registration.showNotification(title, {
                        body: body,
                        icon: "/assets/img/logo.png", // Ajusta ruta
                        vibrate: [200, 100, 200],
                    });
                });
            } else {
                new Notification(title, {
                    body: body,
                    icon: "/assets/img/logo.png",
                });
            }
        }
    }
}

window.notificationManager = new NotificationManager();
window.notificationManager.init();
