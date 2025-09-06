<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_tematica"])) {
    $id_tematica = intval($_POST["id_tematica"]);
    $stmt = $conexion->prepare("DELETE FROM tematica WHERE id_tematica = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id_tematica])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tem%C3%A1tica%20eliminada&mensaje=La%20tem%C3%A1tica%20fue%20eliminada%20correctamente&redirect_to=../views/pastel/listado_tematica.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20la%20tem%C3%A1tica");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>