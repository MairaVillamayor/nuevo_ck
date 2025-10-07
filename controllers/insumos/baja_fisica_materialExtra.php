<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_material_extra"])) {
    $id = intval($_POST["id_material_extra"]);
    $stmt = $conexion->prepare("DELETE FROM material_extra WHERE ID_material_extra = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Material%20extra%20eliminado&mensaje=El%20material%20extra%20fue%20eliminado%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20material%20extra");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>
