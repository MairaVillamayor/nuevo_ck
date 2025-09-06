<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["tematica_descripcion"])) {
    $tematica_descripcion = trim($_POST["tematica_descripcion"]);

    if ($tematica_descripcion != "") {
        $pdo = getConexion(); // Asumiendo que esta función retorna una instancia PDO
        $stmt = $pdo->prepare("INSERT INTO tematica (tematica_descripcion, RELA_estado_decoraciones) VALUES (?, 1)");
        if ($stmt->execute([$tematica_descripcion])) {
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Temática%20creada&mensaje=La%20nueva%20temática%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_tematica.php&delay=2");
            exit();
        } else {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20la%20temática");
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
