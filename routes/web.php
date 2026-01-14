<?php

return [
    // Autenticación
    'GET|/' => ['AuthController', 'showLogin'],
    'GET|/login' => ['AuthController', 'showLogin'],
    'POST|/login' => ['AuthController', 'login'],
    'GET|/logout' => ['AuthController', 'logout'],
    'GET|/register' => ['AuthController', 'showRegister'],
    'POST|/auth/register' => ['AuthController', 'register'],
    'POST|/auth/verificar-clave-sucursal' => ['AuthController', 'verificarClaveSucursal'],
    'GET|/notificaciones/no-leidas' => ['NotificacionesController', 'getNoLeidas'],
    'POST|/notificaciones/marcar-leida/{id}' => ['NotificacionesController', 'marcarLeida'],
    'POST|/notificaciones/marcar-todas-leidas' => ['NotificacionesController', 'marcarTodasLeidas'],
    'POST|/auth/verificar-sucursal' => ['AuthController', 'verificarSucursal'],
    'POST|/auth/cambiar-sucursal' => ['AuthController', 'cambiarSucursal'],

    // Caja Chica
    'GET|/cajas' => ['CajaController', 'index'],
    'GET|/cajas/aprir' => ['CajaController', 'abrirPanel'],
    'POST|/cajas/abrir' => ['CajaController', 'abrir'],
    'GET|/cajas/cerrar' => ['CajaController', 'cerrarPanel'],
    'POST|/cajas/cerrar' => ['CajaController', 'cerrar'],
    'GET|/cajas/movimientos' => ['CajaController', 'movimientos'],
    'POST|/cajas/movimientos' => ['CajaController', 'addMovimiento'],

    // Dashboard
    'GET|/dashboard' => ['DashboardController', 'index'],
    'GET|/dashboard/notificaciones' => ['DashboardController', 'getNotificaciones'],
    'POST|/dashboard/notificacion/leida' => ['DashboardController', 'marcarNotificacionLeida'],

    // Productos
    'GET|/productos' => ['ProductosController', 'index'],
    'GET|/productos/create' => ['ProductosController', 'create'],
    'POST|/productos/store' => ['ProductosController', 'store'],
    'GET|/productos/edit/{id}' => ['ProductosController', 'edit'],
    'POST|/productos/update/{id}' => ['ProductosController', 'update'],
    'POST|/productos/delete/{id}' => ['ProductosController', 'delete'],
    'GET|/productos/search' => ['ProductosController', 'search'],
    'GET|/productos/stock-bajo' => ['ProductosController', 'stockBajo'],

    // Insumos
    'GET|/insumos' => ['InsumosController', 'index'],
    'GET|/insumos/create' => ['InsumosController', 'create'],
    'POST|/insumos/store' => ['InsumosController', 'store'],
    'GET|/insumos/edit/{id}' => ['InsumosController', 'edit'],
    'POST|/insumos/update/{id}' => ['InsumosController', 'update'],
    'POST|/insumos/delete/{id}' => ['InsumosController', 'delete'],
    'GET|/insumos/search' => ['InsumosController', 'search'],
    'GET|/insumos/stock-bajo' => ['InsumosController', 'stockBajo'],

    // Recetas
    'GET|/recetas' => ['RecetasController', 'index'],
    'GET|/recetas/create' => ['RecetasController', 'create'],
    'POST|/recetas/store' => ['RecetasController', 'store'],
    'GET|/recetas/edit/{id}' => ['RecetasController', 'edit'],
    'GET|/recetas/show/{id}' => ['RecetasController', 'show'],
    'POST|/recetas/update/{id}' => ['RecetasController', 'update'],
    'POST|/recetas/delete/{id}' => ['RecetasController', 'delete'],
    'POST|/recetas/calcular' => ['RecetasController', 'calcular'],
    'GET|/recetas/calcular' => ['RecetasController', 'calcular'],
    'POST|/recetas/add-insumo' => ['RecetasController', 'addInsumo'],
    'POST|/recetas/remove-insumo' => ['RecetasController', 'removeInsumo'],

    // Ventas
    'GET|/ventas' => ['VentasController', 'index'],
    'GET|/ventas/create' => ['VentasController', 'create'],
    'POST|/ventas/store' => ['VentasController', 'store'],
    'GET|/ventas/show/{id}' => ['VentasController', 'show'],
    'GET|/ventas/ticket/{id}' => ['VentasController', 'ticket'],

    // Clientes
    'GET|/clientes' => ['ClientesController', 'index'],
    'GET|/clientes/create' => ['ClientesController', 'create'],
    'POST|/clientes/store' => ['ClientesController', 'store'],
    'GET|/clientes/edit/{id}' => ['ClientesController', 'edit'],
    'POST|/clientes/update/{id}' => ['ClientesController', 'update'],
    'POST|/clientes/delete/{id}' => ['ClientesController', 'delete'],
    'GET|/clientes/show/{id}' => ['ClientesController', 'show'],
    'GET|/clientes/search' => ['ClientesController', 'search'],

    // Pedidos
    'GET|/pedidos' => ['PedidosController', 'index'],
    'GET|/pedidos/create' => ['PedidosController', 'create'],
    'POST|/pedidos/store' => ['PedidosController', 'store'],
    'GET|/pedidos/show/{id}' => ['PedidosController', 'show'],
    'POST|/pedidos/update/{id}' => ['PedidosController', 'update'],
    'POST|/pedidos/registrar-pago' => ['PedidosController', 'registrarPago'],

    // Producciones
    'GET|/producciones' => ['ProduccionesController', 'index'],
    'GET|/producciones/create' => ['ProduccionesController', 'create'],
    'POST|/producciones/store' => ['ProduccionesController', 'store'],
    'GET|/producciones/show/{id}' => ['ProduccionesController', 'show'],

    // Auditorías
    'GET|/auditorias' => ['AuditoriasController', 'index'],
    'GET|/auditorias/show/{id}' => ['AuditoriasController', 'show'],
    'POST|/auditorias/deshacer' => ['AuditoriasController', 'deshacer'],
    'GET|/auditorias/estadisticas' => ['AuditoriasController', 'estadisticas'],

    // Respaldos
    'GET|/respaldos' => ['RespaldosController', 'index'],
    'POST|/respaldos/generar' => ['RespaldosController', 'generar'],
    'POST|/respaldos/restaurar' => ['RespaldosController', 'restaurar'],
    'GET|/respaldos/descargar/{id}' => ['RespaldosController', 'descargar'],

    // Sugerencias de Compra
    'GET|/sugerencias' => ['SugerenciasController', 'index'],
    'POST|/sugerencias/generar' => ['SugerenciasController', 'generar'],
    'POST|/sugerencias/aprobar' => ['SugerenciasController', 'aprobar'],
    'POST|/sugerencias/rechazar' => ['SugerenciasController', 'rechazar'],
    'POST|/sugerencias/completar' => ['SugerenciasController', 'completar'],

    // Reportes
    'GET|/reportes' => ['ReportesController', 'index'],
    'GET|/reportes/ventas' => ['ReportesController', 'ventas'],
    'GET|/reportes/productos' => ['ReportesController', 'productos'],
    'GET|/reportes/clientes' => ['ReportesController', 'clientes'],
    'GET|/reportes/vencimientos' => ['ReportesController', 'vencimientos'],
    'GET|/reportes/compras' => ['ReportesController', 'compras'],
    'GET|/reportes/insumos' => ['ReportesController', 'insumos'],
    'GET|/reportes/producciones' => ['ReportesController', 'producciones'],
    'GET|/reportes/pedidos' => ['ReportesController', 'pedidos'],

    // Usuarios
    'GET|/usuarios' => ['UsuariosController', 'index'],
    'GET|/usuarios/perfil' => ['UsuariosController', 'perfil'],
    'GET|/usuarios/actividad' => ['UsuariosController', 'actividad'],
    'POST|/usuarios/actualizar-perfil' => ['UsuariosController', 'actualizarPerfil'],
    'POST|/usuarios/cambiar-estado' => ['UsuariosController', 'cambiarEstado'],

    // Sucursales (Admin only)
    'GET|/sucursales' => ['SucursalesController', 'index'],
    'GET|/sucursales/create' => ['SucursalesController', 'create'],
    'POST|/sucursales/store' => ['SucursalesController', 'store'],
    'GET|/sucursales/show/{id}' => ['SucursalesController', 'show'],
    'GET|/sucursales/edit/{id}' => ['SucursalesController', 'edit'],
    'POST|/sucursales/update/{id}' => ['SucursalesController', 'update'],
    'POST|/sucursales/cambiar-estado' => ['SucursalesController', 'cambiarEstado'],
    'POST|/sucursales/regenerar-clave/{id}' => ['SucursalesController', 'regenerarClave'],

    // Cálculo de Insumos
    'POST|/calculo-insumos/calcular' => ['CalculoInsumosController', 'calcularInsumos'],
    'POST|/calculo-insumos/verificar' => ['CalculoInsumosController', 'verificarDisponibilidad'],
    'GET|/recetas/list' => ['RecetasController', 'list'],

    // Predicciones
    'GET|/predicciones' => ['PrediccionesController', 'index'],
    'GET|/predicciones/data' => ['PrediccionesController', 'getDatosVentas'],
    'POST|/predicciones/generar' => ['PrediccionesController', 'generarSugerenciasAutomaticas'],

    // Proveedores
    'GET|/proveedores' => ['ProveedoresController', 'index'],
    'GET|/proveedores/create' => ['ProveedoresController', 'create'],
    'POST|/proveedores/store' => ['ProveedoresController', 'store'],
    'GET|/proveedores/edit/{id}' => ['ProveedoresController', 'edit'],
    'POST|/proveedores/update/{id}' => ['ProveedoresController', 'update'],
    'POST|/proveedores/delete/{id}' => ['ProveedoresController', 'delete'],
    'GET|/proveedores/show/{id}' => ['ProveedoresController', 'show'],
    'GET|/proveedores/insumos-sin-proveedor' => ['ProveedoresController', 'insumosSinProveedor'],

    // Compras
    'GET|/compras' => ['ComprasController', 'index'],
    'GET|/compras/create' => ['ComprasController', 'create'],
    'POST|/compras/store' => ['ComprasController', 'store'],
    'GET|/compras/show/{id}' => ['ComprasController', 'show'],

    // Control de Lotes / Vencimientos
    'GET|/lotes' => ['LotesController', 'index'],
    'POST|/lotes/ajustar' => ['LotesController', 'ajustar'],

    // System Config
    'GET|/config/refresh-tasa' => ['ConfigController', 'refreshTasa'],
];
