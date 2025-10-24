<?php
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../views/usuario/login.php?error=not_logged');
    exit;
}

$pdo = getConexion();
$id_usuario = $_SESSION['usuario_id'];

// Buscar pedido pendiente
$sql = "SELECT * FROM pedido WHERE RELA_usuario = ? AND pedido_estado = 'pendiente' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

$items = [];
if ($pedido) {
    $sql = "SELECT d.ID_pedido_detalle, p.producto_nombre, d.cantidad
            FROM pedido_detalle d
            JOIN Producto_finalizado p ON d.RELA_producto_finalizado = p.ID_producto_finalizado
            WHERE d.RELA_pedido = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pedido['ID_pedido']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tu Carrito - Cake Party</title>
  <link rel="stylesheet" href="../../style.css">
</head>
<body>
  <div class="contenedor-principal">
    <h1>🧺 Tu Carrito</h1>

    <?php if (empty($items)): ?>
      <p>No tienes productos en tu carrito.</p>
      <a href="productos_finalizados.php" class="btn-volver">Volver a Productos</a>
    <?php else: ?>
      <table class="tabla-carrito">
        <tr><th>Producto</th><th>Cantidad</th></tr>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['producto_nombre']) ?></td>
            <td><?= $item['cantidad'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>

      <a href="../../controllers/cliente/finalizar_pedido.php" class="btn-finalizar">Finalizar Pedido 💳</a>
    <?php endif; ?>
  </div>
</body>
</html>
