<?php
require_once("../../config/conexion.php");
include("../../includes/navegacion.php");

session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../../index.php?error=not_logged');
  exit;
}

$pdo = getConexion();

$sql = " SELECT 
    f.ID_factura,
    f.factura_fecha_emision,
    CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS cliente,
    f.factura_subtotal,
    f.factura_iva_tasa,
    f.factura_iva_monto,
    f.factura_total,
    ef.estado_factura_descri AS estado
FROM factura f
LEFT JOIN persona p ON f.RELA_persona = p.ID_persona
LEFT JOIN estado_factura ef ON f.RELA_estado_factura = ef.ID_estado_factura
ORDER BY f.factura_fecha_emision DESC
";
$stmt = $pdo->query($sql);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Facturas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/caja_dashboard.css">
</head>

<body>

  <div class="container mt-4">
    <h2 class="text-pink mb-4">🧾 Listado de Facturas</h2>
    <a href="nueva_venta.php" class="btn btn-primary">Crear Factura</a>

    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <form method="GET" class="row g-2">
          <div class="col-md-3">
            <input type="text" name="cliente" class="form-control" placeholder="Buscar cliente...">
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_desde" class="form-control">
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_hasta" class="form-control">
          </div>
          <div class="col-md-3 text-end">
            <button class="btn btn-pink" type="submit">Buscar</button>
            <a href="listado_facturas.php" class="btn btn-secondary">Limpiar</a>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="table-pink text-white">
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Subtotal</th>
              <th>IVA</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($facturas) > 0): ?>
              <?php foreach ($facturas as $f): ?>
               <tr>
  <td><?= $f['ID_factura'] ?></td>
  <td><?= date("d/m/Y H:i", strtotime($f['factura_fecha_emision'])) ?></td>
  <td><?= htmlspecialchars($f['cliente'] ?? 'Sin nombre') ?></td>

  <td>
    $<?= number_format((float)($f['factura_subtotal'] ?? 0), 2, ',', '.') ?>
  </td>
  <td>
    $<?= number_format((float)($f['factura_iva_monto'] ?? 0), 2, ',', '.') ?>
  </td>
  <td>
    <strong>$<?= number_format((float)($f['factura_total'] ?? 0), 2, ',', '.') ?></strong>
  </td>
  <td>
    <span class="badge bg-<?= ($f['estado'] == 'Activo') ? 'success' : 'secondary' ?>">
      <?= $f['estado'] ?>
    </span>
  </td>
  <td>
    <a href="imprimir_venta.php?idFactura=<?= $f['ID_factura'] ?>" class="btn btn-sm btn-outline-secondary">🖨️</a>
  </td>
</tr>

              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center text-muted">No hay facturas registradas.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>

</html>