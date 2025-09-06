<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_material_extra"])) {
    $id = intval($_POST["id_material_extra"]);
    $stmt = $conexion->prepare("UPDATE material_extra SET RELA_estado = 2 WHERE id_material_extra = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Baja%20l%C3%B3gica%20realizada&mensaje=El%20material%20extra%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
        exit();
    } else {
        $stmt->close();
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%20material%20extra");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
    exit();
}
?>
