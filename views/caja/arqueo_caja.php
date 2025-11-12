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
<div class="container cake-card p-4">
    <?php
    require_once("../../config/conexion.php");
    require_once("../../models/caja/caja.php");
    session_start(); 
    
    $caja = new Caja();
    $caja_abierta = $caja->getCajaAbierta(); 

    if (!$caja_abierta) {
        echo "
        <div class='container mt-4'>
            <div class='alert alert-warning text-center' style='font-size:1.1rem; background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:10px;'>
                ⚠️ No hay una caja abierta actualmente.<br>
                <a href='caja.php' class='btn btn-rosa mt-3' style='background-color:#ff66b2; color:white; border:none; border-radius:8px; padding:8px 16px;'>Volver al listado</a>
            </div>
        </div>";
        exit; 
    }


    $caja_datos = $caja_abierta;
    
    $caja_id = isset($caja_datos['id']) ? $caja_datos['id'] : null;    $totales = $caja->obtenerTotalesCaja($caja_id);
    $total_efectivo = $total_transferencia = 0;
    foreach ($totales as $fila) {
        if ($fila['metodo_nombre'] == 'Efectivo') $total_efectivo = $fila['total_monto'];
        if ($fila['metodo_nombre'] == 'Transferencia') $total_transferencia = $fila['total_monto'];
    }

    $gastos = $caja->obtenerTotalesGastosCaja($caja_datos['id']);
    $total_gastos_efectivo = $total_gastos_transferencia = 0;
    foreach ($gastos as $fila) {
        if ($fila['metodo_nombre'] == 'Efectivo') $total_gastos_efectivo = $fila['gasto_monto'];
        if ($fila['metodo_nombre'] == 'Transferencia') $total_gastos_transferencia = $fila['gasto_monto'];
    }

    $ingresos = $caja->obtenerTotalesIngresosCaja($caja_datos['id']);
    $total_ingresos_efectivo = $total_ingresos_transferencia = 0;
    foreach ($ingresos as $fila) {
        if ($fila['metodo_nombre'] == 'Efectivo') $total_ingresos_efectivo = $fila['ingreso_monto'];
        if ($fila['metodo_nombre'] == 'Transferencia') $total_ingresos_transferencia = $fila['ingreso_monto'];
    }

    $final_efectivo_sistema = ($caja_datos['monto_inicial_efectivo'] + $total_efectivo + $total_ingresos_efectivo) - $total_gastos_efectivo;
    $final_transferencia_sistema = ($caja_datos['monto_inicial_transferencia'] + $total_transferencia + $total_ingresos_transferencia) - $total_gastos_transferencia;
    $total_sistema = $final_efectivo_sistema + $final_transferencia_sistema;
    
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $nombre_usuario = isset($_SESSION['nombre_usuarios']) ? $_SESSION['nombre_usuarios'] : 'Desconocido';
    ?>

    <h3 class="text-center text-rosa mb-4"><i class="bi bi-calculator"></i> Arqueo de Caja</h3>

    <form method="POST" action="../controllers/caja_controlador.php">
        <input type="hidden" name="action" value="cerrar">
        <input type="hidden" name="id" value="<?= $caja_datos['id']; ?>">
        <input type="hidden" name="usuario_cierre_id" value="<?= $usuario_id; ?>">

        <div class="cake-info mb-3">
            <p><strong>Hora de Apertura:</strong> <?= $caja_datos['fecha_apertura'] . ' ' . $caja_datos['hora_apertura']; ?></p>
            <p><strong>Creada por:</strong> <?= $nombre_usuario; ?></p>
            <p><strong>Estado:</strong> <span class="badge bg-success"><?= $caja_datos['estado']; ?></span></p>
        </div>

        <div class="row g-3">
            <p>Monto Inicial Efectivo: <?= number_format($caja_datos['monto_inicial_efectivo'], 2); ?></p>
            <p>Monto Inicial Transferencia: <?= number_format($caja_datos['monto_inicial_transferencia'], 2); ?></p>

            <div class="col-md-4 cake-box">
                </div>
            </div>
        </form>
</div>

</body>
</html>