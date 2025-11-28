<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_usuario"])) {
    $id = intval($_POST["ID_usuario"]);
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE ID_usuario = ?");

 
    
    if ($stmt->execute([$id])) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Usuario%20eliminado&mensaje=El%20usuario%20se%20eliminó%20correctamente&redirect_to=../views/usuario/Listado_Usuarios.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20usuario");
        exit();
    }
    
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=ID%20no%20recibido");
    exit();
}
?>