<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "msg" => "invalid_method"]);
    exit;
}

if (!isset($_SESSION['carrito'])) {
    echo json_encode(["status" => "error", "msg" => "carrito_vacio"]);
    exit;
}

$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

if ($producto_id <= 0 || $cantidad < 1 || !isset($_SESSION['carrito'][$producto_id])) {
    echo json_encode(["status" => "error", "msg" => "datos_invalidos"]);
    exit;
}

// Actualizar cantidad
$_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;

echo json_encode(["status" => "success"]);
exit;
