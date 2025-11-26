<?php
session_start();
header("Content-Type: application/json");

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "msg" => "invalid_method"]);
    exit;
}

if (!isset($_SESSION['carrito'])) {
    echo json_encode(["status" => "error", "msg" => "carrito_vacio"]);
    exit;
}

$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

if ($producto_id <= 0 || !isset($_SESSION['carrito'][$producto_id])) {
    echo json_encode(["status" => "error", "msg" => "producto_no_encontrado"]);
    exit;
}

// Eliminar del carrito
unset($_SESSION['carrito'][$producto_id]);

echo json_encode(["status" => "success"]);
exit;
