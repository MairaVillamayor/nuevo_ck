<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");

try {
    $idFactura    = $_POST['idFactura'] ?? null;
    $subTotal     = floatval($_POST['subTotal'] ?? 0);
    $tasaIVA      = floatval($_POST['tasaIVA'] ?? 0);
    $montoIVA     = floatval($_POST['montoIVA'] ?? 0);
    $totalFactura = floatval($_POST['totalFactura'] ?? 0);
    $pagos        = $_POST['pagos'] ?? [];

    if (!$idFactura || empty($pagos)) {
        echo json_encode([
            'success' => false,
            'error'   => 'Datos de factura o pagos incompletos.'
        ]);
        exit;
    }

    $sqlFactura = "UPDATE factura 
        SET 
            factura_subtotal = :subtotal,
            factura_iva_monto = :iva,
            factura_iva_tasa = :tasa,
            factura_total = :total,
            factura_fecha_emision = NOW()
        WHERE id_factura = :idFactura
    ";

    $stmtFactura = $conexion->prepare($sqlFactura);

    $stmtFactura->execute([
        ':subtotal' => $subTotal,
        ':iva' => $montoIVA,
        ':tasa' => $tasaIVA,
        ':total' => $totalFactura,
        ':idFactura' => $idFactura
    ]);

    // Registrar pagos
    $sqlPago = "INSERT INTO factura_pagos 
        (RELA_factura, RELA_metodo_pago, pago_interes, pago_monto)
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

    echo json_encode([
        'success' => true,
        'id_factura_finalizada' => $idFactura,
        'mensaje' => 'Factura y pagos registrados correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
