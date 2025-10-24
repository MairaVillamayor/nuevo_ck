<?php
/**
 * Formulario de Alta de Insumos - Módulo de Stock
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$pdo = getConexion();

// Obtener datos para los selects
$categorias = $pdo->query("SELECT * FROM categoria_insumos ORDER BY categoria_insumo_nombre")->fetchAll(PDO::FETCH_ASSOC);
$proveedores = $pdo->query("SELECT * FROM proveedor ORDER BY proveedor_nombre")->fetchAll(PDO::FETCH_ASSOC);
$unidades = $pdo->query("SELECT * FROM unidad_medida ORDER BY unidad_medida_nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Insumo - Cake Party</title>
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary { background-color: #e91e63; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
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

        .required {
            color: #e91e63;
        }

        .form-help {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
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
                <a href="dashboard_stock.php" class="nav-item">
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
                <a href="listado_insumos.php" class="nav-item">
                    <i>🔧</i> Gestionar Insumos
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content" id="mainContent">
            <a href="listado_insumos.php" class="back-link">← Volver al Listado</a>
            
            <div class="content-header">
                <h1 class="content-title">
                    <i>➕</i> Agregar Nuevo Insumo
                </h1>
                <p class="content-subtitle">Complete los datos del nuevo insumo para el sistema</p>
                
                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb;">
                        ❌ <?= htmlspecialchars($_GET['mensaje'] ?? 'Error en la operación') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <form action="../../controllers/stock/alta_insumo.php" method="POST">
                    <div class="form-group">
                        <label for="insumo_nombre">Nombre del Insumo <span class="required">*</span></label>
                        <input type="text" 
                               name="insumo_nombre" 
                               id="insumo_nombre" 
                               required
                               placeholder="Ej: Harina de trigo, Crema de leche..."
                               maxlength="50">
                        <div class="form-help">Nombre descriptivo del insumo (máximo 50 caracteres)</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="RELA_unidad_medida">Unidad de Medida <span class="required">*</span></label>
                            <select name="RELA_unidad_medida" id="RELA_unidad_medida" required>
                                <option value="">Seleccione una unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?= $unidad['ID_unidad_medida'] ?>">
                                        <?= htmlspecialchars($unidad['unidad_medida_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="insumo_stock_minimo">Stock Mínimo <span class="required">*</span></label>
                            <input type="number" 
                                   name="insumo_stock_minimo" 
                                   id="insumo_stock_minimo" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   placeholder="Ej: 5.00">
                            <div class="form-help">Cantidad mínima para alertas de stock bajo</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="RELA_categoria_insumos">Categoría <span class="required">*</span></label>
                            <select name="RELA_categoria_insumos" id="RELA_categoria_insumos" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['ID_categoria_insumo'] ?>">
                                        <?= htmlspecialchars($categoria['categoria_insumo_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="RELA_proveedor">Proveedor <span class="required">*</span></label>
                            <select name="RELA_proveedor" id="RELA_proveedor" required>
                                <option value="">Seleccione un proveedor</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?= $proveedor['ID_proveedor'] ?>">
                                        <?= htmlspecialchars($proveedor['proveedor_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="insumo_precio_costo">Precio de Costo (Opcional)</label>
                        <input type="number" 
                               name="insumo_precio_costo" 
                               id="insumo_precio_costo" 
                               step="0.01" 
                               min="0"
                               placeholder="Ej: 1250.50">
                        <div class="form-help">Costo por unidad para control de inventario</div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            ✅ Guardar Insumo
                        </button>
                        <a href="listado_insumos.php" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </form>
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

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const nombre = document.getElementById('insumo_nombre').value.trim();
            const unidad = document.getElementById('RELA_unidad_medida').value;
            const stockMinimo = document.getElementById('insumo_stock_minimo').value;
            const categoria = document.getElementById('RELA_categoria_insumos').value;
            const proveedor = document.getElementById('RELA_proveedor').value;

            if (!nombre || !unidad || !stockMinimo || !categoria || !proveedor) {
                e.preventDefault();
                alert('Por favor complete todos los campos obligatorios marcados con *');
                return false;
            }

            if (parseFloat(stockMinimo) < 0) {
                e.preventDefault();
                alert('El stock mínimo no puede ser negativo');
                return false;
            }
        });
    </script>
</body>
</html>
