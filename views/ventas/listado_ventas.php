<?php
require_once("../../config/conexion.php");
require_once("../../models/factura.php");
include("../../includes/navegacion.php");

session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../../index.php?error=not_logged');
  exit;
}

$factura = new Factura();
$cliente_filtro = isset($_GET['cliente']) ? htmlspecialchars($_GET['cliente']) : null;

$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : null;

$facturas = $factura->get_facturas_con_filtros(
    $cliente_filtro, 
    $fecha_filtro
);

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
  <?php include("../../includes/header.php"); ?>

  <div class="container mt-4">
    <h2 class="text-pink mb-4">🧾 Listado de Facturas</h2>
    <a href="registrar_venta.php" class="btn btn-primary">Crear Factura</a>


    <div class="card mb-3 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="cliente" class="form-control" 
                       placeholder="Buscar cliente (Nombre, Apellido o Documento)..." 
                       value="<?= htmlspecialchars($cliente_filtro ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="date" name="fecha" class="form-control"
                       value="<?= htmlspecialchars($fecha_filtro ?? '') ?>">
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-pink" type="submit">Buscar</button>
                <a href="listado_facturas.php" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
    <span class="badge bg-<?= ($f['estado'] == 'Pagado') ? 'success' : 'secondary' ?>">
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