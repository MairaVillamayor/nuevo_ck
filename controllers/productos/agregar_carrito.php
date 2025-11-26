<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

header("Content-Type: application/json");

// Requiere estar logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "msg" => "not_logged"]);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "msg" => "invalid_method"]);
    exit;
}

$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
$cantidad = isset($_POST['cantidad']) ? max(1, (int)$_POST['cantidad']) : 1;

if ($producto_id <= 0) {
    echo json_encode(["status" => "error", "msg" => "invalid_product"]);
    exit;
}

try {
    $pdo = getConexion();

    // Traer información del producto
    $stmt = $pdo->prepare("
        SELECT producto_finalizado_nombre, producto_finalizado_precio, stock_actual
        FROM producto_finalizado
        WHERE ID_producto_finalizado = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $producto_id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        echo json_encode(["status" => "error", "msg" => "not_found"]);
        exit;
    }

    if ($p['stock_actual'] < $cantidad) {
        echo json_encode(["status" => "error", "msg" => "stock_insufficient"]);
        exit;
    }

    // Inicializar carrito
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar o sumar
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$producto_id] = [
            'id' => $producto_id,
            'nombre' => $p['producto_finalizado_nombre'],
            'precio' => (float)$p['producto_finalizado_precio'],
            'cantidad' => $cantidad
        ];
    }

    echo json_encode(["status" => "success"]);
    exit;

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => "exception"]);
    exit;
}
