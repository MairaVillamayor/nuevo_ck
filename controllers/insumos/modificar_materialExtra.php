<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["id_material_extra"]) ||
        !isset($_POST["material_extra_nombre"]) ||
        !isset($_POST["material_extra_descri"]) ||
        !isset($_POST["material_extra_precio"]) ||
        !isset($_POST["rela_estado_insumos"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario");
        exit();
    }

    $id_material_extra = intval($_POST["id_material_extra"]);
    $material_extra_nombre = trim($_POST["material_extra_nombre"]);
    $material_extra_descri = trim($_POST["material_extra_descri"]);
    $material_extra_precio = floatval($_POST["material_extra_precio"]);
    $rela_estado_insumos = intval($_POST["rela_estado_insumos"]);

    if ($material_extra_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20material%20extra%20no%20puede%20estar%20vac%C3%ADo");
        exit();
    }

    $conexion = Conexion::getInstance()->getConnection();
    $sql = "UPDATE material_extra 
            SET material_extra_nombre = :material_extra_nombre, 
                material_extra_descri = :material_extra_descri, 
                material_extra_precio = :material_extra_precio,
                rela_estado_insumos = :rela_estado_insumos 
            WHERE id_material_extra = :id_material_extra";
    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute([
        'material_extra_nombre' => $material_extra_nombre,
        'material_extra_descri' => $material_extra_descri,
        'material_extra_precio' => $material_extra_precio,
        'rela_estado_insumos' => $rela_estado_insumos,
        'id_material_extra' => $id_material_extra
    ]);
    if ($result) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Material%20extra%20modificado&mensaje=El%20material%20extra%20fue%20modificado%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20modificar%20el%20material%20extra");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido");
    exit();
}