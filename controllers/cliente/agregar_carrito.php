<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => true, 'mensaje' => 'Debes iniciar sesión para agregar productos.']);
    exit;
}

$pdo = getConexion();
$id_usuario = $_SESSION['usuario_id'];
$id_producto = $_POST['id_producto'] ?? null;

if (!$id_producto) {
    echo json_encode(['error' => true, 'mensaje' => 'Producto inválido.']);
    exit;
}

// Verificar si el usuario tiene un pedido activo (estado “pendiente”)
$sql = "SELECT ID_pedido FROM pedido WHERE RELA_usuario = ? AND pedido_estado = 'pendiente' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no hay pedido pendiente, crear uno nuevo
if (!$pedido) {
    $stmt = $pdo->prepare("INSERT INTO pedido (RELA_usuario, pedido_fecha, pedido_estado) VALUES (?, NOW(), 'pendiente')");
    $stmt->execute([$id_usuario]);
    $id_pedido = $pdo->lastInsertId();
} else {
    $id_pedido = $pedido['ID_pedido'];
}

// Verificar si el producto ya está en el pedido_detalle
$sql = "SELECT * FROM pedido_detalle WHERE RELA_pedido = ? AND RELA_producto_finalizado = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_pedido, $id_producto]);

if ($stmt->rowCount() > 0) {
    // Si ya existe, aumentar cantidad
    $pdo->prepare("UPDATE pedido_detalle SET cantidad = cantidad + 1 WHERE RELA_pedido = ? AND RELA_producto_finalizado = ?")
        ->execute([$id_pedido, $id_producto]);
} else {
    // Si no existe, insertar nuevo
    $pdo->prepare("INSERT INTO pedido_detalle (RELA_pedido, RELA_producto_finalizado, cantidad) VALUES (?, ?, 1)")
        ->execute([$id_pedido, $id_producto]);
}

echo json_encode(['error' => false, 'mensaje' => 'Producto agregado al carrito con éxito 🎉']);
