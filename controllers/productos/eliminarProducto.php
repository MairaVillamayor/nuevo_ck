<?php
session_start();
require_once '../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Método no permitido";
    $_SESSION['status'] = "danger";
    header("Location: ../../views/productos/productos_finalizados.php");
    exit;
}

if (!isset($_POST['id_producto_finalizado'])) {
    $_SESSION['message'] = "ID de producto no recibido";
    $_SESSION['status'] = "danger";
    header("Location: ../../views/productos/productos_finalizados.php");
    exit;
}

$id = (int) $_POST['id_producto_finalizado'];

try {
    $pdo = getConexion();
    $stmt = $pdo->prepare("DELETE FROM producto_finalizado WHERE ID_producto_finalizado = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = "Producto eliminado correctamente";
    $_SESSION['status'] = "success";

} catch (Exception $e) {
    $_SESSION['message'] = "Error al eliminar: " . $e->getMessage();
    $_SESSION['status'] = "danger";
}

header("Location: ../../views/productos/productos_finalizados.php");
exit;
