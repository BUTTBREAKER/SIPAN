// SIPAN - Sistema Integral para Panaderías
// JavaScript principal con Alpine.js

// Configuración global de Alpine.js
document.addEventListener('alpine:init', () => {
    // Store global para notificaciones
    Alpine.store('notifications', {
        items: [],
        count: 0,
        
        async load() {
            try {
                const response = await fetch('/dashboard/notificaciones');
                const data = await response.json();
                if (data.success) {
                    this.items = data.notificaciones;
                    this.count = data.total;
                }
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            }
        },
        
        async marcarLeida(id) {
            try {
                const formData = new FormData();
                formData.append('id', id);
                
                const response = await fetch('/dashboard/notificacion/leida', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    this.items = this.items.filter(item => item.id !== id);
                    this.count = this.items.length;
                }
            } catch (error) {
                console.error('Error al marcar notificación:', error);
            }
        }
    });
});

// Funciones auxiliares
const SIPAN = {
    // Mostrar alerta de éxito
    success(message, title = 'Éxito') {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonColor: '#D4A574',
            timer: 3000,
            timerProgressBar: true
        });
    },
    
    // Mostrar alerta de error
    error(message, title = 'Error') {
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonColor: '#D4A574'
        });
    },
    
    // Mostrar alerta de advertencia
    warning(message, title = 'Advertencia') {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            confirmButtonColor: '#D4A574'
        });
    },
    
    // Mostrar alerta de información
    info(message, title = 'Información') {
        Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            confirmButtonColor: '#D4A574'
        });
    },
    
    // Confirmar acción
    async confirm(message, title = '¿Estás seguro?') {
        const result = await Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D4A574',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        });
        return result.isConfirmed;
    },
    
    // Alerta de stock bajo
    stockBajo(producto, stockActual, stockMinimo) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock Bajo',
            html: `
                <p><strong>${producto}</strong></p>
                <p>Stock actual: <span class="badge badge-danger">${stockActual}</span></p>
                <p>Stock mínimo: <span class="badge badge-warning">${stockMinimo}</span></p>
                <p class="mt-2">Se recomienda reabastecer este producto.</p>
            `,
            confirmButtonColor: '#D4A574',
            confirmButtonText: 'Entendido'
        });
    },
    
    // Formatear moneda
    formatMoney(amount) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN'
        }).format(amount);
    },
    
    // Formatear fecha
    formatDate(date) {
        return new Intl.DateTimeFormat('es-PE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(new Date(date));
    },
    
    // Formatear fecha y hora
    formatDateTime(date) {
        return new Intl.DateTimeFormat('es-PE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    },
    
    // Enviar formulario con AJAX
    async submitForm(formElement, successCallback) {
        const formData = new FormData(formElement);
        
        try {
            const response = await fetch(formElement.action, {
                method: formElement.method || 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.success(data.message);
                if (successCallback) successCallback(data);
            } else {
                this.error(data.message);
            }
            
            return data;
        } catch (error) {
            this.error('Error al procesar la solicitud');
            console.error('Error:', error);
            return { success: false, error };
        }
    },
    
    // Eliminar registro
    async deleteRecord(url, successCallback) {
        const confirmed = await this.confirm(
            'Esta acción no se puede deshacer',
            '¿Eliminar registro?'
        );
        
        if (!confirmed) return;
        
        try {
            const response = await fetch(url, {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.success(data.message);
                if (successCallback) successCallback(data);
            } else {
                this.error(data.message);
            }
            
            return data;
        } catch (error) {
            this.error('Error al eliminar el registro');
            console.error('Error:', error);
            return { success: false, error };
        }
    },
    
    // Buscar con debounce
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Validar formulario
    validateForm(formElement) {
        const inputs = formElement.querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    },
    
    // Cargar productos para venta/pedido
    async buscarProductos(query) {
        try {
            const response = await fetch(`/productos/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            return data.success ? data.productos : [];
        } catch (error) {
            console.error('Error al buscar productos:', error);
            return [];
        }
    },
    
    // Cargar clientes
    async buscarClientes(query) {
        try {
            const response = await fetch(`/clientes/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            return data.success ? data.clientes : [];
        } catch (error) {
            console.error('Error al buscar clientes:', error);
            return [];
        }
    },
    
    // Verificar stock bajo automáticamente
    async verificarStockBajo() {
        try {
            const response = await fetch('/productos/stock-bajo');
            const data = await response.json();
            
            if (data.success && data.productos.length > 0) {
                data.productos.forEach(producto => {
                    this.stockBajo(producto.nombre, producto.stock_actual, producto.stock_minimo);
                });
            }
        } catch (error) {
            console.error('Error al verificar stock:', error);
        }
    }
};

// Hacer SIPAN global
window.SIPAN = SIPAN;

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('SIPAN - Sistema Integral para Panaderías');
    
    // Cargar notificaciones si existe el store
    if (Alpine.store('notifications')) {
        Alpine.store('notifications').load();
    }
    
    // Actualizar notificaciones cada 30 segundos
    setInterval(() => {
        if (Alpine.store('notifications')) {
            Alpine.store('notifications').load();
        }
    }, 30000);
});
