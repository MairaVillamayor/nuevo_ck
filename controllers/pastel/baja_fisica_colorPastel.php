<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_color_pastel"])) {
    $id_color_pastel = intval($_POST["id_color_pastel"]);
    $stmt = $conexion->prepare("DELETE FROM color_pastel WHERE id_color_pastel = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id_color_pastel])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Color%20eliminado&mensaje=El%20color%20de%20pastel%20fue%20eliminado%20correctamente&redirect_to=../views/pastel/listado_colorPastel.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20color%20de%20pastel");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>