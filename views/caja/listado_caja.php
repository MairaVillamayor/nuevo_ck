<?php
session_start();
require_once __DIR__ . '/../../models/caja/caja.php';

$cajaModel = new Caja();
$cajas = $cajaModel->obtenerCajas();

if (!is_array($cajas)) {
    $cajas = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cajas | Cake Party</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        thead.table-cakeparty {
            background: #ff79c6 !important;
            color: white !important;
        }

        .text-pink {
            color: #ff4fa3;
        }

        .btn-primary {
            background-color: #ff4fa3;
            border-color: #ff4fa3;
        }

        .btn-primary:hover {
            background-color: #ff2d8f;
            border-color: #ff2d8f;
        }

        .badge-cake-open {
            background-color: #ff4fa3 !important;
            /* rosa fuerte */
            color: white !important;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .badge-cake-closed {
            background-color: #d6c6d8 !important;
            /* gris pastel */
            color: #4a3b4f !important;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include("../../includes/navegacion.php"); ?>
    <?php include("../../includes/header.php"); ?>
    <?php include("../../includes/alerts.php"); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4 text-pink">💰 Gestión de Cajas</h2>

        <div class="mb-3 d-flex gap-2">
            <a href="apertura.php" class="btn btn-primary">Abrir Caja</a>
            <a href="arqueo_caja.php" class="btn btn-secondary">Arqueo de Caja</a>
            <a href="listado_gastos.php" class="btn btn-primary">Registrar Gastos</a>
        </div>

        <div class="card shadow-sm p-3 bg-light rounded-4">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-cakeparty text-center">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Usuario Apertura</th>
                            <th rowspan="2">Fecha Apertura</th>
                            <th rowspan="2">Fecha Cierre</th>
                            <th colspan="2">Monto Inicial</th>
                            <th colspan="2">Ingresos</th>
                            <th colspan="2">Egresos</th>
                            <th colspan="2">Saldo Esperado</th>
                            <th colspan="2">Arqueo Real Final</th>
                            <th rowspan="2">Estado</th>
                            <th rowspan="2">Acciones</th>
                        </tr>
                        <tr>
                            <th>Efectivo</th>
                            <th>Transferencia</th>
                            <th>Efectivo</th>
                            <th>Transferencia</th>
                            <th>Efectivo</th>
                            <th>Transferencia</th>
                            <th>Efectivo</th>
                            <th>Transferencia</th>
                            <th>Efectivo</th>
                            <th>Transferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cajas)): ?>
                            <tr>
                                <td colspan="16" class="text-center text-muted fs-5">💸 No hay cajas registradas aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cajas as $caja): ?>

                                <?php
                                // Monto inicial
                                $monto_inicial_efectivo       = (float)($caja['caja_monto_inicial_efectivo'] ?? 0);
                                $monto_inicial_transferencia  = (float)($caja['caja_monto_inicial_transferencia'] ?? 0);

                                // Monto final (solo para arqueo)
                                $monto_final_efectivo         = (float)($caja['caja_monto_final_efectivo'] ?? 0);
                                $monto_final_transferencia    = (float)($caja['caja_monto_final_transferencia'] ?? 0);

                                // Movimientos reales
                                $mov = $cajaModel->obtenerEgresosIngresosPorMetodo($caja['ID_caja'] ?? 0);

                                $ing_efe   = (float)($mov['ingreso_efectivo'] ?? 0);
                                $ing_trans = (float)($mov['ingreso_transferencia'] ?? 0);

                                $eg_efe    = (float)($mov['egreso_efectivo'] ?? 0);
                                $eg_trans  = (float)($mov['egreso_transferencia'] ?? 0);

                                // Saldo esperado
                                $saldo_efe  = $monto_inicial_efectivo      + $ing_efe  - $eg_efe;
                                $saldo_trans = $monto_inicial_transferencia + $ing_trans - $eg_trans;
                                ?>

                                <tr class="text-center">
                                    <td><?= htmlspecialchars($caja['ID_caja'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($caja['persona'] ?? 'Desconocido') ?></td>
                                    <td><?= isset($caja['caja_fecha_apertura']) ? date('d-m-Y H:i', strtotime($caja['caja_fecha_apertura'])) : '-' ?></td>
                                    <td><?= !empty($caja['caja_fecha_cierre']) ? date('d-m-Y H:i', strtotime($caja['caja_fecha_cierre'])) : '-' ?></td>

                                    <td>$<?= number_format($monto_inicial_efectivo, 2) ?></td>
                                    <td>$<?= number_format($monto_inicial_transferencia, 2) ?></td>

                                    <td class="text-success">$<?= number_format($ing_efe, 2) ?></td>
                                    <td class="text-success">$<?= number_format($ing_trans, 2) ?></td>

                                    <td class="text-danger">$<?= number_format($eg_efe, 2) ?></td>
                                    <td class="text-danger">$<?= number_format($eg_trans, 2) ?></td>

                                    <td>$<?= number_format($saldo_efe, 2) ?></td>
                                    <td>$<?= number_format($saldo_trans, 2) ?></td>

                                    <td><?= $monto_final_efectivo > 0 ? '$' . number_format($monto_final_efectivo, 2) : '-' ?></td>
                                    <td><?= $monto_final_transferencia > 0 ? '$' . number_format($monto_final_transferencia, 2) : '-' ?></td>

                                    <td>
                                        <?php if (($caja['caja_estado'] ?? '') === 'abierta'): ?>
                                            <span class="badge-cake-open">Abierta</span>
                                        <?php else: ?>
                                            <span class="badge-cake-closed">Cerrada</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a href="reporte_caja.php?id_caja=<?= urlencode($caja['ID_caja']) ?>" class="btn btn-sm btn-outline-primary">
                                            Imprimir
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AUTO-MOSTRAR ALERTA NUEVA -->
    <?php if (isset($_SESSION['message'], $_SESSION['status'])): ?>
        <script>
            showCakeAlert("<?= $_SESSION['status'] === 'success' ? '¡Éxito!' : 'Aviso' ?>",
                "<?= htmlspecialchars($_SESSION['message']) ?>");
        </script>
        <?php unset($_SESSION['message'], $_SESSION['status']); ?>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>