<?php
/**
 * Dashboard de Gestión de Stock con Sidebar
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../controllers/stock/StockController.php';
include '../../includes/navegacion.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$stockController = new StockController();

// Obtener todos los insumos con estado de stock
$insumos = $stockController->obtenerTodosInsumosConEstado();

// Obtener insumos con stock bajo
$insumos_stock_bajo = $stockController->obtenerInsumosStockBajo();

// Contar por estado
$contadores = [
    'normal' => 0,
    'bajo' => 0,
    'sin_stock' => 0,
    'total' => count($insumos)
];

foreach ($insumos as $insumo) {
    if ($insumo['estado_stock'] === 'Stock normal') {
        $contadores['normal']++;
    } elseif ($insumo['estado_stock'] === 'Bajo stock') {
        $contadores['bajo']++;
    } else {
        $contadores['sin_stock']++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Stock - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #ffe6ef;
            color: #333;
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
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

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .toggle-sidebar {
            background: #e91e63;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 14px;
            transition: background 0.3s;
        }

        .toggle-sidebar:hover {
            background: #d81b60;
        }

        /* MAIN CONTENT */
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

        /* CONTENT SECTIONS */
        .content-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* STATS GRID */
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
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.normal { border-left-color: #4caf50; }
        .stat-card.bajo { border-left-color: #ff9800; }
        .stat-card.sin-stock { border-left-color: #f44336; }
        .stat-card.total { border-left-color: #2196f3; }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1em;
            color: #666;
        }

        /* TABLES */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
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

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-normal { background-color: #e8f5e8; color: #2e7d32; }
        .status-bajo { background-color: #fff3e0; color: #ef6c00; }
        .status-sin-stock { background-color: #ffebee; color: #c62828; }

        /* ALERTS */
        .alerts-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .alert-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }

        .alert-item:hover {
            background: #f8f9fa;
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
        }

        /* BUTTONS */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary { background-color: #e91e63; color: white; }
        .btn-success { background-color: #4caf50; color: white; }
        .btn-warning { background-color: #ff9800; color: white; }
        .btn-info { background-color: #2196f3; color: white; }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-header {
                padding: 20px;
            }

            .content-title {
                font-size: 1.8em;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .mobile-menu-btn {
                display: block;
                background: #e91e63;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 8px;
                margin-bottom: 20px;
                cursor: pointer;
            }
        }

        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
        }

        .mobile-menu-btn {
            display: none;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            margin: 20px;
            border-radius: 10px;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>📦 Stock</h2>
                <p>Gestión de Inventario</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-section="dashboard">
                    <i>📊</i> Dashboard
                </a>
                <a href="#" class="nav-item" data-section="ingreso">
                    <i>➕</i> Ingreso de Stock
                </a>
                <a href="#" class="nav-item" data-section="movimientos">
                    <i>📈</i> Ver Movimientos
                </a>
                <a href="#" class="nav-item" data-section="alertas">
                    <i>⚠️</i> Alertas de Stock
                </a>
                <a href="#" class="nav-item" data-section="insumos">
                    <i>🔧</i> Gestionar Insumos
                </a>
            </nav>

            <div class="sidebar-footer">
                <button class="toggle-sidebar" onclick="toggleSidebar()">
                    📱 Menú
                </button>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content" id="mainContent">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">☰ Menú</button>
            
            <a href="../admin/admin_dashboard.php" class="back-link">← Volver al Dashboard</a>

            <!-- DASHBOARD SECTION -->
            <div id="dashboard" class="content-section active">
                <div class="content-header">
                    <h1 class="content-title">
                        <i>📊</i> Dashboard de Stock
                    </h1>
                    <p class="content-subtitle">Resumen general del estado de inventario</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card normal">
                        <div class="stat-number"><?= $contadores['normal'] ?></div>
                        <div class="stat-label">Stock Normal</div>
                    </div>
                    <div class="stat-card bajo">
                        <div class="stat-number"><?= $contadores['bajo'] ?></div>
                        <div class="stat-label">Stock Bajo</div>
                    </div>
                    <div class="stat-card sin-stock">
                        <div class="stat-number"><?= $contadores['sin_stock'] ?></div>
                        <div class="stat-label">Sin Stock</div>
                    </div>
                    <div class="stat-card total">
                        <div class="stat-number"><?= $contadores['total'] ?></div>
                        <div class="stat-label">Total Insumos</div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">📋 Listado de Insumos</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Estado</th>
                                <th>Proveedor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($insumos)): ?>
                                <tr>
                                    <td colspan="7" class="no-data">No hay insumos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($insumos as $insumo): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($insumo['insumo_nombre']) ?></strong><br>
                                            <small style="color: #666;"><?= htmlspecialchars($insumo['insumo_unidad_medida']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($insumo['categoria_insumo_nombre']) ?></td>
                                        <td>
                                            <strong><?= number_format($insumo['insumo_stock_actual'], 2) ?></strong>
                                            <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?>
                                        </td>
                                        <td>
                                            <?= number_format($insumo['insumo_stock_minimo'], 2) ?>
                                            <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $insumo['estado_stock'])) ?>">
                                                <?= $insumo['estado_stock'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($insumo['proveedor_nombre']) ?></td>
                                        <td>
                                            <a href="../../controllers/stock/historial_insumo.php?id=<?= $insumo['ID_insumo'] ?>" 
                                               class="btn btn-small btn-info" title="Ver historial">
                                                📊
                                            </a>
                                            <a href="../../controllers/stock/ingreso_stock.php?insumo=<?= $insumo['ID_insumo'] ?>" 
                                               class="btn btn-small btn-success" title="Ingresar stock">
                                                ➕
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- INGRESO SECTION -->
            <div id="ingreso" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">
                        <i>➕</i> Ingreso de Stock
                    </h1>
                    <p class="content-subtitle">Registrar nuevos ingresos de inventario</p>
                </div>
                
                <div style="text-align: center; padding: 50px;">
                    <a href="../../controllers/stock/ingreso_stock.php" class="btn btn-success btn-lg" style="font-size: 18px; padding: 15px 30px;">
                        ➕ Registrar Ingreso de Stock
                    </a>
                </div>
            </div>

            <!-- MOVIMIENTOS SECTION -->
            <div id="movimientos" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">
                        <i>📈</i> Movimientos de Stock
                    </h1>
                    <p class="content-subtitle">Historial de ingresos y egresos</p>
                </div>
                
                <div style="text-align: center; padding: 50px;">
                    <a href="../../controllers/stock/movimientos_stock.php" class="btn btn-info btn-lg" style="font-size: 18px; padding: 15px 30px;">
                        📊 Ver Movimientos Detallados
                    </a>
                </div>
            </div>

            <!-- ALERTAS SECTION -->
            <div id="alertas" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">
                        <i>⚠️</i> Alertas de Stock
                    </h1>
                    <p class="content-subtitle">Insumos con stock bajo o sin stock</p>
                </div>

                <?php if (empty($insumos_stock_bajo)): ?>
                    <div class="no-data">
                        <h3>🎉 ¡Excelente!</h3>
                        <p>No hay alertas de stock. Todos los insumos tienen stock suficiente.</p>
                    </div>
                <?php else: ?>
                    <div class="alerts-container">
                        <div class="table-header">
                            <h3 class="table-title">🚨 Alertas Activas (<?= count($insumos_stock_bajo) ?>)</h3>
                        </div>
                        <?php foreach ($insumos_stock_bajo as $insumo): ?>
                            <div class="alert-item">
                                <div class="alert-info">
                                    <div class="alert-title">
                                        <?= htmlspecialchars($insumo['insumo_nombre']) ?>
                                    </div>
                                    <div class="alert-details">
                                        <strong>Stock actual:</strong> <?= number_format($insumo['insumo_stock_actual'], 2) ?> <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?><br>
                                        <strong>Stock mínimo:</strong> <?= number_format($insumo['insumo_stock_minimo'], 2) ?> <?= htmlspecialchars($insumo['insumo_unidad_medida']) ?><br>
                                        <strong>Proveedor:</strong> <?= htmlspecialchars($insumo['proveedor_nombre']) ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="status-badge status-<?= $insumo['estado_stock'] === 'Sin stock' ? 'sin-stock' : 'bajo' ?>">
                                        <?= $insumo['estado_stock'] ?>
                                    </span>
                                    <br><br>
                                    <a href="../../controllers/stock/ingreso_stock.php?insumo=<?= $insumo['ID_insumo'] ?>" class="btn btn-success btn-small">
                                        ➕ Ingresar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- INSUMOS SECTION -->
            <div id="insumos" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">
                        <i>🔧</i> Gestionar Insumos
                    </h1>
                    <p class="content-subtitle">Administrar catálogo de insumos</p>
                </div>
                
                <div style="text-align: center; padding: 50px;">
                    <a href="listado_insumos.php" class="btn btn-primary btn-lg" style="font-size: 18px; padding: 15px 30px; margin: 10px;">
                        📋 Ver Listado de Insumos
                    </a>
                    <br>
                    <a href="form_alta_insumo.php" class="btn btn-success btn-lg" style="font-size: 18px; padding: 15px 30px; margin: 10px;">
                        ➕ Agregar Nuevo Insumo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const sections = document.querySelectorAll('.content-section');

            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all nav items
                    navItems.forEach(nav => nav.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Hide all sections
                    sections.forEach(section => section.classList.remove('active'));
                    
                    // Show selected section
                    const targetSection = this.getAttribute('data-section');
                    document.getElementById(targetSection).classList.add('active');
                    
                    // Close sidebar on mobile after selection
                    if (window.innerWidth <= 768) {
                        document.getElementById('sidebar').classList.remove('open');
                    }
                });
            });
        });

        // Sidebar toggle functionality
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

        // Auto-refresh alerts every 30 seconds
        setInterval(function() {
            // Only refresh if we're on the alerts section
            if (document.getElementById('alertas').classList.contains('active')) {
                // You could implement AJAX refresh here if needed
                console.log('Refreshing alerts...');
            }
        }, 30000);
    </script>
    
    <script src="../../public/js/stock_dashboard.js"></script>
</body>
</html>
