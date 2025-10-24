<?php
/**
 * Listado de Insumos - Módulo de Stock
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../controllers/stock/StockController.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$stockController = new StockController();

// Obtener todos los insumos con información completa
$insumos = $stockController->obtenerTodosInsumosConEstado();

// Obtener datos para los selects (por si se necesitan)
$pdo = getConexion();
$categorias = $pdo->query("SELECT * FROM categoria_insumos ORDER BY categoria_insumo_nombre")->fetchAll(PDO::FETCH_ASSOC);
$proveedores = $pdo->query("SELECT * FROM proveedor ORDER BY proveedor_nombre")->fetchAll(PDO::FETCH_ASSOC);
$unidades = $pdo->query("SELECT * FROM unidad_medida ORDER BY unidad_medida_nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Insumos - Cake Party</title>
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

        .actions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

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
        .btn-info { background-color: #2196f3; color: white; }
        .btn-warning { background-color: #ff9800; color: white; }
        .btn-danger { background-color: #f44336; color: white; }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .table-container {
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

        tbody tr:hover {
            background-color: #fff0f5;
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

        .actions-table {
            display: flex;
            gap: 5px;
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

        .search-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-container input,
        .search-container select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .search-container input:focus,
        .search-container select:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .search-container input {
            flex: 2;
            min-width: 250px;
        }

        .search-container select {
            flex: 1;
            min-width: 150px;
        }

        .search-container button {
            background: #e91e63;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-container button:hover {
            background: #c2185b;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-container input,
            .search-container select {
                width: 100%;
                min-width: auto;
            }
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
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }
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
                <a href="../stock/dashboard_stock.php" class="nav-item">
                    <i>📊</i> Dashboard
                </a>
                <a href="../../controllers/stock/ingreso_stock.php" class="nav-item">
                    <i>➕</i> Ingreso de Stock
                </a>
                <a href="../../controllers/stock/movimientos_stock.php" class="nav-item">
                    <i>📈</i> Ver Movimientos
                </a>
                <a href="../../controllers/stock/alertas_stock.php" class="nav-item">
                    <i>⚠️</i> Alertas de Stock
                </a>
                <a href="#" class="nav-item active">
                    <i>🔧</i> Gestionar Insumos
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content" id="mainContent">
            <a href="../admin/admin_dashboard.php" class="back-link">← Volver al Dashboard</a>
            
            <div class="content-header">
                <h1 class="content-title">
                    <i>🔧</i> Gestionar Insumos
                </h1>
                <p class="content-subtitle">Administrar catálogo de insumos y sus configuraciones</p>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #c3e6cb;">
                        ✅ <?= htmlspecialchars($_GET['mensaje'] ?? 'Operación realizada exitosamente') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb;">
                        ❌ <?= htmlspecialchars($_GET['mensaje'] ?? 'Error en la operación') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="actions-header">
                <div>
                    <a href="form_alta_insumo.php" class="btn btn-success">
                        ➕ Agregar Nuevo Insumo
                    </a>
                    <a href="../../controllers/stock/ingreso_stock.php" class="btn btn-primary">
                        📦 Ingreso de Stock
                    </a>
                </div>
                <div>
                    <span class="badge" style="background: #f8f9fa; padding: 8px 15px; border-radius: 20px; color: #666;">
                        Total: <?= count($insumos) ?> insumos
                    </span>
                </div>
            </div>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Buscar por nombre..." />
                <select id="filterCategoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['categoria_insumo_nombre']) ?>">
                            <?= htmlspecialchars($categoria['categoria_insumo_nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="filterProveedor">
                    <option value="">Todos los proveedores</option>
                    <?php foreach($proveedores as $proveedor): ?>
                        <option value="<?= htmlspecialchars($proveedor['proveedor_nombre']) ?>">
                            <?= htmlspecialchars($proveedor['proveedor_nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button id="clearFilters" class="btn btn-secondary">Limpiar filtros</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Unidad</th>
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
                                <td colspan="9" class="no-data">No hay insumos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($insumos as $insumo): ?>
                                <tr>
                                    <td><strong>#<?= $insumo['ID_insumo'] ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($insumo['insumo_nombre']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($insumo['categoria_insumo_nombre']) ?></td>
                                    <td><?= htmlspecialchars($insumo['insumo_unidad_medida']) ?></td>
                                    <td>
                                        <strong><?= number_format($insumo['insumo_stock_actual'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <?= number_format($insumo['insumo_stock_minimo'], 2) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $insumo['estado_stock'])) ?>">
                                            <?= $insumo['estado_stock'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($insumo['proveedor_nombre']) ?></td>
                                    <td>
                                        <div class="actions-table">
                                            <a href="../../controllers/stock/historial_insumo.php?id=<?= $insumo['ID_insumo'] ?>" 
                                               class="btn btn-small btn-info" title="Ver historial">
                                                📊
                                            </a>
                                            <a href="../../controllers/stock/ingreso_stock.php?insumo=<?= $insumo['ID_insumo'] ?>" 
                                               class="btn btn-small btn-success" title="Ingresar stock">
                                                ➕
                                            </a>
                                            <a href="modificar_insumo.php?id=<?= $insumo['ID_insumo'] ?>" 
                                               class="btn btn-small btn-warning" title="Modificar">
                                                ✏️
                                            </a>
                                        </div>
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
        // Toggle sidebar functionality
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

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterCategoria = document.getElementById('filterCategoria');
            const filterProveedor = document.getElementById('filterProveedor');
            const tableRows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const categoriaFilter = filterCategoria.value.toLowerCase();
                const proveedorFilter = filterProveedor.value.toLowerCase();
                
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    // Obtener valores de cada columna
                    const nombre = row.cells[1].textContent.toLowerCase();
                    const categoria = row.cells[2].textContent.toLowerCase();
                    const proveedor = row.cells[7].textContent.toLowerCase();
                    
                    // Verificar si cumple con todos los filtros
                    const matchNombre = nombre.includes(searchTerm);
                    const matchCategoria = !categoriaFilter || categoria.includes(categoriaFilter);
                    const matchProveedor = !proveedorFilter || proveedor.includes(proveedorFilter);
                    
                    // Mostrar u ocultar fila
                    if (matchNombre && matchCategoria && matchProveedor) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Actualizar contador de resultados
                const totalBadge = document.querySelector('.badge');
                if (totalBadge) {
                    totalBadge.textContent = `Total: ${visibleCount} insumos`;
                }
            }

            // Event listeners
            searchInput.addEventListener('keyup', filterTable);
            filterCategoria.addEventListener('change', filterTable);
            filterProveedor.addEventListener('change', filterTable);

            document.getElementById('clearFilters').addEventListener('click', () => {
                searchInput.value = '';
                filterCategoria.value = '';
                filterProveedor.value = '';
                filterTable();
            });
        });
    </script>
</body>
</html>
