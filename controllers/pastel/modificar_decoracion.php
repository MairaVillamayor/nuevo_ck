<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["id_decoracion"]) ||
        !isset($_POST["decoracion_nombre"]) ||
        !isset($_POST["decoracion_descripcion"]) ||
        !isset($_POST["RELA_estado_decoraciones"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario.");
        exit();
    }

    $id_decoracion = intval($_POST["id_decoracion"]);
    $decoracion_nombre = trim($_POST["decoracion_nombre"]);
    $decoracion_descripcion = trim($_POST["decoracion_descripcion"]);
    $rela_estado_decoraciones = intval($_POST["RELA_estado_decoraciones"]);

    if ($decoracion_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20de%20la%20decoraci%C3%B3n%20no%20puede%20estar%20vac%C3%ADo.");
        exit();
    }

    try {
        $pdo = getConexion();
        $sql = "UPDATE decoracion 
                SET decoracion_nombre = :decoracion_nombre, 
                    decoracion_descripcion = :decoracion_descripcion, 
                    RELA_estado_decoraciones = :rela_estado_decoraciones 
                WHERE id_decoracion = :id_decoracion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'decoracion_nombre' => $decoracion_nombre,
            'decoracion_descripcion' => $decoracion_descripcion,
            'rela_estado_decoraciones' => $rela_estado_decoraciones,
            'id_decoracion' => $id_decoracion
        ]);
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Decoración%20modificada&mensaje=La%20decoración%20fue%20modificada%20correctamente&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20al%20actualizar%20la%20decoración:%20".urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido.");
    exit();
}
?>