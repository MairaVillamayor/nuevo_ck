<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

$pdo = Conexion::getInstance()->getConnection();

if (isset($_POST["id_producto_finalizado"])) {

    $id = intval($_POST["id_producto_finalizado"]);

    $stmt = $pdo->prepare("DELETE FROM producto_finalizado WHERE ID_producto_finalizado = ?");

    if ($stmt->execute([$id])) {
        $_SESSION['message'] = "El producto fue eliminado correctamente.";
        $_SESSION['status'] = "success";

    } else {
        $_SESSION['message'] = "No se pudo eliminar el producto.";
        $_SESSION['status'] = "danger";
    }
    header("Location: ../../views/productos/productos_finalizados.php");
    exit();

} else {
    header("Location: ../../views/productos/productos_finalizados.php");
    exit();
}
?>
