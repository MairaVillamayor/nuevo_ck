<?php
require_once __DIR__ . '/../../config/conexion.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["ID_perfil"]) || !isset($_POST["perfil_rol"])) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20para%20modificar%20el%20perfil");
        exit();
    }

    $ID_perfil = intval($_POST["ID_perfil"]);
    $perfil_rol = trim($_POST["perfil_rol"]);

    if ($perfil_rol === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20rol%20no%20puede%20estar%20vac%C3%ADo");
        exit();
    }

    $conexion = Conexion::getInstance()->getConnection();
    $sql = "UPDATE perfiles SET perfil_rol = :perfil_rol WHERE ID_perfil = :ID_perfil";
    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute([
        ':perfil_rol' => $perfil_rol,
        ':ID_perfil' => $ID_perfil
    ]);
    if ($result) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Perfil%20modificado&mensaje=Los%20cambios%20se%20guardaron&redirect_to=../views/admin/listado_perfiles.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20modificar%20el%20perfil");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido");
    exit();
}