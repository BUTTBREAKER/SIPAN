const driver = window.driver.js.driver;

/**
 * Start a tour based on the current page context
 * @param {string} pageKey Initial key passed from the header (e.g. 'ventas')
 */
function startContextualTour(pageKey) {
    const path = window.location.pathname;
    let contextKey = pageKey;

    // Refine context based on path for sub-pages
    if (path.includes("/create")) {
        contextKey = `${pageKey}_create`;
    } else if (path.includes("/edit")) {
        contextKey = `${pageKey}_edit`;
    } else if (path.includes("/show")) {
        contextKey = `${pageKey}_show`;
    }

    // Special cases for ambiguous keys or routes
    if (path.includes("/auditorias")) contextKey = "auditorias";
    if (path.includes("/predicciones")) contextKey = "predicciones";
    if (path.includes("/sugerencias")) contextKey = "sugerencias";
    if (path.includes('/usuarios')) contextKey = 'usuarios';
    if (path.includes('/sucursales')) contextKey = 'sucursales';
    if (path.includes('/respaldos')) contextKey = 'respaldos';
    if (path.includes('/notificaciones')) contextKey = 'notificaciones';

    // Sub-context overwrites for broader matches
    if (path.includes('/sucursales/create')) contextKey = 'sucursales_create';
    if (path.includes('/sucursales/edit')) contextKey = 'sucursales_edit';
    if (path.includes('/clientes/create')) contextKey = 'clientes_create';
    if (path.includes('/clientes/edit')) contextKey = 'clientes_edit';
    if (path.includes('/proveedores/create')) contextKey = 'proveedores_create';
    if (path.includes('/proveedores/edit')) contextKey = 'proveedores_edit';
    if (path.includes('/insumos/create')) contextKey = 'insumos_create';
    if (path.includes('/recetas/create')) contextKey = 'recetas_create';
    if (path.includes('/compras/create')) contextKey = 'compras_create';
    if (path.includes('/pedidos/create')) contextKey = 'pedidos_create';

    const driverObj = driver({
        showProgress: true,
        animate: true,
        allowClose: true,
        stagePadding: 5,
        overlayColor: "rgba(0, 0, 0, 0.75)",
        nextBtnText: "Siguiente",
        prevBtnText: "Anterior",
        doneBtnText: "Finalizar",
        steps: steps,
    });

    driverObj.drive();
}

/**
 * Detailed steps for every module and sub-page
 */
