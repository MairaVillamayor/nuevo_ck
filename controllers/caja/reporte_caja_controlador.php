<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

require_once("../../config/conexion.php");
require_once("../../models/caja/caja.php");

$cajaModel = new Caja();

// 1️⃣ Obtener el ID de caja (puede venir por GET o tomar la abierta)
$id_caja = $_GET['id_caja'] ?? null;
if (!$id_caja) {
    $caja_abierta = $cajaModel->getCajaAbierta();
    if (!$caja_abierta) {
        die("No hay ninguna caja abierta ni se proporcionó ID de caja.");
    }
    $id_caja = $caja_abierta['ID_caja'];
}

// 2️⃣ Obtener datos generales de la caja
$datos_caja = $cajaModel->obtenerCajaPorId($id_caja);

// 3️⃣ Obtener ventas por método de pago
$ventas_efectivo = $cajaModel->obtenerVentasPorMetodoPago($id_caja, 'efectivo');
$ventas_transferencia = $cajaModel->obtenerVentasPorMetodoPago($id_caja, 'transferencia');
$ventas_mp = $cajaModel->obtenerVentasPorMetodoPago($id_caja, 'mp');

// 4️⃣ Obtener totales por método
$total_efectivo = $cajaModel->obtenerTotalVentasPorMetodo('efectivo', $id_caja);
$total_transferencia = $cajaModel->obtenerTotalVentasPorMetodo('transferencia', $id_caja);
$total_mp = $cajaModel->obtenerTotalVentasPorMetodo('mp', $id_caja);

// 5️⃣ Cargar la vista
require_once("../../views/caja/reporte_caja.php");
