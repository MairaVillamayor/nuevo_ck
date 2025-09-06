<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["ID_modulos"]) || !isset($_POST["modulos_nombre"])) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario");
        exit();
    }

    $ID_modulos = intval($_POST["ID_modulos"]);
    $modulos_nombre = trim($_POST["modulos_nombre"]);

    if ($modulos_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20módulo%20no%20puede%20estar%20vacío");
        exit();
    }

    $conexion = Conexion::getInstance()->getConnection();
    $sql = "UPDATE modulos SET modulos_nombre = :modulos_nombre WHERE ID_modulos = :ID_modulos";
    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute([
        'modulos_nombre' => $modulos_nombre,
        'ID_modulos' => $ID_modulos
    ]);
    if ($result) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Módulo%20modificado&mensaje=El%20módulo%20fue%20modificado%20correctamente");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20modificar%20el%20módulo");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido");
    exit();
}
