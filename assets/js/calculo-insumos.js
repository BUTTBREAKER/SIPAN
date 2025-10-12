// Componente Alpine.js para cálculo de insumos
function calculoInsumosModal() {
    return {
        showModal: false,
        loading: false,
        recetas: [],
        resultado: null,
        formData: {
            id_receta: '',
            cantidad_producir: ''
        },
        
        async init() {
            await this.cargarRecetas();
        },
        
        async cargarRecetas() {
            try {
                const response = await fetch('/recetas/list');
                const data = await response.json();
                if (data.success) {
                    this.recetas = data.recetas;
                }
            } catch (error) {
                console.error('Error cargando recetas:', error);
            }
        },
        
        abrirModal() {
            this.showModal = true;
            this.resultado = null;
            this.formData = {
                id_receta: '',
                cantidad_producir: ''
            };
        },
        
        cerrarModal() {
            this.showModal = false;
            this.resultado = null;
        },
        
        async calcular() {
            if (!this.formData.id_receta || !this.formData.cantidad_producir) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Complete todos los campos',
                    confirmButtonColor: '#D4A574'
                });
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/calculo-insumos/calcular', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.resultado = data;
                    
                    if (!data.puede_producir) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Insumos Insuficientes',
                            text: `Faltan ${data.insumos_faltantes.length} insumos para completar la producción`,
                            confirmButtonColor: '#D4A574'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonColor: '#D4A574'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión',
                    confirmButtonColor: '#D4A574'
                });
            } finally {
                this.loading = false;
            }
        },
        
        getNombreReceta(id) {
            const receta = this.recetas.find(r => r.id == id);
            return receta ? receta.nombre : '';
        },
        
        exportarPDF() {
            window.print();
        }
    };
}

// Función global para abrir el modal desde cualquier vista
window.abrirCalculoInsumos = function() {
    const event = new CustomEvent('abrir-calculo-insumos');
    window.dispatchEvent(event);
};

