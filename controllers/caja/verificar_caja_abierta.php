<?php
require_once("../../config/conexion.php");
require_once("../../models/caja/caja.php");

$cajaModel = new Caja();

// Buscar la caja abierta
$sql = "SELECT * FROM caja WHERE caja_estado = 'Abierta' LIMIT 1";
$stmt = $conexion->query($sql);

$response = [];

if ($stmt->rowCount() > 0) {
    // Obtener la fila de la caja
    $caja = $stmt->fetch(PDO::FETCH_ASSOC);

    $ID_caja = $caja['ID_caja']; // <-- AQUÍ SE DEFINE LA VARIABLE

    // Obtener ventas (si necesitas enviarlas al front)
    $ventas_efectivo = $cajaModel->obtenerVentasPorMetodoPago($ID_caja, 'efectivo');
    $total_efectivo  = $cajaModel->obtenerTotalVentasPorMetodo('efectivo');

    $response = [
        "abierta" => true,
        "id_caja" => $ID_caja,
        "ventas_efectivo" => $ventas_efectivo,
        "total_efectivo" => $total_efectivo
    ];
} else {
    // No hay caja abierta
    $response = [
        "abierta" => false
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
