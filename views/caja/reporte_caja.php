
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
        color: #000;
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
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 20px;
    }

    th, td {
        border: 1px solid #333;
        padding: 6px 8px;
        text-align: right;
    }

    th {
        background: #f2f2f2;
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
        border-bottom: 2px solid #000;
        margin-top: 25px;
    }

    @media print {
        .no-print { display: none; }
        body { margin: 0; }
    }
</style>
</head>

<body>

<div class="header">
    <img src="logo.png" alt="Logo"> <!-- Tu logo -->
    <div><strong>Reporte de Caja</strong></div>
    <div>Fecha: <?= date("d/m/Y H:i") ?></div>
    <div>Usuario: <?= $usuario_nombre ?></div>
</div>

<h2>Inicio de Caja</h2>

<table>
    <tr>
        <th class="left">Fecha</th>
        <td><?= $caja['caja_fecha_apertura'] ?></td>
    </tr>
    <tr>
        <th class="left">Hora</th>
        <td><?= $caja['caja_hora_apertura'] ?></td>
    </tr>
    <tr>
        <th class="left">Usuario</th>
        <td><?= $usuario_nombre ?></td>
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
</table>

<table>
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
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha - Hora</th>
        <th>N° Recibo</th>
        <th>Total</th>
        <th>Estado</th>
    </tr>
    <?php foreach ($ventas_contado as $v): ?>
    <tr>
        <td class="left"><?= $v['id'] ?></td>
        <td class="left"><?= $v['usuario'] ?></td>
        <td><?= $v['fecha_hora'] ?></td>
        <td><?= $v['recibo'] ?></td>
        <td>$ <?= number_format($v['total'], 2) ?></td>
        <td><?= $v['estado'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<table>
    <tr>
        <th class="left">Monto Total en Contado</th>
        <td>$ <?= number_format($total_contado, 2) ?></td>
    </tr>
</table>

<h2>Ventas Mercado Pago / QR</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha - Hora</th>
        <th>N° Recibo</th>
        <th>Total</th>
        <th>Estado</th>
    </tr>
    <?php foreach ($ventas_mp as $v): ?>
    <tr>
        <td class="left"><?= $v['id'] ?></td>
        <td class="left"><?= $v['usuario'] ?></td>
        <td><?= $v['fecha_hora'] ?></td>
        <td><?= $v['recibo'] ?></td>
        <td>$ <?= number_format($v['total'], 2) ?></td>
        <td><?= $v['estado'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<table>
    <tr>
        <th class="left">Monto Total en MP / QR</th>
        <td>$ <?= number_format($total_mp, 2) ?></td>
    </tr>
</table>

<h2>Ventas de Transferencia</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha - Hora</th>
        <th>N° Recibo</th>
        <th>Total</th>
        <th>Estado</th>
    </tr>
    <?php foreach ($ventas_transf as $v): ?>
    <tr>
        <td class="left"><?= $v['id'] ?></td>
        <td class="left"><?= $v['usuario'] ?></td>
        <td><?= $v['fecha_hora'] ?></td>
        <td><?= $v['recibo'] ?></td>
        <td>$ <?= number_format($v['total'], 2) ?></td>
        <td><?= $v['estado'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<table>
    <tr>
        <th class="left">Monto Total en Transferencia</th>
        <td>$ <?= number_format($total_transferencia, 2) ?></td>
    </tr>
</table>

<button class="no-print" onclick="window.print()">🖨 Imprimir</button>

</body>
</html>
