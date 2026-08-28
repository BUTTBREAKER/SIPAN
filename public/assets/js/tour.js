const driver = window.driver.js.driver

/**
 * Start a tour based on the current page context
 * @param {string} pageKey Initial key passed from the header (e.g. 'ventas')
 */
function startContextualTour(pageKey) {
    const path = window.location.pathname
    let contextKey = pageKey

    // Refine context based on path for sub-pages
    if (path.includes('/create')) {
        contextKey = `${pageKey}_create`
    } else if (path.includes('/edit')) {
        contextKey = `${pageKey}_edit`
    } else if (path.includes('/show')) {
        contextKey = `${pageKey}_show`
    }

    // Special cases for ambiguous keys or routes
    if (path.includes('/auditorias')) contextKey = 'auditorias'
    if (path.includes('/predicciones')) contextKey = 'predicciones'
    if (path.includes('/sugerencias')) contextKey = 'sugerencias'
    if (path.includes('/usuarios')) contextKey = 'usuarios'
    if (path.includes('/sucursales')) contextKey = 'sucursales'
    if (path.includes('/respaldos')) contextKey = 'respaldos'
    if (path.includes('/notificaciones')) contextKey = 'notificaciones'

    // Sub-context overwrites for broader matches
    if (path.includes('/sucursales/create')) contextKey = 'sucursales_create'
    if (path.includes('/sucursales/edit')) contextKey = 'sucursales_edit'
    if (path.includes('/clientes/create')) contextKey = 'clientes_create'
    if (path.includes('/clientes/edit')) contextKey = 'clientes_edit'
    if (path.includes('/proveedores/create')) contextKey = 'proveedores_create'
    if (path.includes('/proveedores/edit')) contextKey = 'proveedores_edit'
    if (path.includes('/insumos/create')) contextKey = 'insumos_create'
    if (path.includes('/recetas/create')) contextKey = 'recetas_create'
    if (path.includes('/compras/create')) contextKey = 'compras_create'
    if (path.includes('/pedidos/create')) contextKey = 'pedidos_create'

    const steps =
        tourSteps[contextKey] || tourSteps[pageKey] || tourSteps['dashboard']

    const driverObj = driver({
        allowClose: true,
        animate: true,
        doneBtnText: 'Finalizar',
        nextBtnText: 'Siguiente',
        overlayColor: 'rgba(0, 0, 0, 0.75)',
        prevBtnText: 'Anterior',
        showProgress: true,
        stagePadding: 5,
        steps: steps,
    })

    driverObj.drive()
}

/**
 * Detailed steps for every module and sub-page
 */
