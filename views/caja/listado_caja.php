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
    <title>Gestión de Cajas | Cake Party</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        body {
            background: #fff5f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .contenido {
            padding: 40px;
        }

        h2 {
            color: #ff2d8f;
            margin-bottom: 20px;
        }

        .acciones-superiores {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn-cake {
            background: #ff4fa3;
            color: white;
            border: none;
            padding: 12px 22px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-cake:hover {
            background: #ff2d8f;
        }

        .card-cake {
            background: white;
            padding: 25px;
            border-radius: 25px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 18px;
            overflow: hidden;
        }

        thead {
            background: #ffc2d8;
            color: #4a0033;
        }

        thead th {
            padding: 12px;
            font-size: 14px;
        }

        tbody td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ffe0ec;
        }

        tbody tr:hover {
            background: #fff1f7;
        }

        .success {
            color: #2e7d32;
            font-weight: bold;
        }

        .danger {
            color: #c62828;
            font-weight: bold;
        }

        .badge-abierta {
            background-color: #ff4fa3;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .badge-cerrada {
            background-color: #e0cdd8;
            color: #5a3f4f;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-outline {
            border: 2px solid #ff4fa3;
            background: transparent;
            color: #ff4fa3;
            padding: 7px 16px;
            border-radius: 16px;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-outline:hover {
            background: #ff4fa3;
            color: white;
        }

        @media (max-width: 900px) {
            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

<?php include("../../includes/navegacion.php"); ?>
<?php include("../../includes/header.php"); ?>

<div class="contenido">

    <h2>💰 Gestión de Cajas</h2>

    <div class="acciones-superiores">
        <a href="apertura.php" class="btn-cake">Abrir Caja</a>
        <a href="arqueo_caja.php" class="btn-cake">Arqueo</a>
        <a href="listado_gastos.php" class="btn-cake">Registrar Gastos</a>
    </div>

    <div class="card-cake">
        <table>
            <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Usuario</th>
                <th rowspan="2">Fecha Apertura</th>
                <th rowspan="2">Fecha Cierre</th>
                <th colspan="2">Monto Inicial</th>
                <th colspan="2">Ingresos</th>
                <th colspan="2">Egresos</th>
                <th colspan="2">Saldo Esperado</th>
                <th colspan="2">Arqueo Real</th>
                <th rowspan="2">Estado</th>
                <th rowspan="2">Acción</th>
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
                    <td colspan="16">💸 No hay cajas registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cajas as $caja): ?>
                    <?php
                    $monto_inicial_efectivo      = (float)($caja['caja_monto_inicial_efectivo'] ?? 0);
                    $monto_inicial_transferencia = (float)($caja['caja_monto_inicial_transferencia'] ?? 0);

                    $monto_final_efectivo  = (float)($caja['caja_monto_final_efectivo'] ?? 0);
                    $monto_final_transferencia      = (float)($caja['caja_monto_final_transferencia'] ?? 0);

                    $mov = $cajaModel->obtenerEgresosIngresosPorMetodo($caja['ID_caja']);

                   // echo "<pre>";var_dump($mov); echo "</pre>";

                    // echo "Caja: {$caja['ID_caja']}<br>";echo "Ing. efec: {$mov['ingreso_efectivo']}<br>";
                    // echo "Egr. efec: {$mov['egreso_efectivo']}<hr>";

                    $ingreso_efectivo = (float)($mov['ingreso_efectivo'] ?? 0);
                    $ingreso_transferencia  = (float)($mov['ingreso_transferencia'] ?? 0);
                    $egreso_efectivo  = (float)($mov['egreso_efectivo'] ?? 0);
                    $egreso_transferencia   = (float)($mov['egreso_transferencia'] ?? 0);

                    $saldo_efectivo = $monto_inicial_efectivo + $ingreso_efectivo - $egreso_efectivo;
                    $saldo_transferencia  = $monto_inicial_transferencia + $ingreso_transferencia - $egreso_transferencia;
                    ?>

                    <tr>
                        <td><?= $caja['ID_caja'] ?></td>
                        <td><?= htmlspecialchars($caja['persona'] ?? 'Desconocido') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($caja['caja_fecha_apertura'])) ?></td>
                        <td><?= $caja['caja_fecha_cierre'] ? date('d/m/Y H:i', strtotime($caja['caja_fecha_cierre'])) : '-' ?></td>

                        <td>$<?= number_format($monto_inicial_efectivo, 2) ?></td>
                        <td>$<?= number_format($monto_inicial_transferencia, 2) ?></td>

                        <td class="success">$<?= number_format($ingreso_efectivo, 2) ?></td>
                        <td class="success">$<?= number_format($ingreso_transferencia, 2) ?></td>

                        <td class="danger">$<?= number_format($egreso_efectivo, 2) ?></td>
                        <td class="danger">$<?= number_format($egreso_transferencia, 2) ?></td>

                        <td>$<?= number_format($saldo_efectivo, 2) ?></td>
                        <td>$<?= number_format($saldo_transferencia, 2) ?></td>

                        <td><?= $monto_final_efectivo > 0 ? '$'.number_format($monto_final_efectivo,2) : '-' ?></td>
                        <td><?= $monto_final_transferencia > 0 ? '$'.number_format($monto_final_transferencia,2) : '-' ?></td>

                        <td>
                            <?php if ($caja['caja_estado'] === "abierta"): ?>
                                <span class="badge-abierta">Abierta</span>
                            <?php else: ?>
                                <span class="badge-cerrada">Cerrada</span>
                            <?php endif; ?>
                        </td>

                        <td>

                            <a class="btn-outline" href="../../controllers/caja/reporte_caja_controlador.php?id_caja=<?= $caja['ID_caja'] ?>">
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

<?php if (isset($_SESSION['message'], $_SESSION['status'])): ?>
<script>
    showCakeAlert(
        "<?= $_SESSION['status'] === 'success' ? '¡Éxito!' : 'Aviso' ?>",
        "<?= $_SESSION['message'] ?>"
    );
</script>
<?php unset($_SESSION['message'], $_SESSION['status']); endif; ?>

</body>
</html>
