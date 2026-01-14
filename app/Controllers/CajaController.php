<?php

namespace App\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use App\Middlewares\AuthMiddleware;

class CajaController
{
    private $cajaModel;
    private $sucursalModel;

    public function __construct()
    {
        $this->cajaModel = new Caja();
        $this->sucursalModel = new Sucursal();
    }

    public function index()
    {
        $id_sucursal = $_SESSION['sucursal_id'];
        $cajaActiva = $this->cajaModel->getActiva($id_sucursal);
        $historial = $this->cajaModel->getHistorial($id_sucursal);

        $currentPage = 'cajas';
        $title = 'Caja Chica';
        require_once __DIR__ . '/../Views/cajas/index.php';
    }

    public function abrirPanel()
    {
        $id_sucursal = $_SESSION['sucursal_id'];
        $cajaActiva = $this->cajaModel->getActiva($id_sucursal);

        if ($cajaActiva) {
            header('Location: /cajas');
            exit;
        }

        $currentPage = 'cajas';
        $title = 'Apertura de Caja';
        require_once __DIR__ . '/../Views/cajas/apertura.php';
    }

    public function abrir()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cajas');
            exit;
        }

        $monto_usd = floatval($_POST['monto_usd'] ?? 0);
        $monto_bs = floatval($_POST['monto_bs'] ?? 0);
        $id_sucursal = $_SESSION['sucursal_id'];
        $id_usuario = $_SESSION['user_id'];

        // Obtener tasa actual
        $configModel = new \App\Models\Configuracion();
        $tasa = $configModel->getTasaBCV();

        $success = $this->cajaModel->abrir($id_sucursal, $id_usuario, $monto_usd, $monto_bs, $tasa);

        if ($success) {
            $_SESSION['flash_message'] = 'Caja abierta correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al abrir la caja.';
            $_SESSION['flash_type'] = 'error';
        }

        header('Location: /cajas');
        exit;
    }

    public function cerrarPanel()
    {
        $id_sucursal = $_SESSION['sucursal_id'];
        $cajaActiva = $this->cajaModel->getActiva($id_sucursal);

        if (!$cajaActiva) {
            header('Location: /cajas');
            exit;
        }

        $resumen = $this->cajaModel->getResumen($cajaActiva['id']);

        $currentPage = 'cajas';
        $title = 'Cierre de Caja';
        require_once __DIR__ . '/../Views/cajas/cierre.php';
    }

    public function cerrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cajas');
            exit;
        }

        $id_caja = $_POST['id_caja'];
        $monto_usd = floatval($_POST['monto_usd'] ?? 0);
        $monto_bs = floatval($_POST['monto_bs'] ?? 0);
        $observaciones = $_POST['observaciones'] ?? '';
        $id_usuario = $_SESSION['user_id'];

        // Obtener tasa actual
        $configModel = new \App\Models\Configuracion();
        $tasa = $configModel->getTasaBCV();

        $success = $this->cajaModel->cerrar($id_caja, $id_usuario, $monto_usd, $monto_bs, $tasa, $observaciones);

        if ($success) {
            $_SESSION['flash_message'] = 'Caja cerrada correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al cerrar la caja.';
            $_SESSION['flash_type'] = 'error';
        }

        header('Location: /cajas');
        exit;
    }

    public function movimientos()
    {
        $id_sucursal = $_SESSION['sucursal_id'];
        $cajaActiva = $this->cajaModel->getActiva($id_sucursal);

        if (!$cajaActiva) {
            $_SESSION['flash_message'] = ['type' => 'warning', 'content' => 'Debe abrir caja para gestionar movimientos.'];
            header('Location: /cajas');
            exit;
        }

        $movimientos = $this->cajaModel->getMovimientos($cajaActiva['id']);

        $currentPage = 'cajas';
        $title = 'Movimientos de Caja';
        require_once __DIR__ . '/../Views/cajas/movimientos.php';
    }

    public function addMovimiento()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cajas/movimientos');
            exit;
        }

        $id_caja = $_POST['id_caja'];
        $tipo = $_POST['tipo'];
        $monto = $_POST['monto'];
        $descripcion = $_POST['descripcion'];
        $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';

        $success = $this->cajaModel->addMovimiento($id_caja, $tipo, $monto, $descripcion, $metodo_pago);

        if ($success) {
            $_SESSION['flash_message'] = 'Movimiento registrado.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al registrar movimiento.';
            $_SESSION['flash_type'] = 'error';
        }

        header('Location: /cajas/movimientos');
        exit;
    }
}
