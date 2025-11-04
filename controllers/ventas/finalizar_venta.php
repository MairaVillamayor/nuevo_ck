<?php
header('Content-Type: application/json');

// 1. Recoger los datos enviados por AJAX
$idFactura = $_POST['idFactura'] ?? null;
$subTotal = $_POST['subTotal'] ?? 0;
$tasaIVA = $_POST['tasaIVA'] ?? 0;
$montoIVA = $_POST['montoIVA'] ?? 0;
$totalFactura = $_POST['totalFactura'] ?? 0;
$pagos = $_POST['pagos'] ?? [];

// 2. Validación mínima de datos
if (!$idFactura || count($pagos) === 0) {
    echo json_encode(['success' => false, 'error' => 'Datos de factura o pagos incompletos.']);
    exit;
}


$log = [
    'factura_actualizada' => "Factura N° $idFactura actualizada con Total: $totalFactura y Tasa IVA: $tasaIVA%",
    'pagos_registrados' => [],
];

foreach ($pagos as $pago) {
    // Aquí se ejecutaría la sentencia SQL de inserción:
    // INSERT INTO factura_pagos (id_factura, id_forma_pago, interes, monto) VALUES (...)
    $log['pagos_registrados'][] = "Registrado: Forma Pago ID {$pago['id_pago']}, Interés {$pago['interes']}%, Monto {$pago['monto']}";
}

// Si todo sale bien (sin errores de BD)
echo json_encode([
    'success' => true,
    'id_factura_finalizada' => $idFactura,
    'mensaje_debug' => $log,
]);

// Si hubiera un error de BD:
// // echo json_encode(['success' => false, 'error' => 'Error al guardar en la base de datos.']);
?>