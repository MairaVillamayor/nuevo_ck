<?php
require_once __DIR__ . '/../../config/conexion.php';

if (
    isset($_POST["id_tematica"]) &&
    isset($_POST["tematica_descripcion"]) &&
    isset($_POST["rela_estado"])
) {
    $id_tematica = intval($_POST["id_tematica"]);
    $tematica_descripcion = trim($_POST["tematica_descripcion"]);
    $estado = intval($_POST["rela_estado"]);

    try {
        $pdo = getConexion();
        $sql = "UPDATE tematica SET tematica_descripcion = :tematica_descripcion, rela_estado = :estado WHERE id_tematica = :id_tematica";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'tematica_descripcion' => $tematica_descripcion,
            'estado' => $estado,
            'id_tematica' => $id_tematica
        ]);
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tem%C3%A1tica%20modificada&mensaje=La%20tem%C3%A1tica%20fue%20modificada%20correctamente&redirect_to=../views/pastel/listado_tematica.php&delay=2");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20al%20actualizar%20la%20tem%C3%A1tica:%20".urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario.");
    exit();
}
?>