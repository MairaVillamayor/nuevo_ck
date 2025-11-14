<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();

$pdo = getConexion();

// Consulta de productos disponibles
$sql = "SELECT pf.*, c.categoria_producto_finalizado_nombre
        FROM producto_finalizado pf
        LEFT JOIN categoria_producto_finalizado c
          ON pf.RELA_categoria_producto_finalizado = c.ID_categoria_producto_finalizado
        WHERE pf.disponible_web = 1 AND pf.stock_actual > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ruta_base = '/nuevo_ck/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Catálogo - Cake Party</title>
  <link rel="stylesheet" href="<?= $ruta_base ?>css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.02);
    }
    .btn-pink {
      background-color: #ff6fa1;
      color: #fff;
      border: none;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .btn-pink:hover {
      background-color: #ff4081;
      color: #fff;
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navegacion.php'; ?>

<div class="container py-4">
  <h1 class="mb-4 text-center text-secondary">🎂 Catálogo de Productos</h1>

  <div class="row">
    <?php if (!empty($productos)): ?>
      <?php foreach ($productos as $p): ?>
        <?php
        // Determinar la imagen con ruta base
        $img = !empty($p['imagen_url'])
            ? $ruta_base . ltrim($p['imagen_url'], '/')
            : $ruta_base . 'img/default.png';
        ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="<?= htmlspecialchars($img) ?>"
                 class="card-img-top"
                 style="height:220px;object-fit:cover;"
                 alt="<?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Producto') ?>">

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Sin nombre') ?></h5>
              <p class="card-text"><?= htmlspecialchars($p['producto_finalizado_descri'] ?? 'Sin descripción') ?></p>
              <p class="mb-1"><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria_producto_finalizado_nombre'] ?? 'Sin categoría') ?></p>
              <p class="h5 text-success mt-auto">$<?= number_format($p['producto_finalizado_precio'] ?? 0, 2, ',', '.') ?></p>

              <form method="post" action="<?= htmlspecialchars($ruta_base . 'controllers/productos/agregar_carrito.php') ?>">
                <input type="hidden" name="producto_id" value="<?= (int)$p['ID_producto_finalizado'] ?>">
                <div class="d-flex gap-2 mt-2">
                  <input type="number" name="cantidad" value="1" min="1" max="<?= (int)$p['stock_actual'] ?>" class="form-control" style="width:100px;">
                  <button type="submit" class="btn btn-pink flex-grow-1">Agregar al carrito</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p class="text-center text-muted">No hay productos disponibles por el momento.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
