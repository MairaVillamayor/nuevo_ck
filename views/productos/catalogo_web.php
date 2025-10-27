<?php
require_once __DIR__ . '/../../config/conexion.php'; // ajustar si tu config está en otro lugar
session_start();

$pdo = getConexion();

$sql = "SELECT pf.*, c.categoria_producto_finalizado_nombre
        FROM producto_finalizado pf
        LEFT JOIN categoria_producto_finalizado c
          ON pf.RELA_categoria_producto_finalizado = c.ID_categoria_producto_finalizado
        WHERE pf.disponible_web = 1 AND pf.stock_actual > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Catálogo - Cake Party</title>
  <link rel="stylesheet" href="/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../../includes/navegacion.php'; ?>

<div class="container py-4">
  <h1 class="mb-4">Productos disponibles</h1>

  <div class="row">
    <?php if (!empty($productos)): ?>
      <?php foreach ($productos as $p): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <?php $img = !empty($p['imagen_url']) ? $p['imagen_url'] : '/img/default.png'; ?>
            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:220px;object-fit:cover;" alt="<?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Producto') ?>">

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Sin nombre') ?></h5>
              <p class="card-text"><?= htmlspecialchars($p['producto_finalizado_descri'] ?? 'Sin descripción') ?></p>
              <p class="mb-1"><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria_producto_finalizado_nombre'] ?? 'Sin categoría') ?></p>
              <p class="h5 text-success mt-auto">$<?= number_format($p['producto_finalizado_precio'] ?? 0, 2, ',', '.') ?></p>

              <form method="post" action="<?= htmlspecialchars('../../controllers/productos/agregar_carrito.php') ?>">
                <input type="hidden" name="producto_id" value="<?= (int)$p['ID_producto_finalizado'] ?>">
                <div class="d-flex gap-2 mt-2">
                  <input type="number" name="cantidad" value="1" min="1" max="<?= (int)$p['stock_actual'] ?>" class="form-control" style="width:100px;">
                  <button type="submit" class="btn btn-pink" style="background:#ff6fa1;color:#fff;border:none;">Agregar al carrito</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p>No hay productos disponibles por el momento.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
