<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["material_extra_nombre"]) && isset($_POST["material_extra_descripcion"])) {
    $material_extra_nombre = trim($_POST["material_extra_nombre"]);
    $material_extra_descripcion = trim($_POST["material_extra_descripcion"]);

    if ($material_extra_nombre != "" && $material_extra_descripcion != "") {
        $stmt = $conexion->prepare("INSERT INTO material_extra (material_extra_nombre, material_extra_descripcion, rela_estado) VALUES (?, ?, 1)");
        $stmt->bind_param('ss', $material_extra_nombre, $material_extra_descripcion);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Material%20extra%20creado&mensaje=El%20nuevo%20material%20extra%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
            exit();
        } else {
            $stmt->close();
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20material%20extra");
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