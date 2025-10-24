<?php
/**
 * Controlador para ingreso manual de stock
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
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos recibidos
        $insumo_id = isset($_POST['insumo_id']) ? (int)$_POST['insumo_id'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (float)$_POST['cantidad'] : 0;
        $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

        if (!$insumo_id || $cantidad <= 0) {
            throw new Exception("Datos inválidos. Verifique el insumo y la cantidad.");
        }

        // Registrar el ingreso
        $resultado = $stockController->registrarIngreso($insumo_id, $cantidad, $observaciones);

        if ($resultado['success']) {
            $response = [
                'success' => true, 
                'message' => 'Stock ingresado correctamente'
            ];
        } else {
            throw new Exception($resultado['error']);
        }

    } catch (Exception $e) {
        $response = [
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Obtener lista de insumos para el formulario
$insumos = $stockController->obtenerTodosInsumosConEstado();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Stock - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #fff0f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h1 {
            color: #e91e63;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #e91e63;
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        select, input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #e91e63;
        }

        .btn {
            background-color: #e91e63;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        .btn:hover {
            background-color: #d81b60;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stock-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #e91e63;
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
    <?php include '../../includes/navegacion.php'; ?>
    
    <div class="container">
        <a href="../admin/admin_dashboard.php" class="back-link">← Volver al Dashboard</a>
        
        <h1>📦 Ingreso de Stock</h1>

        <?php if ($response['message']): ?>
            <div class="alert <?= $response['success'] ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($response['message']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="insumo_id">Insumo:</label>
                <select name="insumo_id" id="insumo_id" required onchange="actualizarInfoStock()">
                    <option value="">Seleccione un insumo</option>
                    <?php foreach ($insumos as $insumo): ?>
                        <option value="<?= $insumo['ID_insumo'] ?>" 
                                data-stock="<?= $insumo['insumo_stock_actual'] ?>"
                                data-minimo="<?= $insumo['insumo_stock_minimo'] ?>"
                                data-unidad="<?= htmlspecialchars($insumo['insumo_unidad_medida']) ?>"
                                data-estado="<?= htmlspecialchars($insumo['estado_stock']) ?>">
                            <?= htmlspecialchars($insumo['insumo_nombre']) ?> 
                            (<?= htmlspecialchars($insumo['categoria_insumo_nombre']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="stock-info" class="stock-info" style="display: none;">
                <strong>Información del Stock:</strong><br>
                <span id="stock-actual"></span><br>
                <span id="stock-minimo"></span><br>
                <span id="estado-stock"></span>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad a ingresar:</label>
                <input type="number" 
                       name="cantidad" 
                       id="cantidad" 
                       step="0.01" 
                       min="0.01" 
                       required
                       placeholder="Ingrese la cantidad">
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones (opcional):</label>
                <textarea name="observaciones" 
                          id="observaciones" 
                          rows="3" 
                          placeholder="Ej: Compra realizada a proveedor X, lote Y..."></textarea>
            </div>

            <button type="submit" class="btn">✅ Registrar Ingreso de Stock</button>
        </form>
    </div>

    <script>
        function actualizarInfoStock() {
            const select = document.getElementById('insumo_id');
            const stockInfo = document.getElementById('stock-info');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                const stockActual = option.getAttribute('data-stock');
                const stockMinimo = option.getAttribute('data-minimo');
                const unidad = option.getAttribute('data-unidad');
                const estado = option.getAttribute('data-estado');
                
                document.getElementById('stock-actual').textContent = 
                    `Stock actual: ${stockActual} ${unidad}`;
                document.getElementById('stock-minimo').textContent = 
                    `Stock mínimo: ${stockMinimo} ${unidad}`;
                document.getElementById('estado-stock').textContent = 
                    `Estado: ${estado}`;
                
                stockInfo.style.display = 'block';
            } else {
                stockInfo.style.display = 'none';
            }
        }
    </script>
</body>
</html>
