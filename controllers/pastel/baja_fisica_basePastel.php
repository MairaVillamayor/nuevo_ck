<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_base_pastel"])) {
    $id_base_pastel = intval($_POST["id_base_pastel"]);
    $stmt = $conexion->prepare("DELETE FROM base_pastel WHERE id_base_pastel = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id_base_pastel])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Base%20eliminada&mensaje=La%20base%20de%20pastel%20fue%20eliminada%20correctamente&redirect_to=../views/pastel/listado_basePastel.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20la%20base%20de%20pastel");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>