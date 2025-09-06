<?php
require_once __DIR__ . '/../../config/conexion.php';


if (isset($_POST["ID_perfil"])) {
    $id = intval($_POST["ID_perfil"]);
    $stmt = $conexion->prepare("DELETE FROM perfiles WHERE ID_perfil = ?");
    
    // Cambiar bind_param por execute con array
    if ($stmt->execute([$id])) {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Perfil%20eliminado&mensaje=El%20perfil%20se%20eliminó%20correctamente&redirect_to=../views/admin/listado_perfiles.php&delay=2");
        exit();
    } else {
        // No necesitas close() en PDO
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20perfil");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>