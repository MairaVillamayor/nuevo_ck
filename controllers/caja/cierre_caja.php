<?php
/**
 * Controlador para cierre de caja
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/CajaController.php';

session_start();

// Verificar permisos (empleado, admin o gerente)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 2, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$cajaController = new CajaController();
$mensaje = '';
$tipo_mensaje = '';

// Procesar cierre de caja
if ($_POST && isset($_POST['caja_id'])) {
    $caja_id = intval($_POST['caja_id']);
    
    $resultado = $cajaController->cerrarCaja($caja_id, $_SESSION['usuario_id']);
    
    if ($resultado['success']) {
        header('Location: ../../views/caja/dashboard_caja.php?success=1&mensaje=' . urlencode($resultado['mensaje']));
        exit;
    } else {
        $mensaje = $resultado['error'];
        $tipo_mensaje = 'error';
    }
}

// Obtener caja abierta del usuario
$caja_abierta = $cajaController->obtenerCajaAbierta($_SESSION['usuario_id']);

if (!$caja_abierta) {
    header('Location: ../views/caja/dashboard_caja.php?error=1&mensaje=' . urlencode('No tienes ninguna caja abierta'));
    exit;
}

// Obtener arqueo de la caja
$arqueo = $cajaController->obtenerArqueoCaja($caja_abierta['ID_caja']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-title {
            color: #e91e63;
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .resumen-caja {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #e91e63;
        }

        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .resumen-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .resumen-item h4 {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .resumen-item .valor {
            font-size: 24px;
            font-weight: bold;
            color: #e91e63;
        }

        .resumen-item.ingresos .valor {
            color: #4caf50;
        }

        .resumen-item.egresos .valor {
            color: #f44336;
        }

        .movimientos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .movimientos-table th,
        .movimientos-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .movimientos-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .movimientos-table tbody tr:hover {
            background: #fff0f5;
        }

        .badge-tipo {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-ingreso {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .badge-egreso {
            background: #ffebee;
            color: #c62828;
        }

        .btn-submit {
            width: 100%;
            background: #f44336;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #e91e63;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .saldo-final {
            background: linear-gradient(135deg, #e91e63, #f06292);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .saldo-final h3 {
            margin-bottom: 10px;
        }

        .saldo-final .valor {
            font-size: 32px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div style="background: #ffe6ef; min-height: 100vh; padding: 20px;">
        <a href="../../views/caja/dashboard_caja.php" class="back-link">← Volver al Dashboard de Caja</a>
        
        <div class="form-container">
            <h1 class="form-title">Cierre de Caja</h1>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if ($arqueo['success']): ?>
                <div class="resumen-caja">
                    <h3>📊 Resumen de la Caja</h3>
                    <p><strong>Fecha de apertura:</strong> <?= date('d/m/Y H:i', strtotime($caja_abierta['caja_fecha_apertura'])) ?></p>
                    
                    <div class="resumen-grid">
                        <div class="resumen-item">
                            <h4>Monto Inicial</h4>
                            <div class="valor">$<?= number_format($caja_abierta['caja_monto_inicial'], 2) ?></div>
                        </div>
                        <div class="resumen-item ingresos">
                            <h4>Total Ingresos</h4>
                            <div class="valor">$<?= number_format($arqueo['totales']['total_ingresos'], 2) ?></div>
                        </div>
                        <div class="resumen-item egresos">
                            <h4>Total Egresos</h4>
                            <div class="valor">$<?= number_format($arqueo['totales']['total_egresos'], 2) ?></div>
                        </div>
                    </div>

                    <div class="saldo-final">
                        <h3>💰 Saldo Final</h3>
                        <div class="valor">$<?= number_format($arqueo['saldo_actual'], 2) ?></div>
                    </div>
                </div>

                <?php if (!empty($arqueo['movimientos'])): ?>
                    <h3>📋 Movimientos Registrados</h3>
                    <table class="movimientos-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Descripción</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arqueo['movimientos'] as $movimiento): ?>
                                <tr>
                                    <td><?= $movimiento['fecha_formateada'] ?></td>
                                    <td>
                                        <span class="badge-tipo badge-<?= $movimiento['movimiento_tipo'] ?>">
                                            <?= ucfirst($movimiento['movimiento_tipo']) ?>
                                        </span>
                                    </td>
                                    <td><strong>$<?= number_format($movimiento['movimiento_monto'], 2) ?></strong></td>
                                    <td><?= htmlspecialchars($movimiento['movimiento_descripcion']) ?></td>
                                    <td><?= htmlspecialchars($movimiento['persona_nombre'] . ' ' . $movimiento['persona_apellido']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cerrar la caja? Esta acción no se puede deshacer.')">
                    <input type="hidden" name="caja_id" value="<?= $caja_abierta['ID_caja'] ?>">
                    <button type="submit" class="btn-submit">
                        🔒 Cerrar Caja
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
