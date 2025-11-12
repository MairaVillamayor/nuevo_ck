<?php
require_once __DIR__ . '/../../models/caja/caja.php';

$cajaModel = new Caja();
$cajas = $cajaModel->obtenerCajas();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Cajas | Cake Party</title>

 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="../../public/css/caja_dashboard.css">

</head>
<body class="bg-light">
<?php include("../../includes/navegacion.php"); ?>
<?php 
if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
    $message = htmlspecialchars($_SESSION['message']);
    $status = htmlspecialchars($_SESSION['status']);
    echo "<div class='alert alert-$status' role='alert'>$message</div>";

    unset($_SESSION['message']);
    unset($_SESSION['status']);
}
?>
<div class="container mt-4">
    <h2 class="mb-4 text-pink">💰 Gestión de Cajas</h2>

    <div class="mb-3 d-flex gap-2">
        <a href="apertura.php" class="btn btn-primary">Abrir Caja</a>
        <a href="arqueo_caja.php" class="btn btn-secondary">Arqueo de Caja</a>
        <a href="listado_gastos.php" class="btn btn-primary">Registrar Gastos</a>
    </div>

    <div class="card shadow-sm p-3 bg-light rounded-4">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Fecha Apertura</th>
                    <th>Fecha Cierre</th>
                    <th>Monto Inicial</th>
                    <th>Ingresos</th>
                    <th>Egresos</th>
                    <th>Saldo Final</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cajas)): ?>
                    <tr>
                        <td colspan="10" class="text-center">
                            <span class="text-muted fs-5">💸 No hay cajas registradas aún.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cajas as $caja): ?>
                        <tr>
                            <td><?= htmlspecialchars($caja['id_caja'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($caja['persona'] ?? 'Desconocido') ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($caja['fecha_apertura'] ?? '')) ?></td>
                            <td><?= $caja['fecha_cierre'] ? date('d-m-Y H:i', strtotime($caja['fecha_cierre'])) : '-' ?></td>
                            <td>$<?= number_format($caja['monto_inicial'] ?? 0, 2) ?></td>
                            <td>$<?= number_format($caja['ingresos'] ?? 0, 2) ?></td>
                            <td>$<?= number_format($caja['egresos'] ?? 0, 2) ?></td>
                            <td>$<?= number_format($caja['saldo_final'] ?? 0, 2) ?></td>
                            <td>
                                <?php if (($caja['estado'] ?? '') === 'Abierta'): ?>
                                    <span class="badge bg-success">Abierta</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cerrada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="caja.php?id_caja=<?= $caja['id_caja'] ?>" class="btn btn-sm btn-outline-primary">👁️ Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
