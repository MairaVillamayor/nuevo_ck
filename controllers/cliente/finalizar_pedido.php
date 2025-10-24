<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();

// --------------------
// 1) Verificar login
// --------------------
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

$pdo = getConexion();
$id_usuario = $_SESSION['usuario_id'];

// ------------------------------------
// 2) Buscar el pedido pendiente actual
// ------------------------------------
$sql = "SELECT ID_pedido FROM pedido 
        WHERE RELA_usuario = ? AND RELA_estado = 1 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: ../../views/cliente/mis_pedidos.php?msg=no_pedido');
    exit;
}

$id_pedido = $pedido['ID_pedido'];

// -----------------------------------------------------
// 3) Calcular el total del pedido (precio * cantidad)
// -----------------------------------------------------
$sql = "SELECT SUM(d.cantidad * p.producto_precio) AS total
        FROM pedido_detalle d
        JOIN Producto_finalizado p 
        ON d.RELA_producto_finalizado = p.ID_producto_finalizado
        WHERE d.RELA_pedido = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_pedido]);
$total = $stmt->fetchColumn() ?? 0;

// -----------------------------------------------------
// 4) Actualizar el pedido con el total y cambiar estado
// -----------------------------------------------------
$sql = "UPDATE pedido 
        SET pedido_total = ?, pedido_estado = 'confirmado', pedido_fecha_confirmacion = NOW()
        WHERE ID_pedido = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$total, $id_pedido]);

// -----------------------------------------------------
// 5) Redirigir a Mis Pedidos con mensaje de éxito
// -----------------------------------------------------
header('Location: ../../views/cliente/mis_pedidos.php?msg=pedido_confirmado');
exit;