const tourSteps = {
    auditorias: [
        {
            popover: {
                description:
                    'Registro estricto de cada acción realizada por los usuarios.',
                title: '🛡️ Seguridad y Auditoría',
            },
        },
        {
            element: '#panelFiltros',
            popover: {
                description: 'Filtra por usuario, tabla o tipo de cambio.',
                side: 'bottom',
                title: '🔍 Búsqueda Selectiva',
            },
        },
        {
            element: '#timelineAuditorias',
            popover: {
                description:
                    'Secuencia de eventos con colores según gravedad: Verde (Registro), Amarillo (Edición), Rojo (Borrado).',
                side: 'top',
                title: '⏳ Línea de Tiempo',
            },
        },
    ],
    cajas: [
        {
            popover: {
                description:
                    'Gestiona la apertura, movimientos y cierre del efectivo diario.',
                title: '💰 Control de Caja Chica',
            },
        },
        {
            element: '.card-apertura',
            popover: {
                description:
                    'Aquí verás el monto inicial ($ y Bs) con el que comenzó el día.',
                side: 'bottom',
                title: '🔓 Apertura de Turno',
            },
        },
        {
            element: 'a[href="/cajas/movimientos"]',
            popover: {
                description:
                    'Registra entradas o salidas de efectivo que no sean ventas directas (ej: pago de servicios).',
                side: 'bottom',
                title: '💸 Movimientos Manuales',
            },
        },
        {
            element: 'a[href="/cajas/cerrarPanel"]',
            popover: {
                description:
                    'Al finalizar el turno, realiza el cuadre físico comparando el sistema con tu efectivo real.',
                side: 'top',
                title: '🔒 Cierre de Caja',
            },
        },
    ],
    clientes_create: [
        {
            popover: {
                description: 'Añade un nuevo cliente a la base de datos.',
                title: '👤 Registrar Cliente',
            },
        },
        {
            element: 'input[name="nombre"]',
            popover: {
                description: 'Nombre completo del cliente.',
                side: 'bottom',
                title: '📝 Datos Personales',
            },
        },
    ],
    compras_create: [
        {
            popover: {
                description:
                    'Ingresa una nueva factura de compra para aumentar stock.',
                title: '🛍️ Registrar Compra',
            },
        },
        {
            element: '#selectProveedor',
            popover: {
                description: 'Selecciona a quién le estás comprando.',
                title: '🤝 Proveedor',
            },
        },
    ],
    dashboard: [
        {
            popover: {
                align: 'start',
                description:
                    'Este es el centro de mando de tu panadería. Aquí tienes una visión 360° de tu operación en tiempo real.',
                side: 'left',
                title: '🚀 Bienvenido a SIPAN',
            },
        },
        {
            element: '#header-tasa',
            popover: {
                align: 'center',
                description:
                    'Aquí ves el tipo de cambio oficial. Úsalo para ventas en Bolívares. El botón de refrescar te asegura tener siempre el dato legal del día.',
                side: 'bottom',
                title: '💵 Tasa BCV Actualizada',
            },
        },
        {
            element: '.notifications-wrapper',
            popover: {
                align: 'center',
                description:
                    'Recibirás notificaciones si un producto tiene poco stock o si un insumo está por vencer.',
                side: 'bottom',
                title: '🔔 Alertas Críticas',
            },
        },
        {
            element: '.sidebar',
            popover: {
                align: 'start',
                description:
                    'Desde aquí accedes a todos los módulos. Pasa el cursor para expandirlo.',
                side: 'right',
                title: '📂 Navegación Inteligente',
            },
        },
    ],
    insumos: [
        {
            popover: {
                description: 'Control de materia prima (harina, azúcar, etc.)',
                title: '📦 Almacén de Insumos',
            },
        },
    ],
    insumos_create: [
        {
            popover: {
                description: 'Define una nueva materia prima.',
                title: '📦 Nuevo Insumo',
            },
        },
        {
            element: 'select[name="unidad_medida"]',
            popover: {
                description: '¿Cómo mides este insumo? (KG, Litros, Unidades)',
                side: 'bottom',
                title: '⚖️ Unidad',
            },
        },
    ],
    notificaciones: [
        {
            popover: {
                description:
                    'Revisa todas las alertas y avisos importantes del sistema.',
                title: '🔔 Centro de Notificaciones',
            },
        },
    ],
    pedidos_create: [
        {
            popover: {
                description: 'Toma un pedido especial para una fecha futura.',
                title: '📝 Nuevo Pedido',
            },
        },
        {
            element: 'input[name="fecha_entrega"]',
            popover: {
                description: '¿Cuándo debe estar listo el pedido?',
                side: 'bottom',
                title: '📅 Fecha de Entrega',
            },
        },
    ],
    predicciones: [
        {
            popover: {
                description:
                    'Predicciones de demanda generadas por el sistema.',
                title: '🔮 Inteligencia de Negocios',
            },
        },
        {
            element: '#prediccionChart',
            popover: {
                description:
                    'Compara ventas históricas con proyecciones futuras para anticipar pedidos.',
                side: 'top',
                title: '📈 Gráfico de Tendencia',
            },
        },
    ],
    producciones: [
        {
            popover: {
                description:
                    'Registra qué se horneó hoy y descuenta insumos automáticamente.',
                title: '🏭 Producción Diaria',
            },
        },
    ],
    productos: [
        {
            popover: {
                description:
                    'Administra tus panes, tortas y productos finales.',
                title: '🥖 Catálogo de Productos',
            },
        },
        {
            element: 'a[href="/productos/create"]',
            popover: {
                description: 'Agrega nuevas creaciones a tu inventario.',
                side: 'bottom',
                title: '➕ Nuevo Producto',
            },
        },
    ],
    productos_create: [
        {
            popover: {
                description:
                    'Define los parámetros básicos para tu nuevo producto.',
                title: '📝 Registro de Producto',
            },
        },
        {
            element: 'input[name="nombre"]',
            popover: {
                description: 'Nombre comercial del producto.',
                side: 'bottom',
                title: '🏷️ Nombre',
            },
        },
        {
            element: 'input[name="precio_actual"]',
            popover: {
                description: 'Precio de venta al público en USD.',
                side: 'bottom',
                title: '💰 Precio $',
            },
        },
    ],
    proveedores_create: [
        {
            popover: {
                description:
                    'Ingresa los datos de contacto de tu socio comercial.',
                title: '🚛 Registrar Proveedor',
            },
        },
    ],
    recetas: [
        {
            popover: {
                description:
                    'Define la composición de tus productos para calcular costos exactos.',
                title: '📖 Recetario Maestro',
            },
        },
    ],
    recetas_create: [
        {
            popover: {
                description:
                    'Crea la fórmula de un producto para descontar inventario.',
                title: '📖 Nueva Receta',
            },
        },
    ],
    reportes: [
        {
            popover: {
                description:
                    'Genera PDFs detallados para contabilidad y gerencia.',
                title: '📊 Centro de Reportes',
            },
        },
    ],
    respaldos: [
        {
            popover: {
                description:
                    'Genera y descarga copias de seguridad de tu base de datos.',
                title: '💾 Respaldos de Seguridad',
            },
        },
        {
            element: 'button[type="submit"]',
            popover: {
                description:
                    'Crea una copia instantánea del estado actual del sistema.',
                side: 'bottom',
                title: '⚡ Generar Respaldo',
            },
        },
    ],
    sucursales: [
        {
            popover: {
                description:
                    'Administra las diferentes sedes o tiendas de tu negocio.',
                title: '🏢 Gestión de Sucursales',
            },
        },
        {
            element: 'a[href="/sucursales/create"]',
            popover: {
                description: 'Registra una nueva tienda física.',
                side: 'bottom',
                title: '➕ Nueva Sucursal',
            },
        },
    ],
    sucursales_create: [
        {
            popover: {
                description:
                    'Registra los datos de una nueva ubicación física.',
                title: '🏢 Nueva Sucursal',
            },
        },
        {
            element: 'input[name="nombre"]',
            popover: {
                description: 'Identificador único de la tienda.',
                side: 'bottom',
                title: '🏷️ Nombre',
            },
        },
    ],
    sugerencias: [
        {
            popover: {
                description:
                    'Lo que el sistema recomienda comprar basado en predicciones.',
                title: '📝 Sugerencias de Abastecimiento',
            },
        },
        {
            element: '#btnGenerar',
            popover: {
                description: 'Analiza nuevamente el stock y las proyecciones.',
                side: 'bottom',
                title: '⚙️ Motor de Cálculo',
            },
        },
    ],
    ventas: [
        {
            popover: {
                description:
                    'Revisa tu historial de facturacion y el estado de tus ingresos diarios.',
                title: '💰 Gestión de Ventas',
            },
        },
        {
            element: 'a[href="/ventas/create"]',
            popover: {
                description: 'Inicia una venta rápida desde aquí.',
                side: 'bottom',
                title: '🛒 Nueva Transacción',
            },
        },
        {
            element: '#grid-ventas',
            popover: {
                description:
                    'Consulta todas las ventas pasadas y reimprime comprobantes.',
                side: 'top',
                title: '📋 Historial de Tickets',
            },
        },
    ],
    ventas_create: [
        {
            popover: {
                description:
                    'Interfaz de facturación rápida optimizada para panaderías.',
                title: '🛒 Punto de Venta (POS)',
            },
        },
        {
            element: '#selectCliente',
            popover: {
                description:
                    'Selecciona un cliente o usa el botón azul (+) para registrar uno nuevo al instante.',
                side: 'bottom',
                title: '👥 Cliente',
            },
        },
        {
            element: '#btnNuevoCliente',
            popover: {
                description:
                    '¿El cliente no está en la lista? Regístralo rápidamente sin salir de esta pantalla.',
                side: 'bottom',
                title: '➕ Nuevo Cliente',
            },
        },
        {
            element: '#inputBusqueda',
            popover: {
                description:
                    'Escribe el nombre o código del pan/dulce. El stock se descuenta automáticamente al facturar.',
                side: 'top',
                title: '🔍 Buscar Producto',
            },
        },
        {
            element: '#panel-pagos',
            popover: {
                description:
                    'Carga pagos en Efectivo $, Bs, Zelle o Mixto. El sistema detecta si falta cubrir el total.',
                side: 'left',
                title: '💳 Pagos Multi-Moneda',
            },
        },
        {
            element: 'button[type="submit"]',
            popover: {
                description: 'Procesa la venta cuando el saldo esté cubierto.',
                side: 'top',
                title: '✅ Finalizar',
            },
        },
    ],
}
