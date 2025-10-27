<?php
/**
 * Controlador para arqueo de caja
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

// Obtener caja abierta del usuario
$caja_abierta = $cajaController->obtenerCajaAbierta($_SESSION['usuario_id']);

if (!$caja_abierta) {
    header('Location: ../../views/caja/dashboard_caja.php?error=1&mensaje=' . urlencode('No tienes ninguna caja abierta'));
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
    <title>Arqueo de Caja - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        .arqueo-container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .arqueo-title {
            color: #e91e63;
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .resumen-principal {
            background: linear-gradient(135deg, #e91e63, #f06292);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .resumen-principal h2 {
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-left: 5px solid;
        }

        .stat-card.inicial {
            border-left-color: #2196f3;
        }

        .stat-card.ingresos {
            border-left-color: #4caf50;
        }

        .stat-card.egresos {
            border-left-color: #f44336;
        }

        .stat-card.saldo {
            border-left-color: #e91e63;
        }

        .stat-card h4 {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .stat-card .valor {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .stat-card.inicial .valor {
            color: #2196f3;
        }

        .stat-card.ingresos .valor {
            color: #4caf50;
        }

        .stat-card.egresos .valor {
            color: #f44336;
        }

        .stat-card.saldo .valor {
            color: #e91e63;
        }

        .movimientos-section {
            margin-top: 30px;
        }

        .movimientos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .movimientos-table th,
        .movimientos-table td {
            padding: 15px;
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
            padding: 6px 12px;
            border-radius: 15px;
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

        .info-caja {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #e91e63;
        }

        .info-caja h3 {
            color: #e91e63;
            margin-bottom: 10px;
        }

        .no-movimientos {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .btn-imprimir {
            background: #2196f3;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-imprimir:hover {
            background: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }
            
            .movimientos-table {
                font-size: 14px;
            }
            
            .movimientos-table th,
            .movimientos-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div style="background: #ffe6ef; min-height: 100vh; padding: 20px;">
        <a href="../../views/caja/dashboard_caja.php" class="back-link">← Volver al Dashboard de Caja</a>
        
        <div class="arqueo-container">
            <h1 class="arqueo-title">Arqueo de Caja</h1>
            
            <?php if ($arqueo['success']): ?>
                <div class="info-caja">
                    <h3>Información de la Caja</h3>
                    <p><strong>Fecha de apertura:</strong> <?= date('d/m/Y H:i', strtotime($caja_abierta['caja_fecha_apertura'])) ?></p>
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($arqueo['caja']['persona_nombre'] . ' ' . $arqueo['caja']['persona_apellido']) ?></p>
                    <p><strong>Estado:</strong> <span style="color: #4caf50; font-weight: bold;">Abierta</span></p>
                </div>

                <div class="resumen-principal">
                    <h2>💰 Saldo Actual de la Caja</h2>
                    <div style="font-size: 3em; font-weight: bold; margin-top: 10px;">
                        $<?= number_format($arqueo['saldo_actual'], 2) ?>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card inicial">
                        <h4>Monto Inicial</h4>
                        <div class="valor">$<?= number_format($caja_abierta['caja_monto_inicial'], 2) ?></div>
                    </div>
                    <div class="stat-card ingresos">
                        <h4>Total Ingresos</h4>
                        <div class="valor">$<?= number_format($arqueo['totales']['total_ingresos'], 2) ?></div>
                    </div>
                    <div class="stat-card egresos">
                        <h4>Total Egresos</h4>
                        <div class="valor">$<?= number_format($arqueo['totales']['total_egresos'], 2) ?></div>
                    </div>
                    <div class="stat-card saldo">
                        <h4>Saldo Actual</h4>
                        <div class="valor">$<?= number_format($arqueo['saldo_actual'], 2) ?></div>
                    </div>
                </div>

                <div class="movimientos-section">
                    <button onclick="window.print()" class="btn-imprimir">🖨️ Imprimir Arqueo</button>
                    
                    <h3>📋 Historial de Movimientos</h3>
                    
                    <?php if (!empty($arqueo['movimientos'])): ?>
                        <table class="movimientos-table">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
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
                    <?php else: ?>
                        <div class="no-movimientos">
                            <h4>📝 No hay movimientos registrados</h4>
                            <p>Solo se registra el monto inicial de apertura.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-refresh cada 30 segundos para mantener datos actualizados
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
