<?php
/**
 * Controlador para ver historial de un insumo específico
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/StockController.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$stockController = new StockController();

// Obtener ID del insumo
$insumo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$insumo_id) {
    header('Location: alertas_stock.php?error=insumo_required');
    exit;
}

// Obtener información del insumo
$insumo_info = $stockController->obtenerEstadoStock($insumo_id);

if (!$insumo_info) {
    header('Location: alertas_stock.php?error=insumo_not_found');
    exit;
}

// Obtener historial de movimientos
$historial = $stockController->obtenerHistorialInsumo($insumo_id, 100);

// Calcular estadísticas del historial
$estadisticas = [
    'total_movimientos' => count($historial),
    'total_ingresos' => 0,
    'total_egresos' => 0,
    'ultimo_movimiento' => null
];

foreach ($historial as $movimiento) {
    if ($movimiento['RELA_tipo_de_operacion'] == 1) { // Ingreso
        $estadisticas['total_ingresos'] += $movimiento['operacion_cantidad_de_productos'];
    } else { // Egreso
        $estadisticas['total_egresos'] += $movimiento['operacion_cantidad_de_productos'];
    }
}

if (!empty($historial)) {
    $estadisticas['ultimo_movimiento'] = $historial[0]['fecha_formateada'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Stock - <?= htmlspecialchars($insumo_info['insumo_nombre'] ?? 'Insumo') ?> - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #fff0f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        h1 {
            color: #e91e63;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #e91e63;
            padding-bottom: 15px;
        }

        .insumo-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #e91e63;
        }

        .insumo-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
        }

        .insumo-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .detail-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1.1em;
            font-weight: bold;
            color: #333;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid;
        }

        .stat-card.ingresos { border-left-color: #4caf50; }
        .stat-card.egresos { border-left-color: #f44336; }
        .stat-card.total { border-left-color: #2196f3; }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .historial-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .operation-type {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .operation-ingreso {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .operation-egreso {
            background-color: #ffebee;
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

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .actions {
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .btn-success { background-color: #4caf50; color: white; }
        .btn-info { background-color: #2196f3; color: white; }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <?php include '../../includes/navegacion.php'; ?>
    
    <div class="container">
        <a href="alertas_stock.php" class="back-link">← Volver a Alertas</a>
        
        <div class="header">
            <h1>📊 Historial de Stock</h1>
            
            <div class="insumo-info">
                <div class="insumo-title"><?= htmlspecialchars($insumo_info['insumo_nombre'] ?? 'Insumo') ?></div>
                <div class="insumo-details">
                    <div class="detail-item">
                        <div class="detail-label">Stock Actual</div>
                        <div class="detail-value">
                            <?= number_format($insumo_info['insumo_stock_actual'], 2) ?> 
                            <?= htmlspecialchars($insumo_info['insumo_unidad_medida'] ?? '') ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Stock Mínimo</div>
                        <div class="detail-value">
                            <?= number_format($insumo_info['insumo_stock_minimo'], 2) ?> 
                            <?= htmlspecialchars($insumo_info['insumo_unidad_medida'] ?? '') ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Estado</div>
                        <div class="detail-value" style="color: <?= $insumo_info['estado_stock'] === 'Sin stock' ? '#f44336' : ($insumo_info['estado_stock'] === 'Bajo stock' ? '#ff9800' : '#4caf50') ?>">
                            <?= htmlspecialchars($insumo_info['estado_stock']) ?>
                        </div>
                    </div>
                    <?php if ($estadisticas['ultimo_movimiento']): ?>
                    <div class="detail-item">
                        <div class="detail-label">Último Movimiento</div>
                        <div class="detail-value"><?= htmlspecialchars($estadisticas['ultimo_movimiento']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="actions">
                <a href="ingreso_stock.php?insumo=<?= $insumo_id ?>" class="btn btn-success">
                    ➕ Ingresar Stock
                </a>
                <a href="../admin/admin_dashboard.php" class="btn btn-info">
                    📋 Ver Todos los Insumos
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-number"><?= $estadisticas['total_movimientos'] ?></div>
                    <div class="stat-label">Total Movimientos</div>
                </div>
                <div class="stat-card ingresos">
                    <div class="stat-number"><?= number_format($estadisticas['total_ingresos'], 2) ?></div>
                    <div class="stat-label">Total Ingresos</div>
                </div>
                <div class="stat-card egresos">
                    <div class="stat-number"><?= number_format($estadisticas['total_egresos'], 2) ?></div>
                    <div class="stat-label">Total Egresos</div>
                </div>
            </div>
        </div>

        <div class="historial-table">
            <h3 style="padding: 20px 20px 0 20px; margin: 0; color: #e91e63;">
                📋 Últimos Movimientos
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historial)): ?>
                        <tr>
                            <td colspan="4" class="no-data">No hay movimientos registrados para este insumo</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historial as $movimiento): ?>
                            <tr>
                                <td><?= htmlspecialchars($movimiento['fecha_formateada']) ?></td>
                                <td>
                                    <span class="operation-type operation-<?= $movimiento['RELA_tipo_de_operacion'] == 1 ? 'ingreso' : 'egreso' ?>">
                                        <?= htmlspecialchars($movimiento['tipo_de_operacion_descripcion']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $movimiento['signo_operacion'] ?><?= number_format($movimiento['operacion_cantidad_de_productos'], 2) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($insumo_info['insumo_unidad_medida'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
