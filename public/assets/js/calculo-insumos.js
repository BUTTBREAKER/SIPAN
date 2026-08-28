// Componente Alpine.js para cálculo de insumos
function calculoInsumosModal() {
    return {
        abrirModal() {
            this.showModal = true
            this.resultado = null
            this.formData = {
                cantidad_producir: '',
                id_receta: '',
            }
        },

        async calcular() {
            if (!this.formData.id_receta || !this.formData.cantidad_producir) {
                Swal.fire({
                    confirmButtonColor: '#D4A574',
                    icon: 'error',
                    text: 'Complete todos los campos',
                    title: 'Error',
                })
                return
            }

            this.loading = true

            try {
                const response = await fetch('/calculo-insumos/calcular', {
                    body: JSON.stringify(this.formData),
                    headers: { 'Content-Type': 'application/json' },
                    method: 'POST',
                })

                const data = await response.json()

                if (data.success) {
                    this.resultado = data

                    if (!data.puede_producir) {
                        Swal.fire({
                            confirmButtonColor: '#D4A574',
                            icon: 'warning',
                            text: `Faltan ${data.insumos_faltantes.length} insumos para completar la producción`,
                            title: 'Insumos Insuficientes',
                        })
                    }
                } else {
                    Swal.fire({
                        confirmButtonColor: '#D4A574',
                        icon: 'error',
                        text: data.message,
                        title: 'Error',
                    })
                }
            } catch (error) {
                Swal.fire({
                    confirmButtonColor: '#D4A574',
                    icon: 'error',
                    text: 'Error de conexión',
                    title: 'Error',
                })
            } finally {
                this.loading = false
            }
        },

        async cargarRecetas() {
            try {
                const response = await fetch('/recetas/list')
                const data = await response.json()
                if (data.success) {
                    this.recetas = data.recetas
                }
            } catch (error) {
                console.error('Error cargando recetas:', error)
            }
        },

        cerrarModal() {
            this.showModal = false
            this.resultado = null
        },

        exportarPDF() {
            window.print()
        },
        formData: {
            cantidad_producir: '',
            id_receta: '',
        },

        getNombreReceta(id) {
            const receta = this.recetas.find(r => r.id == id)
            return receta ? receta.nombre : ''
        },

        async init() {
            await this.cargarRecetas()
        },
        loading: false,
        recetas: [],
        resultado: null,
        showModal: false,
    }
}

// Función global para abrir el modal desde cualquier vista
window.abrirCalculoInsumos = () => {
    const event = new CustomEvent('abrir-calculo-insumos')
    window.dispatchEvent(event)
}
