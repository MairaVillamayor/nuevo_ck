<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["material_extra_nombre"]) && isset($_POST["material_extra_descri"])) {
    $material_extra_nombre = trim($_POST["material_extra_nombre"]);
    $material_extra_descri = trim($_POST["material_extra_descri"]);
    $estado = 1; // Activo por defecto

    if ($material_extra_nombre !== "" && $material_extra_descri !== "") {
        try {
            $stmt = $conexion->prepare("
                INSERT INTO material_extra (material_extra_nombre, material_extra_descri, RELA_estado_insumos)
                VALUES (?, ?, ?)
            ");
            $success = $stmt->execute([$material_extra_nombre, $material_extra_descri, $estado]);

            if ($success) {
                header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Material%20extra%20creado&mensaje=El%20nuevo%20material%20extra%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
                exit();
            } else {
                header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20material%20extra");
                exit();
            }
        } catch (PDOException $e) {
            // Podés loguear el error si querés depurar
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20en%20la%20base%20de%20datos");
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
