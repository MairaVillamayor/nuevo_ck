<?php
/**
 * Controlador para ver movimientos de stock
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

// Obtener parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01'); // Primer día del mes
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d'); // Hoy

// Obtener movimientos
$movimientos = $stockController->obtenerMovimientosPorFecha($fecha_inicio, $fecha_fin);

// Calcular totales
$totales = [
    'ingresos' => 0,
    'egresos' => 0,
    'total_movimientos' => count($movimientos)
];

foreach ($movimientos as $movimiento) {
    if ($movimiento['RELA_tipo_de_operacion'] == 1) { // Ingreso
        $totales['ingresos'] += $movimiento['operacion_cantidad_de_productos'];
    } else { // Egreso
        $totales['egresos'] += $movimiento['operacion_cantidad_de_productos'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de Stock - Cake Party</title>
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

        .filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="date"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
        }

        .btn-primary { background-color: #e91e63; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }

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

        .movements-table {
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
    </style>
</head>
<body>
    <?php include '../../includes/navegacion.php'; ?>
    
    <div class="container">
        <a href="../admin/admin_dashboard.php" class="back-link">← Volver al Dashboard</a>
        
        <div class="header">
            <h1>📊 Movimientos de Stock</h1>
            
            <div class="filters">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin:</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                    <a href="?fecha_inicio=<?= date('Y-m-01') ?>&fecha_fin=<?= date('Y-m-d') ?>" class="btn btn-secondary">📅 Este Mes</a>
                </form>
            </div>

            <div class="stats-grid">
                <div class="stat-card ingresos">
                    <div class="stat-number"><?= $totales['ingresos'] ?></div>
                    <div class="stat-label">Ingresos</div>
                </div>
                <div class="stat-card egresos">
                    <div class="stat-number"><?= $totales['egresos'] ?></div>
                    <div class="stat-label">Egresos</div>
                </div>
                <div class="stat-card total">
                    <div class="stat-number"><?= $totales['total_movimientos'] ?></div>
                    <div class="stat-label">Total Movimientos</div>
                </div>
            </div>
        </div>

        <div class="movements-table">
            <h3 style="padding: 20px 20px 0 20px; margin: 0; color: #e91e63;">
                📋 Movimientos del <?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Insumo</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movimientos)): ?>
                        <tr>
                            <td colspan="5" class="no-data">No hay movimientos en el período seleccionado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movimientos as $movimiento): ?>
                            <tr>
                                <td><?= htmlspecialchars($movimiento['fecha_formateada']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($movimiento['insumo_nombre']) ?></strong>
                                </td>
                                <td>
                                    <span class="operation-type operation-<?= $movimiento['RELA_tipo_de_operacion'] == 1 ? 'ingreso' : 'egreso' ?>">
                                        <?= htmlspecialchars($movimiento['tipo_de_operacion_descripcion']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= number_format($movimiento['operacion_cantidad_de_productos'], 2) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($movimiento['unidad_medida']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
