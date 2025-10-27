<?php
/**
 * Dashboard de Caja
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../controllers/caja/CajaController.php';

session_start();

// Verificar permisos (empleado, admin o gerente)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 2, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$cajaController = new CajaController();

// Obtener datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$perfil_id = $_SESSION['perfil_id'];

// Obtener caja abierta del usuario
$caja_abierta = $cajaController->obtenerCajaAbierta($usuario_id);

// Obtener historial de cajas del usuario
$historial_cajas = $cajaController->obtenerCajasUsuario($usuario_id, 10);

// Si es admin o gerente, obtener todas las cajas
$todas_las_cajas = [];
if (in_array($perfil_id, [1, 4])) {
    $todas_las_cajas = $cajaController->obtenerTodasLasCajas(20);
}

// Obtener estadísticas del día
$estadisticas_hoy = $cajaController->obtenerEstadisticasCaja(date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Caja - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 30px 20px;
            background: linear-gradient(135deg, #e91e63, #f06292);
            color: white;
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.5em;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            display: block;
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #fff0f5;
            border-left-color: #e91e63;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: #fff0f5;
            border-left-color: #e91e63;
            color: #e91e63;
        }

        .nav-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .content-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .content-title {
            color: #e91e63;
            font-size: 2.2em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .content-title i {
            margin-right: 15px;
        }

        .content-subtitle {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .stat-card.abierta {
            border-left-color: #4caf50;
        }

        .stat-card.cerrada {
            border-left-color: #f44336;
        }

        .stat-card.ingresos {
            border-left-color: #2196f3;
        }

        .stat-card.egresos {
            border-left-color: #ff9800;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #e91e63, #f06292);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1.1em;
            color: #666;
            font-weight: 500;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }

        .action-card.abrir {
            border-top: 5px solid #4caf50;
        }

        .action-card.cerrar {
            border-top: 5px solid #f44336;
        }

        .action-card.egreso {
            border-top: 5px solid #ff9800;
        }

        .action-card.arqueo {
            border-top: 5px solid #2196f3;
        }

        .action-card.historial {
            border-top: 5px solid #9c27b0;
        }

        .action-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .action-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .action-description {
            color: #666;
            font-size: 0.9em;
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

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
        }

        .status-abierta {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .status-cerrada {
            background-color: #ffebee;
            color: #c62828;
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

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .mobile-menu-btn {
            display: none;
            background: #e91e63;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .mobile-menu-btn {
                display: block;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .actions-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Caja</h2>
                <p>Gestión de Caja</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard_caja.php" class="nav-item active">
                    Dashboard
                </a>
                <?php if (!$caja_abierta): ?>
                    <a href="../../controllers/caja/apertura_caja.php" class="nav-item">
                        Abrir Caja
                    </a>
                <?php else: ?>
                    <a href="../../controllers/caja/cierre_caja.php" class="nav-item">
                        Cerrar Caja
                    </a>
                    <a href="../../controllers/caja/registrar_egreso.php" class="nav-item">
                        Registrar Egreso
                    </a>
                    <a href="../../controllers/caja/arqueo_caja.php" class="nav-item">
                        Arqueo Actual
                    </a>
                <?php endif; ?>
                <a href="historial_cajas.php" class="nav-item">
                    Historial
                </a>
                <?php if (in_array($perfil_id, [1, 4])): ?>
                    <a href="todas_las_cajas.php" class="nav-item">
                        Todas las Cajas
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content" id="mainContent">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">Menú</button>
            
            <div class="content-header">
                <h1 class="content-title">
                    Dashboard de Caja
                </h1>
                <p class="content-subtitle">Gestión completa del sistema de caja</p>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_GET['mensaje'] ?? 'Operación realizada exitosamente') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($_GET['mensaje'] ?? 'Error en la operación') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Estadísticas principales -->
            <div class="stats-grid">
                <div class="stat-card <?= $caja_abierta ? 'abierta' : 'cerrada' ?>">
                    <div class="stat-number"><?= $caja_abierta ? '1' : '0' ?></div>
                    <div class="stat-label">Cajas Abiertas</div>
                </div>
                
                <div class="stat-card ingresos">
                    <div class="stat-number">$<?= number_format($estadisticas_hoy['total_ingresos_general'] ?? 0, 0) ?></div>
                    <div class="stat-label">Ingresos Hoy</div>
                </div>
                
                <div class="stat-card egresos">
                    <div class="stat-number">$<?= number_format($estadisticas_hoy['total_egresos_general'] ?? 0, 0) ?></div>
                    <div class="stat-label">Egresos Hoy</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?= count($historial_cajas) ?></div>
                    <div class="stat-label">Cajas del Usuario</div>
                </div>
            </div>

            <!-- Acciones principales -->
            <div class="actions-grid">
                <?php if (!$caja_abierta): ?>
                    <a href="../../controllers/caja/apertura_caja.php" class="action-card abrir">
                        <div class="action-title">Abrir Caja</div>
                        <div class="action-description">Iniciar nueva jornada de trabajo</div>
                    </a>
                <?php else: ?>
                    <a href="../../controllers/caja/cierre_caja.php" class="action-card cerrar">
                        <div class="action-title">Cerrar Caja</div>
                        <div class="action-description">Finalizar jornada de trabajo</div>
                    </a>
                    
                    <a href="../../controllers/caja/registrar_egreso.php" class="action-card egreso">
                        <div class="action-title">Registrar Egreso</div>
                        <div class="action-description">Registrar gastos menores</div>
                    </a>
                    
                    <a href="../../controllers/caja/arqueo_caja.php" class="action-card arqueo">
                        <div class="action-title">Ver Arqueo</div>
                        <div class="action-description">Consultar estado actual</div>
                    </a>
                <?php endif; ?>
                
                <a href="historial_cajas.php" class="action-card historial">
                    <div class="action-title">Historial</div>
                    <div class="action-description">Ver cajas anteriores</div>
                </a>
            </div>

            <!-- Estado de caja actual -->
            <?php if ($caja_abierta): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">📊 Estado de Caja Actual</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha Apertura</th>
                                <th>Monto Inicial</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($caja_abierta['caja_fecha_apertura'])) ?></td>
                                <td><strong>$<?= number_format($caja_abierta['caja_monto_inicial'], 2) ?></strong></td>
                                <td>
                                    <span class="status-badge status-abierta">Abierta</span>
                                </td>
                                <td>
                                    <a href="../../controllers/caja/arqueo_caja.php" class="btn btn-small btn-info">Ver Arqueo</a>
                                    <a href="../../controllers/caja/cierre_caja.php" class="btn btn-small btn-danger">Cerrar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Historial reciente -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">📋 Historial Reciente de Cajas</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha Apertura</th>
                            <th>Fecha Cierre</th>
                            <th>Monto Inicial</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historial_cajas)): ?>
                            <tr>
                                <td colspan="5" class="no-data">No hay cajas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historial_cajas as $caja): ?>
                                <tr>
                                    <td><?= $caja['fecha_apertura_formateada'] ?></td>
                                    <td><?= $caja['fecha_cierre_formateada'] ?: 'Sin cerrar' ?></td>
                                    <td><strong>$<?= number_format($caja['caja_monto_inicial'], 2) ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?= $caja['caja_estado'] ?>">
                                            <?= ucfirst($caja['caja_estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($caja['caja_estado'] === 'abierta'): ?>
                                            <a href="../../controllers/caja/arqueo_caja.php" class="btn btn-small btn-info">Arqueo</a>
                                        <?php else: ?>
                                            <a href="detalle_caja.php?id=<?= $caja['ID_caja'] ?>" class="btn btn-small btn-primary">Ver Detalle</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                mainContent.classList.remove('expanded');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        });

        // Auto-refresh cada 60 segundos
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
