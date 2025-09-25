<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["id_relleno"]) ||
        !isset($_POST["relleno_nombre"]) ||
        !isset($_POST["relleno_descripcion"]) ||
        !isset($_POST["relleno_precio"]) ||
        !isset($_POST["RELA_estado_decoraciones"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario.");
        exit();
    }

    $id_relleno = intval($_POST["id_relleno"]);
    $relleno_nombre = trim($_POST["relleno_nombre"]);
    $relleno_descripcion = trim($_POST["relleno_descripcion"]);
    $relleno_precio = floatval($_POST["relleno_precio"]);
    $RELA_estado_decoraciones = intval($_POST["RELA_estado_decoraciones"]);

    if ($relleno_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20relleno%20no%20puede%20estar%20vac%C3%ADo.");
        exit();
    }

    try {
        $pdo = getConexion();
        $sql = "UPDATE relleno 
                SET relleno_nombre = :relleno_nombre, 
                    relleno_descripcion = :relleno_descripcion, 
                    relleno_precio = :relleno_precio,
                    RELA_estado_decoraciones = :RELA_estado_decoraciones 
                WHERE id_relleno = :id_relleno";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'relleno_nombre' => $relleno_nombre,
            'relleno_descripcion' => $relleno_descripcion,
            'relleno_precio' => $relleno_precio,
            'RELA_estado_decoraciones' => $RELA_estado_decoraciones,
            'id_relleno' => $id_relleno
        ]);
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Relleno%20modificado&mensaje=El%20relleno%20fue%20modificado%20correctamente&redirect_to=../views/pastel/listado_relleno.php&delay=2");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20al%20actualizar%20el%20relleno:%20".urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido.");
    exit();
}
