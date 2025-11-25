<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../../index.php?error=not_logged');
  exit;
}
?>

<?php
require_once("../../config/conexion.php");
require_once("../../models/caja/caja.php");

$cajaModel = new Caja();
$caja_abierta = $cajaModel->getCajaAbierta();

if (!$caja_abierta) {
  echo " <script>
  showCakeAlert(
          'Caja no encontrada',
          '⚠️ No hay ninguna caja abierta actualmente.',
          'listado_caja.php'
      );
  </script>
  ";
  exit;
}


$caja_id = $caja_abierta['ID_caja'] ?? $caja_abierta['ID_caja'];

$ingresos = $cajaModel->obtenerTotalesIngresosCaja($caja_id);
$gastos = $cajaModel->obtenerTotalesGastosCaja($caja_id);

$total_ingresos_efectivo = $total_ingresos_transferencia = 0;
$total_egresos_efectivo = $total_egresos_transferencia = 0;

foreach ($ingresos as $fila) {
    if ($fila['metodo_nombre'] === 'Efectivo') $total_ingresos_efectivo = $fila['ingreso_monto'] ?? 0;
    if ($fila['metodo_nombre'] === 'Transferencia') $total_ingresos_transferencia = $fila['ingreso_monto'] ?? 0;
}


foreach ($gastos as $fila) {
  if ($fila['metodo_nombre'] === 'Efectivo') $total_egresos_efectivo = $fila['gasto_monto'];
  if ($fila['metodo_nombre'] === 'Transferencia') $total_egresos_transferencia = $fila['gasto_monto'];
}

// Cálculo de saldos del sistema
$final_efectivo_sistema = ($caja_abierta['caja_monto_inicial_efectivo'] ?? 0) + $total_ingresos_efectivo - $total_egresos_efectivo;
$final_transferencia_sistema = ($caja_abierta['caja_monto_inicial_transferencia'] ?? 0) + $total_ingresos_transferencia - $total_egresos_transferencia;
$total_sistema = $final_efectivo_sistema + $final_transferencia_sistema;

