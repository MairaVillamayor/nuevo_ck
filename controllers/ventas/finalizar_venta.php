<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");

try {
    // *************************
    // OBTENER DATOS DE POST (asegúrate de sanitizar si viene de inputs)
    // *************************
    $id_caja_activa = $_POST['id_caja_activa'] ?? 1;
    $id_usuario     = $_POST['id_usuario'] ?? 1;

    $idFactura      = $_POST['idFactura'] ?? null;
    $subTotal       = floatval($_POST['subTotal'] ?? 0);
    $tasaIVA        = floatval($_POST['tasaIVA'] ?? 0);
    $montoIVA       = floatval($_POST['montoIVA'] ?? 0);
    $totalFactura   = floatval($_POST['totalFactura'] ?? 0);
    $pagos          = is_array($_POST['pagos']) ? $_POST['pagos'] : [];

    if (!$idFactura || empty($pagos)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'Datos de factura o pagos incompletos.'
        ]);
        exit;
    }

    // *************************
    // INICIAR CONEXIÓN Y TRANSACCIÓN
    // *************************
    $conexion = getConexion(); // devuelve PDO real

    if (!$conexion->inTransaction()) {
        $conexion->beginTransaction();
    }

    // *************************
    // 1. ACTUALIZAR FACTURA
    // *************************
    // Suponiendo que RELA_estado_factura = 2 es "FINALIZADA"
    $sqlFactura = "
        UPDATE factura 
        SET factura_subtotal    = :subtotal,
            factura_iva_monto   = :iva,
            factura_iva_tasa    = :tasa,
            factura_total       = :total,
            RELA_estado_factura = :estado,
            factura_fecha_emision = NOW()
        WHERE ID_factura = :idFactura
    ";

    $stmtFactura = $conexion->prepare($sqlFactura);
    $stmtFactura->execute([
        ':subtotal'  => $subTotal,
        ':iva'       => $montoIVA,
        ':tasa'      => $tasaIVA,
        ':total'     => $totalFactura,
        ':estado'    => 2,          // 2 = FINALIZADA
        ':idFactura' => $idFactura
    ]);

    // *************************
    // 2. REGISTRAR PAGOS
    // *************************
    $sqlPago = "
        INSERT INTO factura_pagos 
        (RELA_factura, RELA_metodo_pago, pago_interes, pago_monto)
        VALUES (:factura, :metodo, :interes, :monto)
    ";
    $stmtPago = $conexion->prepare($sqlPago);

    foreach ($pagos as $pago) {
        $id_metodo_pago = (int)($pago['id_metodo_pago'] ?? 0);
        $interes        = floatval($pago['interes'] ?? 0);
        $monto          = floatval($pago['monto'] ?? 0);

        if ($id_metodo_pago && $monto > 0) {
            $stmtPago->execute([
                ':factura' => $idFactura,
                ':metodo'  => $id_metodo_pago,
                ':interes' => $interes,
                ':monto'   => $monto
            ]);
        }
    }

    // *************************
    // 3. REGISTRAR MOVIMIENTO DE CAJA
    // *************************
    if ($totalFactura > 0) {
        $sqlCaja = "
            INSERT INTO movimiento_caja 
            (RELA_caja, RELA_usuario, RELA_metodo_pago, movimiento_tipo, movimiento_monto, movimiento_descripcion, movimiento_fecha)
            VALUES (:idCaja, :idUsuario, :idMetodoPago, 'ingreso', :monto, :descripcion, NOW())
        ";

        $stmtCaja = $conexion->prepare($sqlCaja);

        // Usar el primer método de pago de la venta o 1 por defecto
        $primerMetodoPago = $pagos[0]['id_metodo_pago'] ?? 1;

        $stmtCaja->execute([
            ':idCaja'       => $id_caja_activa,
            ':idUsuario'    => $id_usuario,
            ':idMetodoPago' => $primerMetodoPago,
            ':monto'        => $totalFactura,
            ':descripcion'  => "Venta Factura #{$idFactura}"
        ]);
    }

    // *************************
    // 4. FINALIZAR TRANSACCIÓN
    // *************************
    if ($conexion->inTransaction()) {
        $conexion->commit();
    }

    echo json_encode([
        'success' => true,
        'id_factura_finalizada' => $idFactura,
        'mensaje' => 'Factura, pagos y movimiento de caja registrados correctamente'
    ]);

} catch (PDOException $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    error_log("Error de BD al finalizar venta: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al finalizar venta. Se revirtió la operación.',
        'mensaje' => $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    error_log("Error inesperado al finalizar venta: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error inesperado. Se revirtió la operación.',
        'mensaje' => $e->getMessage()
    ]);
}
?>