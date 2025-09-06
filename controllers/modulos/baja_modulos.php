<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_modulos"])) {
    $ID_modulos = intval($_POST["ID_modulos"]);
    $stmt = $conexion->prepare("DELETE FROM modulos WHERE ID_modulos = ?");
    $stmt->bind_param('i', $ID_modulos);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Módulo%20eliminado&mensaje=El%20módulo%20fue%20eliminado%20correctamente");
        exit();
    } else {
        $stmt->close();
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20módulo");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>
