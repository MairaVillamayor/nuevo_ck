<?php
/**
 * Controlador para cargar contenido dinámico del dashboard de stock
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/StockController.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$stockController = new StockController();
$section = isset($_GET['section']) ? $_GET['section'] : '';

switch ($section) {
    case 'stats':
        $insumos = $stockController->obtenerTodosInsumosConEstado();
        
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
        
        echo json_encode($contadores);
        break;
        
    case 'alertas':
        $insumos_stock_bajo = $stockController->obtenerInsumosStockBajo();
        echo json_encode($insumos_stock_bajo);
        break;
        
    case 'movimientos':
        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
        
        $movimientos = $stockController->obtenerMovimientosPorFecha($fecha_inicio, $fecha_fin);
        echo json_encode($movimientos);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Sección no válida']);
        break;
}
?>
