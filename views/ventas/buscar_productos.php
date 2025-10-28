<?php
include 'db_conexion.php'; // Incluir tu archivo de conexión
header('Content-Type: application/json');

$termino = $_GET['q'] ?? '';
$termino = "%" . $termino . "%";

// Consulta SQL real
$stmt = $pdo->prepare("SELECT id, nombre, precio, alicuota_iva FROM Productos WHERE nombre LIKE ? LIMIT 10");
$stmt->execute([$termino]);
$productos = $stmt->fetchAll();

echo json_encode($productos);
?>