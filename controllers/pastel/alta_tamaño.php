<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["tamaño_nombre"]) && isset($_POST["tamaño_medidas"])) {
    $tamaño_nombre = $_POST["tamaño_nombre"];
    $tamaño_medidas = $_POST["tamaño_medidas"];

    if ($tamaño_nombre != "" && $tamaño_medidas != "") {
        $tamaño_nombre = $conexion->real_escape_string($tamaño_nombre);
        $tamaño_medidas = $conexion->real_escape_string($tamaño_medidas);

        $conexion->query("INSERT INTO tamaño (tamaño_nombre, tamaño_medidas)
                        VALUES ('$tamaño_nombre', '$tamaño_medidas')")
            or die($conexion->error);

        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tamaño%20creado&mensaje=El%20nuevo%20tamaño%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_tamaño.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error%20al%20crear%20tamaño&mensaje=No%20se%20pudo%20dar%20de%20alta%20el%20nuevo%20tamaño&redirect_to=../views/pastel/listado_tamaño.php&delay=3");
        exit();
    }
}
