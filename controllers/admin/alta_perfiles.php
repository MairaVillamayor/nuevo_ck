<?php
require_once __DIR__ . '/../../config/conexion.php';


if (isset($_POST["perfil_rol"])) {
    $perfil_rol = trim($_POST["perfil_rol"]);

    if ($perfil_rol != "") {
        try {
            $stmt = $conexion->prepare("INSERT INTO perfiles (perfil_rol) VALUES (:perfil_rol)");
            $stmt->bindParam(':perfil_rol', $perfil_rol, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Perfil%20creado&mensaje=El%20nuevo%20perfil%20fue%20creado&redirect_to=../views/admin/listado_perfiles.php&delay=2");
                exit();
            } else {
                header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20perfil");
                exit();
            }
        } catch (PDOException $e) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20valores%20requeridos");
        exit();
    }
}
header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20datos");
exit();
