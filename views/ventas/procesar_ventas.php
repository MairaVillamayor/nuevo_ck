<?php
include 'db_conexion.php';
header('Content-Type: application/json');

// 1. Recoger los datos enviados por AJAX (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// 2. Validaciones (omitidas aquí por simplicidad)

// 3. Iniciar Transacción
$pdo->beginTransaction();

try {
    // 4. Insertar en la tabla Venta (Cabecera)
    $stmt = $pdo->prepare("INSERT INTO Ventas (rela_cliente, total_final, fecha) VALUES (?, ?, NOW())");
    $stmt->execute([$data['cliente_id'], $data['total_venta']]);
    $venta_id = $pdo->lastInsertId();

    // 5. Insertar los DetalleVenta (Productos)
    $stmt_detalle = $pdo->prepare("INSERT INTO DetalleVenta (rela_venta, rela_producto, cantidad, total) VALUES (?, ?, ?, ?)");
    foreach ($data['productos'] as $producto) {
        $stmt_detalle->execute([$venta_id, $producto['id'], $producto['cantidad'], $producto['total']]);
    }

    // 6. Insertar los Pagos
    $stmt_pago = $pdo->prepare("INSERT INTO PagosVenta (rela_venta, rela_forma_pago, monto) VALUES (?, ?, ?)");
    foreach ($data['pagos'] as $pago) {
        $stmt_pago->execute([$venta_id, $pago['id'], $pago['monto']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Venta procesada con éxito.']);

} catch (\Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()]);
}
?>