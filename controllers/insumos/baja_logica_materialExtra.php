<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_material_extra"])) {
    $id = intval($_POST["id_material_extra"]);

    try {
        $stmt = $conexion->prepare("UPDATE material_extra SET RELA_estado_insumos = 2 WHERE ID_material_extra = ?");
        $success = $stmt->execute([$id]);

        if ($success) {
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Baja%20l%C3%B3gica%20realizada&mensaje=El%20material%20extra%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
            exit();
        } else {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%20material%20extra");
            exit();
        }

    } catch (PDOException $e) {
        // Podés loguear el error con $e->getMessage()
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20en%20la%20base%20de%20datos");
        exit();
    }

} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
    exit();
}
?>
