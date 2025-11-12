<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");


// 1️⃣ Recoger los datos enviados por AJAX
$idFactura    = $_POST['idFactura'] ?? null;
$subTotal     = $_POST['subTotal'] ?? 0;
$tasaIVA      = $_POST['tasaIVA'] ?? 0;
$montoIVA     = $_POST['montoIVA'] ?? 0;
$totalFactura = $_POST['totalFactura'] ?? 0;
$pagos        = $_POST['pagos'] ?? [];

// 2️⃣ Validación mínima de datos
if (!$idFactura || count($pagos) === 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'Datos de factura o pagos incompletos.'
    ]);
    exit;
}

// 3️⃣ Preparar log de depuración (solo para verificar en consola)
$log = [
    'factura_actualizada' => "Factura N° $idFactura actualizada con Total: $totalFactura y Tasa IVA: $tasaIVA%",
    'pagos_registrados'   => [],
];

// 4️⃣ Registrar pagos en la tabla factura_pagos
foreach ($pagos as $pago) {
    // Nombres reales esperados desde el JS
    $idPago   = $pago['id_pago']   ?? null;
    $interes  = $pago['interes']   ?? 0;
    $monto    = $pago['monto']     ?? 0;

    if ($idPago === null) continue;

    // Inserción segura (usa los nombres de columnas reales de tu tabla)
    $stmt = $conexion->prepare("
        INSERT INTO factura_pagos (RELA_factura, RELA_metodo_pago, pago_interes, pago_monto)
        VALUES (?, ?, ?, ?)
    ");

    if ($stmt) {
        $stmt->bind_param("iidd", $idFactura, $idPago, $interes, $monto);
        $stmt->execute();
        $stmt->close();

        $log['pagos_registrados'][] = "Pago ID {$idPago}, Interés {$interes}%, Monto {$monto}";
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error al preparar la consulta SQL: ' . $conexion->error
        ]);
        exit;
    }
}

// 5️⃣ Cerrar conexión
$conexion->close();

// 6️⃣ Respuesta JSON final
echo json_encode([
    'success' => true,
    'id_factura_finalizada' => $idFactura,
    'mensaje_debug' => $log
]);
?>
