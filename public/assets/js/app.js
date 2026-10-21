/**
 * SIPAN - Application Logic
 * Handles Sidebar toggle, persistence, and mobile interactions.
 */

// Global Helpers (SIPAN Object) - Defined outside to be available immediately
window.SIPAN = {
    confirm: (title, text, callback, confirmText = 'Sí, continuar') => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                confirmButtonText: confirmText,
                icon: 'warning',
                showCancelButton: true,
                text: text,
                title: title,
            }).then(result => {
                if (result.isConfirmed) {
                    callback()
                }
            })
        } else {
            if (confirm(title + '\n' + text)) {
                callback()
            }
        }
    },
    error: message => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                confirmButtonColor: '#dc3545',
                icon: 'error',
                text: message,
                title: 'Error',
            })
        } else {
            console.error('ERROR:', message)
            alert('Error: ' + message)
        }
    },
    formatDNI: value => {
        if (!value) return ''
        const firstChar = value.charAt(0).toUpperCase()
        if (!['V', 'E', 'J', 'G'].includes(firstChar)) {
            return ''
        }
        const digits = value.slice(1).replace(/\D/g, '')
        // Siempre incluir guion tras la letra, incluso si no hay dígitos aún
        return firstChar + '-' + digits.slice(0, 9)
    },
    formatPhone: value => {
        if (!value) return ''
        const digits = value.replace(/\D/g, '')
        if (digits.length > 4) {
            return digits.slice(0, 4) + '-' + digits.slice(4, 11)
        }
        if (digits.length === 4) {
            return digits + '-'
        }
        return digits
    },
    // Requires SweetAlert2 (Swal) to be loaded globally
    success: message => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                confirmButtonColor: '#28a745',
                icon: 'success',
                text: message,
                timer: 2000,
                timerProgressBar: true,
                title: '¡Éxito!',
            })
        } else {
            console.log('SUCCESS:', message)
            alert(message)
        }
    },
    warning: message => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Entendido',
                icon: 'warning',
                text: message,
                title: 'Atención',
            })
        } else {
            console.warn('WARNING:', message)
            alert('Aviso: ' + message)
        }
    },
    validateForm: form => {
        if (!form) return false
        if (typeof form.checkValidity === 'function') {
            if (!form.checkValidity()) {
                if (typeof form.reportValidity === 'function') form.reportValidity()
                return false
            }
            return true
        }
        const required = form.querySelectorAll('[required]')
        for (let el of required) {
            if (!el.value || !el.value.trim()) {
                el.focus()
                return false
            }
        }
        return true
    },
    submitForm: async (form, onSuccess = null) => {
        if (!form) return null
        const url = form.getAttribute('action') || window.location.pathname
        const method = (form.getAttribute('method') || 'POST').toUpperCase()
        const formData = new FormData(form)

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })

            const data = await response.json()

            if (data.success) {
                if (typeof onSuccess === 'function') {
                    onSuccess(data)
                } else {
                    SIPAN.success(data.message || 'Guardado exitosamente')
                }
            } else {
                SIPAN.error(data.message || 'Error al guardar los datos')
            }
            return data
        } catch (err) {
            console.error('Error al enviar formulario:', err)
            SIPAN.error('Error de conexión o respuesta inesperada del servidor')
            return { success: false, message: err.message }
        }
    },
}

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('appSidebar')
    const toggleBtn = document.getElementById('sidebarToggle')
    const toggleHeaderBtn = document.getElementById('sidebarToggleHeader')
    const mobileToggleBtn = document.getElementById('sidebarToggleMobile')
    const overlay = document.getElementById('sidebarOverlay')

    if (sidebar) {
        const isExpanded = localStorage.getItem('sidebar-state') === 'expanded'
        if (isExpanded) {
            sidebar.classList.add('expanded')
        }
    }

    function toggleSidebar() {
        if (!sidebar) return
        const isNowExpanded = sidebar.classList.toggle('expanded')
        sidebar.classList.toggle('active', isNowExpanded)
        localStorage.setItem(
            'sidebar-state',
            isNowExpanded ? 'expanded' : 'collapsed',
        )
        if (window.innerWidth <= 991) {
            if (overlay) overlay.classList.toggle('active', isNowExpanded)
            document.body.style.overflow = isNowExpanded ? 'hidden' : ''
        }
        window.dispatchEvent(new Event('resize'))
    }

    if (toggleBtn)
        toggleBtn.addEventListener('click', e => {
            e.stopPropagation()
            toggleSidebar()
        })
    if (toggleHeaderBtn)
        toggleHeaderBtn.addEventListener('click', e => {
            e.stopPropagation()
            toggleSidebar()
        })
    if (mobileToggleBtn)
        mobileToggleBtn.addEventListener('click', e => {
            e.stopPropagation()
            toggleSidebar()
        })
    if (overlay)
        overlay.addEventListener('click', () => {
            if (sidebar.classList.contains('expanded')) toggleSidebar()
        })

    document.addEventListener('click', e => {
        if (sidebar && window.innerWidth <= 991) {
            if (
                sidebar.classList.contains('expanded') &&
                !sidebar.contains(e.target) &&
                (!toggleHeaderBtn || !toggleHeaderBtn.contains(e.target)) &&
                (!mobileToggleBtn || !mobileToggleBtn.contains(e.target))
            ) {
                sidebar.classList.remove('expanded')
                if (overlay) overlay.classList.remove('active')
                document.body.style.overflow = ''
            }
        }
    })
})
