<?php
session_start();
require_once "../../config/conexion.php";
include("../../includes/navegacion.php");

if (!isset($_POST['ID_pedido'])) {
    die("Pedido no especificado.");

}    
    $id_pedido = intval($_POST['ID_pedido']);

try {
    $sql = "UPDATE pedido SET RELA_estado = 4 WHERE ID_pedido = :id_pedido";
    $stmt = $conexion->prepare($sql);
    $success = $stmt->execute([':id_pedido' => $id_pedido]);

    if ($success) {
        $_SESSION['success'] = "❌ El pedido #$id_pedido fue cancelado correctamente.";
    } else {
        $_SESSION['error'] = "⚠️ Error al cancelar el pedido (execute devolvió FALSE).";
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "❌ Error en la base de datos: " . $e->getMessage();

}
