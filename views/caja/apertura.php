<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

require_once("../../config/conexion.php");
require_once("../../models/caja/caja.php");

$caja = new Caja();
$caja_abierta = $caja->getCajaAbierta();

$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario_id = $_SESSION['usuario_id'];
$fecha_actual = date('d/m/Y H:i:s');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apertura de Caja | Cake Party</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <?php
    include("../../includes/header.php");
    include("../../includes/navegacion.php");
    include("../../includes/alerts.php");
    ?>

    <div class="container py-5">
        <div class="card shadow-lg p-4 rounded-4">
            <h3 class="text-center text-pink mb-4">Apertura de Caja</h3>

            <form id="form_caja" method="POST" action="../../controllers/caja/caja_controlador.php">
                <input type="hidden" name="action" value="abrir">

                <div class="mb-3">
                    <label class="form-label">Usuario:</label>
                    <span style="font-weight:bold; color:#d63384;">
                        <?= htmlspecialchars($usuario_nombre) ?>
                    </span>
                    <input type="hidden" name="RELA_usuario" value="<?= $usuario_id ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha y Hora de Apertura:</label>
                    <span style="font-weight:bold; color:#2c3e50;">
                        <?= $fecha_actual ?>
                    </span>
                    <input type="hidden" name="fecha_apertura" value="<?= date('Y-m-d H:i:s') ?>">
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="monto_inicial_efectivo" class="form-label">Monto Inicial - Efectivo</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto_inicial_efectivo" name="monto_inicial_efectivo" required value="<?= htmlspecialchars($_POST['monto_inicial_efectivo'] ?? '0.00') ?>">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="monto_inicial_transferencia" class="form-label">Monto Inicial - Transferencia</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto_inicial_transferencia" name="monto_inicial_transferencia" required value="<?= htmlspecialchars($_POST['monto_inicial_transferencia'] ?? '0.00') ?>">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas sobre la apertura de la caja..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-pink px-4 py-2">🧾 Abrir Caja</button>
                    <a href="listado_caja.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
  
</body>

</html>