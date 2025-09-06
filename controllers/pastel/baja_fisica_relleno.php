<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_relleno"])) {
    $id = intval($_POST["id_relleno"]);
    $stmt = $conexion->prepare("DELETE FROM relleno WHERE id_relleno = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Relleno%20eliminado&mensaje=El%20relleno%20fue%20eliminado%20correctamente&redirect_to=../views/pastel/listado_relleno.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20relleno");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>