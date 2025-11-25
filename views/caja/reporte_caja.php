<?php
include("../../includes/navegacion.php");

$usuario_nombre = $datos_caja['usuario_nombre'] ?? 'Desconocido';
$caja = $datos_caja; // Datos de la caja
$ventas_contado = $ventas_efectivo ?? [];
$ventas_transf = $ventas_transferencia ?? [];
$total_contado = $total_efectivo ?? 0;
$total_transferencia = $total_transferencia ?? 0;
$ingresos_extras = 0; // si aplica
$total_ventas = $total_contado + $total_transferencia;
$total_general = $total_ventas + $ingresos_extras;
$retiros = $caja['caja_total_egresos'] ?? 0;
$fondo_caja = $caja['caja_monto_inicial_efectivo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        margin: 20px;
        color: #d6336c; /* color rosa */
        background-color: #ffe6f0; /* fondo rosa claro */
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header img {
        width: 140px;
        margin-bottom: 10px;
    }

    h2 {
        text-align: center;
        margin-top: 40px;
        margin-bottom: 10px;
        text-transform: uppercase;
        color: #d6336c; /* encabezados en rosa */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 20px;
        color: #d6336c; /* texto de tabla en rosa */
    }

    th,
    td {
        border: 1px solid #d6336c; /* bordes en rosa */
        padding: 6px 8px;
        text-align: right;
    }

    th {
        background: #ffd6e6; /* cabecera de tabla rosa más claro */
        text-align: center;
    }

    .left {
        text-align: left;
    }

    .no-border td {
        border: none;
    }

    .section-title {
        font-weight: bold;
        font-size: 16px;
        padding: 8px 0;
        border-bottom: 2px solid #d6336c;
        margin-top: 25px;
        color: #d6336c;
    }

    @media print {
        .no-print {
            display: none;
        }

        body {
            margin: 0;
        }
    }
</style>

</head>
<body>

<div class="header">
    <img src="logo.png" alt="Logo">
    <div><strong>Reporte de Caja</strong></div>
    <div>Fecha: <?= date("d/m/Y H:i") ?></div>
    <div>Usuario: <?= htmlspecialchars($usuario_nombre) ?></div>
</div>

<h2>Inicio de Caja</h2>
<table>
    <tr>
        <th class="left">Fecha</th>
        <td><?= date("d/m/Y", strtotime($caja['caja_fecha_apertura'])) ?></td>
    </tr>
    <tr>
        <th class="left">Hora</th>
        <td><?= date("H:i:s", strtotime($caja['caja_fecha_apertura'])) ?></td>
    </tr>
    <tr>
        <th class="left">Usuario</th>
        <td><?= htmlspecialchars($usuario_nombre) ?></td>
    </tr>
    <tr>
        <th class="left">Monto de Inicio</th>
        <td>$ <?= number_format($caja['caja_monto_inicial_efectivo'], 2) ?></td>
    </tr>
</table>

<h2>Arqueo de Caja</h2>
<table>
    <tr>
        <th class="left">Monto Inicio</th>
        <td>$ <?= number_format($caja['caja_monto_inicial_efectivo'], 2) ?></td>
    </tr>
    <tr>
        <th class="left">Monto Total de Ventas</th>
        <td>$ <?= number_format($total_ventas, 2) ?></td>
    </tr>
    <tr>
        <th class="left">Monto Ingresos Extras</th>
        <td>$ <?= number_format($ingresos_extras, 2) ?></td>
    </tr>
    <tr>
        <th class="left">Monto Total General</th>
        <td>$ <?= number_format($total_general, 2) ?></td>
    </tr>
    <tr>
        <th class="left">Retiros</th>
        <td>$ <?= number_format($retiros, 2) ?></td>
    </tr>
    <tr>
        <th class="left">Fondo de Caja</th>
        <td>$ <?= number_format($fondo_caja, 2) ?></td>
    </tr>
    <tr>
        <th class="left">Monto de Cierre</th>
        <td>$ <?= number_format($caja['caja_monto_final_efectivo'], 2) ?></td>
    </tr>
</table>

<h2>Ventas de Contado</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Fecha - Hora</th>
            <th>N° Recibo</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas_contado as $v): ?>
            <tr>
                <td class="left"><?= $v['ID_factura'] ?></td>
                <td class="left"><?= htmlspecialchars($v['usuario_nombre']) ?></td>
                <td><?= $v['factura_fecha_emision'] ?></td>
                <td><?= $v['ID_factura'] ?></td>
                <td>$ <?= number_format($v['factura_total'], 2) ?></td>
                <td><?= $v['estado_factura_descri'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="left">Monto Total en Contado</td>
            <td colspan="2">$ <?= number_format($total_contado, 2) ?></td>
        </tr>
    </tfoot>
</table>

<h2>Ventas de Transferencia</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Fecha - Hora</th>
            <th>N° Recibo</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas_transf as $v): ?>
            <tr>
                <td class="left"><?= $v['ID_factura'] ?></td>
                <td class="left"><?= htmlspecialchars($v['usuario_nombre']) ?></td>
                <td><?= $v['factura_fecha_emision'] ?></td>
                <td><?= $v['ID_factura'] ?></td>
                <td>$ <?= number_format($v['factura_total'], 2) ?></td>
                <td><?= $v['estado_factura_descri'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="left">Monto Total en Transferencia</td>
            <td colspan="2">$ <?= number_format($total_transferencia, 2) ?></td>
        </tr>
    </tfoot>
</table>

<div class="text-right">
    <button class="no-print" onclick="window.print()">🖨 Imprimir</button>
</div>

</body>
</html>
