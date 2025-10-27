<?php
/**
 * Vista para todas las cajas (Admin/Gerente)
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../controllers/caja/CajaController.php';

session_start();

// Verificar permisos (solo admin o gerente)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$cajaController = new CajaController();

// Obtener todas las cajas
$todas_las_cajas = $cajaController->obtenerTodasLasCajas(100);

// Procesar filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';
$usuario_filtro = $_GET['usuario'] ?? '';

// Aplicar filtros si existen
if ($fecha_inicio || $fecha_fin || $estado_filtro || $usuario_filtro) {
    $todas_las_cajas = array_filter($todas_las_cajas, function($caja) use ($fecha_inicio, $fecha_fin, $estado_filtro, $usuario_filtro) {
        $fecha_caja = date('Y-m-d', strtotime($caja['caja_fecha_apertura']));
        
        $match_fecha_inicio = !$fecha_inicio || $fecha_caja >= $fecha_inicio;
        $match_fecha_fin = !$fecha_fin || $fecha_caja <= $fecha_fin;
        $match_estado = !$estado_filtro || $caja['caja_estado'] === $estado_filtro;
        $match_usuario = !$usuario_filtro || 
            stripos($caja['persona_nombre'], $usuario_filtro) !== false ||
            stripos($caja['persona_apellido'], $usuario_filtro) !== false ||
            stripos($caja['usuario_nombre'], $usuario_filtro) !== false;
        
        return $match_fecha_inicio && $match_fecha_fin && $match_estado && $match_usuario;
    });
}

// Obtener estadísticas generales
$estadisticas_general = $cajaController->obtenerEstadisticasCaja($fecha_inicio ?: '2024-01-01', $fecha_fin ?: date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Cajas - Cake Party</title>
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
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .stat-card.total {
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

        .stat-number {
            font-size: 2.2em;
            font-weight: bold;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #e91e63, #f06292);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1em;
            color: #666;
            font-weight: 500;
        }

        .filters-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .filter-group input,
        .filter-group select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .btn-filter {
            background: #e91e63;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            height: fit-content;
        }

        .btn-filter:hover {
            background: #c2185b;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .btn-clear {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            height: fit-content;
        }

        .btn-clear:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            color: #e91e63;
            font-size: 1.3em;
            font-weight: bold;
            margin: 0;
        }

        .table-count {
            background: #e91e63;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
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

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #2196f3;
            color: white;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
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

            .filters-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 1000px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>💰 Caja</h2>
                <p>Gestión de Caja</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard_caja.php" class="nav-item">
                    <i>🏠</i> Dashboard
                </a>
                <a href="historial_cajas.php" class="nav-item">
                    <i>📋</i> Historial
                </a>
                <a href="todas_las_cajas.php" class="nav-item active">
                    <i>👥</i> Todas las Cajas
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content" id="mainContent">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">Menú</button>
            
            <a href="dashboard_caja.php" class="back-link">← Volver al Dashboard</a>
            
            <div class="content-header">
                <h1 class="content-title">
                    Todas las Cajas
                </h1>
                <p class="content-subtitle">Vista administrativa de todas las cajas del sistema</p>
            </div>

            <!-- Estadísticas generales -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-number"><?= $estadisticas_general['total_cajas'] ?? 0 ?></div>
                    <div class="stat-label">Total Cajas</div>
                </div>
                
                <div class="stat-card ingresos">
                    <div class="stat-number">$<?= number_format($estadisticas_general['total_ingresos_general'] ?? 0, 0) ?></div>
                    <div class="stat-label">Total Ingresos</div>
                </div>
                
                <div class="stat-card egresos">
                    <div class="stat-number">$<?= number_format($estadisticas_general['total_egresos_general'] ?? 0, 0) ?></div>
                    <div class="stat-label">Total Egresos</div>
                </div>
                
                <div class="stat-card saldo">
                    <div class="stat-number">$<?= number_format($estadisticas_general['saldo_total_general'] ?? 0, 0) ?></div>
                    <div class="stat-label">Saldo Total</div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <div class="filter-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="abierta" <?= $estado_filtro === 'abierta' ? 'selected' : '' ?>>Abierta</option>
                        <option value="cerrada" <?= $estado_filtro === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Buscar por usuario..." value="<?= htmlspecialchars($usuario_filtro) ?>">
                </div>
                
                <button class="btn-filter" onclick="aplicarFiltros()">🔍 Filtrar</button>
                <button class="btn-clear" onclick="limpiarFiltros()">🗑️ Limpiar</button>
            </div>

            <!-- Tabla de todas las cajas -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">📊 Todas las Cajas del Sistema</h3>
                    <div class="table-count"><?= count($todas_las_cajas) ?> registros</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Fecha Apertura</th>
                            <th>Fecha Cierre</th>
                            <th>Monto Inicial</th>
                            <th>Total Ingresos</th>
                            <th>Total Egresos</th>
                            <th>Saldo Final</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todas_las_cajas)): ?>
                            <tr>
                                <td colspan="10" class="no-data">No hay cajas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todas_las_cajas as $caja): ?>
                                <tr>
                                    <td><strong>#<?= $caja['ID_caja'] ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($caja['persona_nombre'] . ' ' . $caja['persona_apellido']) ?></strong>
                                        <br><small><?= htmlspecialchars($caja['usuario_nombre']) ?></small>
                                    </td>
                                    <td><?= $caja['fecha_apertura_formateada'] ?></td>
                                    <td><?= $caja['fecha_cierre_formateada'] ?: 'Sin cerrar' ?></td>
                                    <td><strong>$<?= number_format($caja['caja_monto_inicial'], 2) ?></strong></td>
                                    <td>$<?= number_format($caja['caja_total_ingresos'], 2) ?></td>
                                    <td>$<?= number_format($caja['caja_total_egresos'], 2) ?></td>
                                    <td><strong>$<?= number_format($caja['caja_saldo_final'], 2) ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?= $caja['caja_estado'] ?>">
                                            <?= ucfirst($caja['caja_estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($caja['caja_estado'] === 'abierta'): ?>
                                            <a href="../../controllers/caja/arqueo_caja.php" class="btn btn-info">Arqueo</a>
                                        <?php else: ?>
                                            <a href="detalle_caja.php?id=<?= $caja['ID_caja'] ?>" class="btn btn-primary">Ver Detalle</a>
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

        function aplicarFiltros() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            const estado = document.getElementById('estado').value;
            const usuario = document.getElementById('usuario').value;
            
            const params = new URLSearchParams();
            if (fechaInicio) params.append('fecha_inicio', fechaInicio);
            if (fechaFin) params.append('fecha_fin', fechaFin);
            if (estado) params.append('estado', estado);
            if (usuario) params.append('usuario', usuario);
            
            window.location.href = '?' + params.toString();
        }

        function limpiarFiltros() {
            document.getElementById('fecha_inicio').value = '';
            document.getElementById('fecha_fin').value = '';
            document.getElementById('estado').value = '';
            document.getElementById('usuario').value = '';
            window.location.href = 'todas_las_cajas.php';
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
    </script>
</body>
</html>
