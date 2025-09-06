<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["modulos_nombre"])) {
    $modulos_nombre = trim($_POST["modulos_nombre"]);

    if ($modulos_nombre != "") {
        $stmt = $conexion->prepare("INSERT INTO modulos (modulos_nombre) VALUES (?)");
        $stmt->bind_param('s', $modulos_nombre);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Módulo%20creado&mensaje=El%20nuevo%20módulo%20se%20dio%20de%20alta%20correctamente");
            exit();
        } else {
            $stmt->close();
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20módulo");
            exit();
        }
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20valores%20requeridos");
        exit();
    }
}
header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20datos");
exit();
?>