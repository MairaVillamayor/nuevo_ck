<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");

try {
    // 1️⃣ Recoger datos enviados por AJAX
    $idFactura    = $_POST['idFactura'] ?? null;
    $subTotal     = $_POST['subTotal'] ?? 0;
    $tasaIVA      = floatval($_POST['tasaIVA'] ?? 0);
    $montoIVA     = $_POST['montoIVA'] ?? 0;
    $totalFactura = $_POST['totalFactura'] ?? 0;
    $pagos        = $_POST['pagos'] ?? [];

    // 2️⃣ Validación mínima
    if (!$idFactura || empty($pagos)) {
        echo json_encode([
            'success' => false,
            'error'   => 'Datos de factura o pagos incompletos.'
        ]);
        exit;
    }

    // 3️⃣ Actualizar los datos de la factura
    $sqlFactura = " UPDATE factura 
        SET 
            factura_subtotal = :subtotal,
            factura_iva_monto = :iva,
            factura_total = :total,
            factura_fecha_emision = NOW()
        WHERE id_factura = :idFactura
    ";

    $stmtFactura = $conexion->prepare($sqlFactura);
    $stmtFactura->execute([
        ':subtotal' => $subTotal,
        ':iva' => $montoIVA,
        ':total' => $totalFactura,
        ':idFactura' => $idFactura
    ]);

    // 4️⃣ Registrar los pagos
    $sqlPago = " INSERT INTO factura_pagos (RELA_factura, RELA_metodo_pago, pago_interes, pago_monto)
        VALUES (:factura, :metodo, :interes, :monto)
    ";

    $stmtPago = $conexion->prepare($sqlPago);
    foreach ($pagos as $pago) {
        $id_metodo_pago = $pago['id_metodo_pago'] ?? null;
        $interes = $pago['interes'] ?? 0;
        $monto = $pago['monto'] ?? 0;

        if ($id_metodo_pago) {
            $stmtPago->execute([
                ':factura' => $idFactura,
                ':metodo' => $id_metodo_pago,
                ':interes' => $interes,
                ':monto' => $monto
            ]);
        }
    }

    // 5️⃣ Respuesta final
    echo json_encode([
        'success' => true,
        'id_factura_finalizada' => $idFactura,
        'mensaje_debug' => 'Factura y pagos registrados correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
