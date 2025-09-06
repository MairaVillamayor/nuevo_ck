<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_tamaño"])) {  // Cambiar a mayúsculas
    $id = intval($_POST["ID_tamaño"]);
    $stmt = $conexion->prepare("DELETE FROM tamaño WHERE id_tamaño = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tama%C3%B1o%20eliminado&mensaje=El%20tama%C3%B1o%20fue%20eliminado%20correctamente&redirect_to=../views/pastel/listado_tamaño.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20tama%C3%B1o");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>