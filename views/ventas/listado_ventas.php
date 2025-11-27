<?php
require_once("../../config/conexion.php");
require_once("../../models/ventas/factura.php");
include("../../includes/navegacion.php");
// include("../../includes/header.php");

session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../../index.php?error=not_logged');
  exit;
}

$factura = new Factura();
$cliente_filtro = isset($_GET['cliente']) ? htmlspecialchars($_GET['cliente']) : null;

$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : null;
$limite = 10; // cantidad de facturas por página
$pagina = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($pagina - 1) * $limite;

$totalFacturas = $factura->get_total_facturas_con_filtros($cliente_filtro, $fecha_filtro);
$totalPaginas = ceil($totalFacturas / $limite);

$facturas = $factura->get_facturas_con_filtros(
  $cliente_filtro,
  $fecha_filtro,
  null,      // fechaHasta
  $limite,
  $offset
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
  <style>
    .estado-badge {
      color: #ffffffff;
      padding: 0.4em 0.8em;
      border-radius: 0.5rem;
      font-weight: bold;
    }

    .estado-badge.pagado {
      background-color: #69d883ff;
    }

    .estado-badge.otro {
      background-color: #ecc6c6ff;
    }

    /* Contenedor centrado */
    .pagination-rosa {
      text-align: center;
    }

    /* Botones de paginación */
    .page-btn {
      display: inline-block;
      padding: 8px 14px;
      margin: 0 4px;
      background-color: #ffb6c1;
      /* rosa claro */
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.2s;
    }

    .page-btn:hover {
      background-color: #ff69b4;
      /* rosa fuerte */
      color: white;
    }

    /* Página activa */
    .page-btn.active {
      background-color: #ff1493;
      /* rosa más intenso */
      color: white;
    }
  </style>
</head>

<body class="fondo-rosa">



  <div class="container mt-4">
    <h2 class="text-pink mb-4">🧾 Listado de Facturas</h2>
    <a href="registrar_venta.php" class="btn btn-primary">Crear Factura</a>
    <a href="../admin/admin_dashboard.php" class="btn btn-secondary">Volver al dashboard</a>


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
            <button class="btn btn-primary" type="submit">Buscar</button>
            <a href="listado_ventas.php" class="btn btn-light">Limpiar</a>
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
                    <span class="badge estado-badge <?= ($f['estado'] == 'Pagado') ? 'pagado' : 'otro' ?>">
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
        <!-- Paginación personalizada centrada -->
        <div class="pagination-rosa mt-4">
          <?php if ($pagina > 1): ?>
            <a class="page-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagina - 1])) ?>"><</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a class="page-btn <?= ($i == $pagina) ? 'active' : '' ?>"
              href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if ($pagina < $totalPaginas): ?>
            <a class="page-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagina + 1])) ?>">></a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>

</body>

</html>