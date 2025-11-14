<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_producto_finalizado"])) {
    $id = intval($_POST["id_producto_finalizado"]);
    $stmt = $conexion->prepare("DELETE FROM producto_finalizado WHERE id_producto_finalizado = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Producto%20eliminado&mensaje=El%20producto%20fue%20eliminado%20correctamente&redirect_to=../views/productos/productos_finalizados.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20producto");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>