const tourSteps = {
    dashboard: [
        {
            popover: {
                title: "🚀 Bienvenido a SIPAN",
                description:
                    "Este es el centro de mando de tu panadería. Aquí tienes una visión 360° de tu operación en tiempo real.",
                side: "left",
                align: "start",
            },
        },
        {
            element: "#header-tasa",
            popover: {
                title: "💵 Tasa BCV Actualizada",
                description:
                    "Aquí ves el tipo de cambio oficial. Úsalo para ventas en Bolívares. El botón de refrescar te asegura tener siempre el dato legal del día.",
                side: "bottom",
                align: "center",
            },
        },
        {
            element: ".notifications-wrapper",
            popover: {
                title: "🔔 Alertas Críticas",
                description:
                    "Recibirás notificaciones si un producto tiene poco stock o si un insumo está por vencer.",
                side: "bottom",
                align: "center",
            },
        },
        {
            element: ".sidebar",
            popover: {
                title: "📂 Navegación Inteligente",
                description:
                    "Desde aquí accedes a todos los módulos. Pasa el cursor para expandirlo.",
                side: "right",
                align: "start",
            },
        },
    ],
    ventas: [
        {
            popover: {
                title: "💰 Gestión de Ventas",
                description:
                    "Revisa tu historial de facturacion y el estado de tus ingresos diarios.",
            },
        },
        {
            element: 'a[href="/ventas/create"]',
            popover: {
                title: "🛒 Nueva Transacción",
                description: "Inicia una venta rápida desde aquí.",
                side: "bottom",
            },
        },
        {
            element: "#grid-ventas",
            popover: {
                title: "📋 Historial de Tickets",
                description:
                    "Consulta todas las ventas pasadas y reimprime comprobantes.",
                side: "top",
            },
        },
    ],
    ventas_create: [
        {
            popover: {
                title: "🛒 Punto de Venta (POS)",
                description:
                    "Interfaz de facturación rápida optimizada para panaderías.",
            },
        },
        {
            element: "#selectCliente",
            popover: {
                title: "👥 Cliente",
                description:
                    "Selecciona un cliente o usa el botón azul (+) para registrar uno nuevo al instante.",
                side: "bottom",
            },
        },
        {
            element: "#btnNuevoCliente",
            popover: {
                title: "➕ Nuevo Cliente",
                description:
                    "¿El cliente no está en la lista? Regístralo rápidamente sin salir de esta pantalla.",
                side: "bottom",
            },
        },
        {
            element: "#inputBusqueda",
            popover: {
                title: "🔍 Buscar Producto",
                description:
                    "Escribe el nombre o código del pan/dulce. El stock se descuenta automáticamente al facturar.",
                side: "top",
            },
        },
        {
            element: "#panel-pagos",
            popover: {
                title: "💳 Pagos Multi-Moneda",
                description:
                    "Carga pagos en Efectivo $, Bs, Zelle o Mixto. El sistema detecta si falta cubrir el total.",
                side: "left",
            },
        },
        {
            element: 'button[type="submit"]',
            popover: {
                title: "✅ Finalizar",
                description: "Procesa la venta cuando el saldo esté cubierto.",
                side: "top",
            },
        },
    ],
    productos: [
        {
            popover: {
                title: "🥖 Catálogo de Productos",
                description:
                    "Administra tus panes, tortas y productos finales.",
            },
        },
        {
            element: 'a[href="/productos/create"]',
            popover: {
                title: "➕ Nuevo Producto",
                description: "Agrega nuevas creaciones a tu inventario.",
                side: "bottom",
            },
        },
    ],
    productos_create: [
        {
            popover: {
                title: "📝 Registro de Producto",
                description:
                    "Define los parámetros básicos para tu nuevo producto.",
            },
        },
        {
            element: 'input[name="nombre"]',
            popover: {
                title: "🏷️ Nombre",
                description: "Nombre comercial del producto.",
                side: "bottom",
            },
        },
        {
            element: 'input[name="precio_actual"]',
            popover: {
                title: "💰 Precio $",
                description: "Precio de venta al público en USD.",
                side: "bottom",
            },
        },
    ],
    insumos: [
        {
            popover: {
                title: "📦 Almacén de Insumos",
                description: "Control de materia prima (harina, azúcar, etc.)",
            },
        },
    ],
    recetas: [
        {
            popover: {
                title: "📖 Recetario Maestro",
                description:
                    "Define la composición de tus productos para calcular costos exactos.",
            },
        },
    ],
    producciones: [
        {
            popover: {
                title: "🏭 Producción Diaria",
                description:
                    "Registra qué se horneó hoy y descuenta insumos automáticamente.",
            },
        },
    ],
    auditorias: [
        {
            popover: {
                title: "🛡️ Seguridad y Auditoría",
                description:
                    "Registro estricto de cada acción realizada por los usuarios.",
            },
        },
        {
            element: "#panelFiltros",
            popover: {
                title: "🔍 Búsqueda Selectiva",
                description: "Filtra por usuario, tabla o tipo de cambio.",
                side: "bottom",
            },
        },
        {
            element: "#timelineAuditorias",
            popover: {
                title: "⏳ Línea de Tiempo",
                description:
                    "Secuencia de eventos con colores según gravedad: Verde (Registro), Amarillo (Edición), Rojo (Borrado).",
                side: "top",
            },
        },
    ],
    predicciones: [
        {
            popover: {
                title: "🔮 Inteligencia de Negocios",
                description:
                    "Predicciones de demanda generadas por el sistema.",
            },
        },
        {
            element: "#prediccionChart",
            popover: {
                title: "📈 Gráfico de Tendencia",
                description:
                    "Compara ventas históricas con proyecciones futuras para anticipar pedidos.",
                side: "top",
            },
        },
    ],
    sugerencias: [
        {
            popover: {
                title: "📝 Sugerencias de Abastecimiento",
                description:
                    "Lo que el sistema recomienda comprar basado en predicciones.",
            },
        },
        {
            element: "#btnGenerar",
            popover: {
                title: "⚙️ Motor de Cálculo",
                description: "Analiza nuevamente el stock y las proyecciones.",
                side: "bottom",
            },
        },
    ],
    reportes: [
        {
            popover: {
                title: "📊 Centro de Reportes",
                description:
                    "Genera PDFs detallados para contabilidad y gerencia.",
            },
        },
    ],
    cajas: [
        {
            popover: {
                title: "💰 Control de Caja Chica",
                description:
                    "Gestiona la apertura, movimientos y cierre del efectivo diario.",
            },
        },
        {
            element: ".card-apertura",
            popover: {
                title: "🔓 Apertura de Turno",
                description:
                    "Aquí verás el monto inicial ($ y Bs) con el que comenzó el día.",
                side: "bottom",
            },
        },
        {
            element: 'a[href="/cajas/movimientos"]',
            popover: {
                title: "💸 Movimientos Manuales",
                description:
                    "Registra entradas o salidas de efectivo que no sean ventas directas (ej: pago de servicios).",
                side: "bottom",
            },
        },
        {
            element: 'a[href="/cajas/cerrarPanel"]',
            popover: {
                title: "🔒 Cierre de Caja",
                description:
                    "Al finalizar el turno, realiza el cuadre físico comparando el sistema con tu efectivo real.",
                side: "top",
            },
        },
    ],
    'sucursales': [
        {
            popover: { title: '🏢 Gestión de Sucursales', description: 'Administra las diferentes sedes o tiendas de tu negocio.' }
        },
        {
            element: 'a[href="/sucursales/create"]',
            popover: { title: '➕ Nueva Sucursal', description: 'Registra una nueva tienda física.', side: "bottom" }
        }
    ],
    'sucursales_create': [
        {
            popover: { title: '🏢 Nueva Sucursal', description: 'Registra los datos de una nueva ubicación física.' }
        },
        {
            element: 'input[name="nombre"]',
            popover: { title: '🏷️ Nombre', description: 'Identificador único de la tienda.', side: "bottom" }
        }
    ],
    'respaldos': [
        {
            popover: { title: '💾 Respaldos de Seguridad', description: 'Genera y descarga copias de seguridad de tu base de datos.' }
        },
        {
            element: 'button[type="submit"]',
            popover: { title: '⚡ Generar Respaldo', description: 'Crea una copia instantánea del estado actual del sistema.', side: "bottom" }
        }
    ],
    'notificaciones': [
        {
            popover: { title: '🔔 Centro de Notificaciones', description: 'Revisa todas las alertas y avisos importantes del sistema.' }
        }
    ],
    'clientes_create': [
        {
            popover: { title: '👤 Registrar Cliente', description: 'Añade un nuevo cliente a la base de datos.' }
        },
        {
            element: 'input[name="nombre"]',
            popover: { title: '📝 Datos Personales', description: 'Nombre completo del cliente.', side: "bottom" }
        }
    ],
    'proveedores_create': [
        {
            popover: { title: '🚛 Registrar Proveedor', description: 'Ingresa los datos de contacto de tu socio comercial.' }
        }
    ],
    'compras_create': [
        {
            popover: { title: '🛍️ Registrar Compra', description: 'Ingresa una nueva factura de compra para aumentar stock.' }
        },
        {
            element: '#selectProveedor',
            popover: { title: '🤝 Proveedor', description: 'Selecciona a quién le estás comprando.' }
        }
    ],
    'insumos_create': [
        {
            popover: { title: '📦 Nuevo Insumo', description: 'Define una nueva materia prima.' }
        },
        {
            element: 'select[name="unidad_medida"]',
            popover: { title: '⚖️ Unidad', description: '¿Cómo mides este insumo? (KG, Litros, Unidades)', side: "bottom" }
        }
    ],
    'pedidos_create': [
        {
            popover: { title: '📝 Nuevo Pedido', description: 'Toma un pedido especial para una fecha futura.' }
        },
        {
            element: 'input[name="fecha_entrega"]',
            popover: { title: '📅 Fecha de Entrega', description: '¿Cuándo debe estar listo el pedido?', side: "bottom" }
        }
    ],
    'recetas_create': [
        {
            popover: { title: '📖 Nueva Receta', description: 'Crea la fórmula de un producto para descontar inventario.' }
        }
    ]
};
