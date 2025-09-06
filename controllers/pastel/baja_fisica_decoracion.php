<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_decoracion"])) {
    $id_decoracion = intval($_POST["id_decoracion"]);
    $stmt = $conexion->prepare("DELETE FROM decoracion WHERE id_decoracion = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id_decoracion])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Decoración%20eliminada&mensaje=La%20decoración%20fue%20eliminada%20correctamente&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20la%20decoración");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>