<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auditoria.php';

$pdo = getConexion();

if (isset($_POST["ID_usuario"])) {
    $id = intval($_POST["ID_usuario"]);

    // OPCIONAL: traer nombre para ponerlo en la auditoría
    $stmtU = $pdo->prepare("SELECT usuario_nombre FROM usuarios WHERE ID_usuario = ?");
    $stmtU->execute([$id]);
    $usuario = $stmtU->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID_usuario = ?");
    
    if ($stmt->execute([$id])) {

        /* 🔥 AUDITORÍA */
        registrarAuditoria(
            "DELETE",
            "usuarios",
            $id,
            "Se eliminó el usuario '$usuario'"
        );

        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Usuario%20eliminado&mensaje=El%20usuario%20se%20eliminó%20correctamente&redirect_to=../views/usuario/Listado_Usuarios.php&delay=2");
        exit();
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20eliminar%20el%20usuario");
        exit();
    }
}
?>
