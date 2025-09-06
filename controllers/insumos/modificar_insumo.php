<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["ID_insumo"]) ||
        !isset($_POST["insumo_nombre"]) ||
        !isset($_POST["insumo_unidad_medida"]) ||
        !isset($_POST["RELA_estado_insumo"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario");
        exit();
    }

    $id_insumo = intval($_POST["ID_insumo"]);
    $insumo_nombre = trim($_POST["insumo_nombre"]);
    $insumo_unidad_medida = trim($_POST["insumo_unidad_medida"]);
    $rela_estado_insumo = intval($_POST["RELA_estado_insumo"]);

    if ($insumo_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20insumo%20no%20puede%20estar%20vac%C3%ADo");
        exit();
    }

    $conexion = Conexion::getInstance()->getConnection();
    $sql = "UPDATE insumos 
            SET insumo_nombre = :insumo_nombre, 
                insumo_unidad_medida = :insumo_unidad_medida, 
                RELA_estado_insumo = :rela_estado_insumo 
            WHERE ID_insumo = :id_insumo";
    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute([
        'insumo_nombre' => $insumo_nombre,
        'insumo_unidad_medida' => $insumo_unidad_medida,
        'rela_estado_insumo' => $rela_estado_insumo,
        'id_insumo' => $id_insumo
    ]);
    if ($result) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Insumo%20modificado&mensaje=El%20insumo%20fue%20modificado%20correctamente&redirect_to=../views/insumos/listado_insumo.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20modificar%20el%20insumo");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido");
    exit();
}
?>