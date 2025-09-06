<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_sabor"])) {
    $id = intval($_POST["id_sabor"]);
    $stmt = $conexion->prepare("DELETE FROM sabor WHERE id_sabor = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Sabor%20eliminado&mensaje=El%20sabor%20fue%20eliminado%20correctamente&redirect_to=../views/pastel/listado_sabor.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20sabor");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>