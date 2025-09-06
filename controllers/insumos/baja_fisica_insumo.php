<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_insumo"])) {  // Cambiar para que coincida con el formulario
    $id_insumo = intval($_POST["ID_insumo"]);
    $stmt = $conexion->prepare("DELETE FROM insumos WHERE ID_insumo = ?");  // Cambiar tabla también
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id_insumo])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Insumo%20eliminado&mensaje=El%20insumo%20fue%20eliminado%20correctamente&redirect_to=../views/insumos/listado_insumo.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20insumo");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>