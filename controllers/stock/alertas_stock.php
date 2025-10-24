<?php
/**
 * Controlador para alertas de stock
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

// Obtener insumos con stock bajo
$insumos_stock_bajo = $stockController->obtenerInsumosStockBajo();

// Separar por tipo de alerta
$sin_stock = [];
$bajo_stock = [];

foreach ($insumos_stock_bajo as $insumo) {
    if ($insumo['insumo_stock_actual'] == 0) {
        $sin_stock[] = $insumo;
    } else {
        $bajo_stock[] = $insumo;
    }
}

// Estadísticas
$estadisticas = [
    'sin_stock' => count($sin_stock),
    'bajo_stock' => count($bajo_stock),
    'total_alertas' => count($insumos_stock_bajo)
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas de Stock - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #fff0f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
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

        .stat-card.sin-stock { border-left-color: #f44336; }
        .stat-card.bajo-stock { border-left-color: #ff9800; }
        .stat-card.total { border-left-color: #2196f3; }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1em;
        }

        .alerts-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            padding: 20px;
            margin: 0;
            font-size: 1.3em;
            font-weight: bold;
            border-bottom: 2px solid #eee;
        }

        .section-header.sin-stock {
            background-color: #ffebee;
            color: #c62828;
        }

        .section-header.bajo-stock {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .alert-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .alert-item:hover {
            background-color: #f8f9fa;
        }

        .alert-item:last-child {
            border-bottom: none;
        }

        .alert-info {
            flex: 1;
        }

        .alert-title {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .alert-details {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .alert-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .status-sin-stock {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .status-bajo-stock {
            background-color: #ffe0b2;
            color: #ef6c00;
        }

        .alert-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-success { background-color: #4caf50; color: white; }
        .btn-info { background-color: #2196f3; color: white; }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
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

        .no-alerts {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            margin: 20px;
            border-radius: 10px;
        }

        .priority-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
            display: inline-block;
        }

        .priority-critical {
            background-color: #f44336;
            animation: pulse 2s infinite;
        }

        .priority-warning {
            background-color: #ff9800;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include '../../includes/navegacion.php'; ?>
    
    <div class="container">
        <a href="../admin/admin_dashboard.php" class="back-link">← Volver al Dashboard</a>
        
        <div class="header">
            <h1>⚠️ Alertas de Stock</h1>
            
            <div class="stats-grid">
                <div class="stat-card sin-stock">
                    <div class="stat-number"><?= $estadisticas['sin_stock'] ?></div>
                    <div class="stat-label">Sin Stock</div>
                </div>
                <div class="stat-card bajo-stock">
                    <div class="stat-number"><?= $estadisticas['bajo_stock'] ?></div>
                    <div class="stat-label">Stock Bajo</div>
                </div>
                <div class="stat-card total">
                    <div class="stat-number"><?= $estadisticas['total_alertas'] ?></div>
                    <div class="stat-label">Total Alertas</div>
                </div>
            </div>
        </div>

        <?php if ($estadisticas['total_alertas'] == 0): ?>
            <div class="alerts-section">
                <div class="no-alerts">
                    <h3>🎉 ¡Excelente!</h3>
                    <p>No hay alertas de stock. Todos los insumos tienen stock suficiente.</p>
                </div>
            </div>
        <?php else: ?>

            <?php if (!empty($sin_stock)): ?>
            <div class="alerts-section">
                <h3 class="section-header sin-stock">
                    🚨 Insumos Sin Stock (<?= count($sin_stock) ?>)
                </h3>
                <?php foreach ($sin_stock as $insumo): ?>
                    <div class="alert-item">
                        <div class="alert-info">
                            <div class="alert-title">
                                <span class="priority-indicator priority-critical"></span>
                                <?= htmlspecialchars($insumo['insumo_nombre']) ?>
                            </div>
                            <div class="alert-details">
                                <strong>Categoría:</strong> <?= htmlspecialchars($insumo['categoria_insumo_nombre']) ?><br>
                                <strong>Proveedor:</strong> <?= htmlspecialchars($insumo['proveedor_nombre']) ?><br>
                                <strong>Stock mínimo requerido:</strong> <?= number_format($insumo['insumo_stock_minimo'], 2) ?> <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <span class="alert-status status-sin-stock">SIN STOCK</span>
                            <a href="ingreso_stock.php?insumo=<?= $insumo['ID_insumo'] ?>" class="btn btn-success">
                                ➕ Ingresar Stock
                            </a>
                            <a href="historial_insumo.php?id=<?= $insumo['ID_insumo'] ?>" class="btn btn-info">
                                📊 Ver Historial
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($bajo_stock)): ?>
            <div class="alerts-section">
                <h3 class="section-header bajo-stock">
                    ⚠️ Insumos con Stock Bajo (<?= count($bajo_stock) ?>)
                </h3>
                <?php foreach ($bajo_stock as $insumo): ?>
                    <div class="alert-item">
                        <div class="alert-info">
                            <div class="alert-title">
                                <span class="priority-indicator priority-warning"></span>
                                <?= htmlspecialchars($insumo['insumo_nombre']) ?>
                            </div>
                            <div class="alert-details">
                                <strong>Stock actual:</strong> <?= number_format($insumo['insumo_stock_actual'], 2) ?> <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?><br>
                                <strong>Stock mínimo:</strong> <?= number_format($insumo['insumo_stock_minimo'], 2) ?> <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?><br>
                                <strong>Proveedor:</strong> <?= htmlspecialchars($insumo['proveedor_nombre']) ?>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <span class="alert-status status-bajo-stock">STOCK BAJO</span>
                            <a href="ingreso_stock.php?insumo=<?= $insumo['ID_insumo'] ?>" class="btn btn-success">
                                ➕ Ingresar Stock
                            </a>
                            <a href="historial_insumo.php?id=<?= $insumo['ID_insumo'] ?>" class="btn btn-info">
                                📊 Ver Historial
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>
