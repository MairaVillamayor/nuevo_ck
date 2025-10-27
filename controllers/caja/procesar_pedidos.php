<?php
/**
 * Controlador para procesar pedidos pendientes en caja
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/IntegracionPedidosCaja.php';

session_start();

// Verificar permisos (admin o gerente)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$integracion = new IntegracionPedidosCaja();
$mensaje = '';
$tipo_mensaje = '';

// Procesar pedidos pendientes
if ($_POST && isset($_POST['procesar_pendientes'])) {
    $resultado = $integracion->procesarPedidosPendientes();
    
    if ($resultado['success']) {
        $mensaje = "Se procesaron {$resultado['procesados']} de {$resultado['total']} pedidos";
        $tipo_mensaje = 'success';
        
        if (!empty($resultado['errores'])) {
            $mensaje .= ". Errores: " . implode(', ', $resultado['errores']);
            $tipo_mensaje = 'warning';
        }
    } else {
        $mensaje = $resultado['error'];
        $tipo_mensaje = 'error';
    }
}

// Obtener ventas del día
$ventas_hoy = $integracion->obtenerVentasDelDia();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pedidos - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .title {
            color: #e91e63;
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 30px;
            text-align: center;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin: 10px;
        }

        .btn-primary {
            background: #e91e63;
            color: white;
        }

        .btn-primary:hover {
            background: #c2185b;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 5px solid #e91e63;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #e91e63;
        }

        .stat-label {
            font-size: 1.1em;
            color: #666;
            font-weight: 500;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            padding: 25px;
            border-bottom: 2px solid #f0f0f0;
            background: #f8f9fa;
        }

        .table-title {
            color: #e91e63;
            font-size: 1.3em;
            font-weight: bold;
            margin: 0;
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

        tbody tr:hover {
            background-color: #fff0f5;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
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

        .action-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .action-section h3 {
            color: #e91e63;
            margin-bottom: 15px;
        }

        .action-section p {
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div style="background: #ffe6ef; min-height: 100vh; padding: 20px;">
        <a href="../views/caja/dashboard_caja.php" class="back-link">← Volver al Dashboard de Caja</a>
        
        <div class="container">
            <h1 class="title">🔄 Procesar Pedidos en Caja</h1>
            <p class="subtitle">Integración automática entre pedidos finalizados y sistema de caja</p>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= count($ventas_hoy['ventas'] ?? []) ?></div>
                    <div class="stat-label">Ventas Hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">$<?= number_format(array_sum(array_column($ventas_hoy['ventas'] ?? [], 'movimiento_monto')), 0) ?></div>
                    <div class="stat-label">Total Ventas Hoy</div>
                </div>
            </div>

            <!-- Acción principal -->
            <div class="action-section">
                <h3>🚀 Procesar Pedidos Pendientes</h3>
                <p>Esta acción registrará automáticamente todos los pedidos finalizados que aún no han sido procesados en el sistema de caja.</p>
                
                <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas procesar todos los pedidos pendientes?')">
                    <button type="submit" name="procesar_pendientes" class="btn btn-primary">
                        🔄 Procesar Pedidos Pendientes
                    </button>
                </form>
            </div>

            <!-- Ventas del día -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">📊 Ventas Registradas Hoy</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Usuario</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Caja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventas_hoy['ventas'])): ?>
                            <tr>
                                <td colspan="5" class="no-data">No hay ventas registradas hoy</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventas_hoy['ventas'] as $venta): ?>
                                <tr>
                                    <td><?= date('H:i', strtotime($venta['movimiento_fecha'])) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($venta['persona_nombre'] . ' ' . $venta['persona_apellido']) ?></strong>
                                        <br><small><?= htmlspecialchars($venta['usuario_nombre']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($venta['movimiento_descripcion']) ?></td>
                                    <td><strong>$<?= number_format($venta['movimiento_monto'], 2) ?></strong></td>
                                    <td>Caja #<?= $venta['RELA_caja'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Información adicional -->
            <div class="action-section">
                <h3>ℹ️ Información Importante</h3>
                <p>
                    <strong>Proceso automático:</strong> Los pedidos finalizados se registran automáticamente como ingresos en la caja del usuario que procesó el pedido.<br>
                    <strong>Requisitos:</strong> El usuario debe tener una caja abierta para que se registre el ingreso.<br>
                    <strong>Verificación:</strong> El sistema evita duplicar ingresos del mismo pedido.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