$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Desconocido';
$usuario_id = $_SESSION['usuario_id'];
$fecha_actual = date('d/m/Y H:i:s');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arqueo de Caja | Cake Party</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
  <style>
    .text-pink {
      color: #d63384;
    }

    .btn-pink {
      background-color: #f06292;
      border: none;
      color: white;
      font-weight: bold;
    }

    .btn-pink:hover {
      background-color: #ec407a;
    }

    .card {
      border: none;
      border-radius: 1.5rem;
      background: #fff;
    }

    .input-group-text {
      background-color: #fce4ec;
      color: #d63384;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <?php include("../../includes/navegacion.php"); ?>
  <?php include("../../includes/header.php"); ?>
  <?php include("../../includes/alerts.php"); ?>
  <div class="container py-5">
    <div class="card shadow-lg p-4 rounded-4">
      <h3 class="text-center text-pink mb-4">📊 Arqueo y Cierre de Caja</h3>

      <form method="POST" action="../../controllers/caja/caja_controlador.php">
        <input type="hidden" name="action" value="cerrar">
        <input type="hidden" name="ID_caja" value="<?= $caja_id ?>">
        <input type="hidden" name="usuario_cierre" value="<?= $usuario_id ?>">

        <!-- Datos de apertura -->
        <div class="mb-4">
          <h5 class="text-secondary">📅 Información de la Caja</h5>
          <p><strong>Usuario Apertura:</strong> <?= htmlspecialchars($usuario_nombre) ?></p>
          <p><strong>Fecha Apertura:</strong> <?= htmlspecialchars($caja_abierta['fecha_apertura']) ?></p>
          <p><strong>Estado actual:</strong> <span class="badge bg-success">Abierta</span></p>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6">
            <h6 class="text-pink">💵 Efectivo (Sistema)</h6>
            <ul class="list-group mb-3">
              <li class="list-group-item">Monto Inicial: <strong>$<?= number_format($caja_abierta['caja_monto_inicial_efectivo'], 2) ?></strong></li>
              <li class="list-group-item">Ingresos: <strong class="text-success">$<?= number_format($total_ingresos_efectivo, 2) ?></strong></li>
              <li class="list-group-item">Egresos: <strong class="text-danger">$<?= number_format($total_egresos_efectivo, 2) ?></strong></li>
              <li class="list-group-item bg-light">Saldo Esperado: <strong>$<?= number_format($final_efectivo_sistema, 2) ?></strong></li>
            </ul>
          </div>

          <div class="col-md-6">
            <h6 class="text-pink">💳 Transferencias (Sistema)</h6>
            <ul class="list-group mb-3">
              <li class="list-group-item">Monto Inicial: <strong>$<?= number_format($caja_abierta['caja_monto_inicial_transferencia'], 2) ?></strong></li>
              <li class="list-group-item">Ingresos: <strong class="text-success">$<?= number_format($total_ingresos_transferencia, 2) ?></strong></li>
              <li class="list-group-item">Egresos: <strong class="text-danger">$<?= number_format($total_egresos_transferencia, 2) ?></strong></li>
              <li class="list-group-item bg-light">Saldo Esperado: <strong>$<?= number_format($final_transferencia_sistema, 2) ?></strong></li>
            </ul>
          </div>
        </div>

        <hr>
        <hr>

        <div class="row mb-4">
          <h5 class="text-secondary mb-3">⚖️ Resultado del Arqueo Real</h5>

          <div class="col-md-6">
            <label class="form-label">Diferencia Efectivo</label>
            <div class="input-group">
              <span class="input-group-text" id="diff_efectivo_sign">$</span>
              <input type="text" class="form-control" id="diff_efectivo_valor" readonly value="0.00">
              <input type="hidden" name="diferencia_efectivo" id="diferencia_efectivo_hidden">
            </div>
            <small class="text-muted" id="estado_efectivo"></small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Diferencia Transferencia</label>
            <div class="input-group">
              <span class="input-group-text" id="diff_transferencia_sign">$</span>
              <input type="text" class="form-control" id="diff_transferencia_valor" readonly value="0.00">
              <input type="hidden" name="diferencia_transferencia" id="diferencia_transferencia_hidden">
            </div>
            <small class="text-muted" id="estado_transferencia"></small>
          </div>
        </div>

        <hr>

        <div class="row">
          <h5 class="text-secondary mb-3">💰 Conteo Real en Caja</h5>
          <div class="col-md-6 mb-3">
            <label for="cierre_efectivo" class="form-label">Efectivo Contado</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control" id="cierre_efectivo" name="cierre_efectivo" required>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <label for="cierre_transferencia" class="form-label">Transferencias Recibidas</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control" id="cierre_transferencia" name="cierre_transferencia" required>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="observaciones" class="form-label">Observaciones (opcional)</label>
          <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas sobre el cierre o diferencias detectadas..."></textarea>
        </div>

        <hr>

        <div class="text-center">
          <p><strong>Fecha y hora de cierre:</strong> <?= $fecha_actual ?></p>
          <input type="hidden" name="fecha_cierre" value="<?= date('Y-m-d H:i:s') ?>">
          <button type="submit" class="btn btn-pink px-4 py-2">✅ Cerrar Caja</button>
          <a href="listado_caja.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
  <script>
    const saldoEsperadoEfectivo = <?= $final_efectivo_sistema ?>;
    const saldoEsperadoTransferencia = <?= $final_transferencia_sistema ?>;

    const inputCierreEfectivo = document.getElementById('cierre_efectivo');
    const inputCierreTransferencia = document.getElementById('cierre_transferencia');

    const outputDiffEfectivo = document.getElementById('diff_efectivo_valor');
    const outputDiffTransferencia = document.getElementById('diff_transferencia_valor');

    const hiddenDiffEfectivo = document.getElementById('diferencia_efectivo_hidden');
    const hiddenDiffTransferencia = document.getElementById('diferencia_transferencia_hidden');

    const estadoEfectivo = document.getElementById('estado_efectivo');
    const estadoTransferencia = document.getElementById('estado_transferencia');

    // ... (código JavaScript anterior)

    function calcularDiferencia(inputElement, saldoEsperado, outputValorElement, hiddenElement, estadoElement) {
        const valorContado = parseFloat(inputElement.value) || 0;
        
        const diferencia = valorContado - saldoEsperado;
        
        const valorAbsoluto = Math.abs(diferencia).toFixed(2);
        
        outputValorElement.value = valorAbsoluto;
        hiddenElement.value = diferencia.toFixed(2); // Guardamos el valor real (positivo o negativo)

        let claseTexto = '';
        let textoEstado = '';

        if (diferencia > 0.01) {
            claseTexto = 'text-success';
            // Mensaje para un Sobrante (diferencia positiva)
            textoEstado = `✅ Sobrante de **$${valorAbsoluto}**`;
        } else if (diferencia < -0.01) {
            claseTexto = 'text-danger';
            // Mensaje para un Faltante (diferencia negativa)
            textoEstado = `❌ Faltante de **$${valorAbsoluto}**`;
        } else {
            claseTexto = 'text-primary';
            textoEstado = `⭐ ¡Caja Cuadrada!`;
        }
        
        estadoElement.innerHTML = textoEstado;
        estadoElement.className = `text-muted ${claseTexto}`; 
    }

// ... (resto del código JavaScript)

    // 3. Asignar Event Listeners
    inputCierreEfectivo.addEventListener('input', () => {
      calcularDiferencia(
        inputCierreEfectivo,
        saldoEsperadoEfectivo,
        outputDiffEfectivo,
        hiddenDiffEfectivo,
        estadoEfectivo
      );
    });

    inputCierreTransferencia.addEventListener('input', () => {
      calcularDiferencia(
        inputCierreTransferencia,
        saldoEsperadoTransferencia,
        outputDiffTransferencia,
        hiddenDiffTransferencia,
        estadoTransferencia
      );
    });

    inputCierreEfectivo.dispatchEvent(new Event('input'));
    inputCierreTransferencia.dispatchEvent(new Event('input'));
  </script>

</body>

</html>