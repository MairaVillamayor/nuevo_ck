<?php
require_once("../../config/conexion.php");
session_start();

if (!isset($_SESSION['ID_usuario'])) {
    echo "Debes iniciar sesión para ver esta página.";
    exit;
}

$id_pedido = $_GET['id'] ?? null;
if (!$id_pedido) {
    echo "Pedido no válido.";
    exit;
}

// Traer pedido + detalle
$stmt = $pdo->prepare("
    SELECT p.ID_pedido, p.pedido_fecha, p.pedido_total, p.pedido_direccion_envio,
           e.estado_descri AS estado, m.metodo_pago_descri AS metodo_pago
    FROM pedido p
    LEFT JOIN estado e ON p.RELA_estado = e.ID_estado
    LEFT JOIN metodo_pago m ON p.RELA_metodo_pago = m.ID_metodo_pago
    WHERE p.ID_pedido = ? AND p.RELA_usuario = ?
");
$stmt->execute([$id_pedido, $_SESSION['ID_usuario']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo "No se encontró el pedido.";
    exit;
}

// Traer detalle del pastel
$stmt = $pdo->prepare("
    SELECT d.pedido_detalle_cantidad, d.pedido_detalle_precio_unitario, 
           d.pedido_detalle_subtotal, d.pedido_detalle_precio_total,
           b.base_pastel_descri, b.base_pastel_precio,
           deco.decoracion_descri, deco.decoracion_precio
    FROM pedido_detalle d
    INNER JOIN pastel_personalizado pp ON d.RELA_pastel = pp.ID_pastel_personalizado
    LEFT JOIN base_pastel b ON pp.RELA_base_pastel = b.ID_base_pastel
    LEFT JOIN decoracion deco ON pp.RELA_decoracion = deco.ID_decoracion
    WHERE d.RELA_pedido = ?
");
$stmt->execute([$id_pedido]);
$detalle = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmación de Pedido</title>
  <style>
    body {
  font-family: Arial, sans-serif;
  background: #fff8f5;
  margin: 0;
  padding: 20px;
}

.container {
  max-width: 700px;
  margin: auto;
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h1 {
  color: #d35400;
  text-align: center;
}

h2 {
  margin-top: 20px;
  color: #444;
}

ul {
  list-style: none;
  padding: 0;
}

ul li {
  background: #fbeee6;
  margin: 5px 0;
  padding: 10px;
  border-radius: 6px;
}

.total {
  font-size: 1.5em;
  color: #27ae60;
  font-weight: bold;
  text-align: center;
}

.acciones {
  margin-top: 20px;
  text-align: center;
}

.btn, .btn-cancel {
  padding: 10px 20px;
  margin: 10px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1em;
}

.btn {
  background: #d35400;
  color: #fff;
}

.btn:hover {
  background: #e67e22;
}

.btn-cancel {
  background: #c0392b;
  color: #fff;
}

.btn-cancel:hover {
  background: #e74c3c;
}
    </style>
</head>
<body>
  <div class="container">
    <h1>🎉 Pedido confirmado</h1>
    <p><strong>N° Pedido:</strong> <?= htmlspecialchars($pedido['ID_pedido']) ?></p>
    <p><strong>Fecha:</strong> <?= htmlspecialchars($pedido['pedido_fecha']) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($pedido['estado'] ?? 'Pendiente') ?></p>
    <p><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago'] ?? 'No seleccionado') ?></p>
    <p><strong>Dirección de envío:</strong> <?= htmlspecialchars($pedido['pedido_direccion_envio']) ?></p>

    <h2>🍰 Detalles del pastel</h2>
    <?php if ($detalle): ?>
      <ul>
        <li>Base: <?= htmlspecialchars($detalle['base_pastel_descri'] ?? 'N/A') ?> (<?= $detalle['base_pastel_precio'] ?? 0 ?>)</li>
        <li>Decoración: <?= htmlspecialchars($detalle['decoracion_descri'] ?? 'N/A') ?> (<?= $detalle['decoracion_precio'] ?? 0 ?>)</li>
        <li>Cantidad: <?= $detalle['pedido_detalle_cantidad'] ?></li>
      </ul>
    <?php else: ?>
      <p>No se encontraron detalles del pastel.</p>
    <?php endif; ?>

    <h2>💵 Total a pagar</h2>
    <p class="total">$ <?= number_format($pedido['pedido_total'], 2) ?></p>

    <div class="acciones">
      <form action="pago.php" method="get">
        <input type="hidden" name="id_pedido" value="<?= $pedido['ID_pedido'] ?>">
        <button type="submit" class="btn">Ir a pagar</button>
      </form>
      <form action="cancelar_pedido.php" method="post">
        <input type="hidden" name="id_pedido" value="<?= $pedido['ID_pedido'] ?>">
        <button type="submit" class="btn-cancel">Cancelar pedido</button>
      </form>
    </div>
  </div>
</body>
</html>
