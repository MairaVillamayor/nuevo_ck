<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["id_sabor"]) ||
        !isset($_POST["sabor_nombre"]) ||
        !isset($_POST["sabor_descripcion"]) ||
        !isset($_POST["rela_estado"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario.");
        exit();
    }

    $id_sabor = intval($_POST["id_sabor"]);
    $sabor_nombre = trim($_POST["sabor_nombre"]);
    $sabor_descripcion = trim($_POST["sabor_descripcion"]);
    $rela_estado = intval($_POST["rela_estado"]);

    if ($sabor_nombre === "") {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20sabor%20no%20puede%20estar%20vac%C3%ADo.");
        exit();
    }

    try {
        $pdo = getConexion();
        $sql = "UPDATE sabor 
                SET sabor_nombre = :sabor_nombre, 
                    sabor_descripcion = :sabor_descripcion, 
                    RELA_estado_decoraciones = :rela_estado 
                WHERE id_sabor = :id_sabor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'sabor_nombre' => $sabor_nombre,
            'sabor_descripcion' => $sabor_descripcion,
            'rela_estado' => $rela_estado,
            'id_sabor' => $id_sabor
        ]);
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Sabor%20modificado&mensaje=El%20sabor%20fue%20modificado%20correctamente&redirect_to=../views/pastel/listado_sabor.php&delay=2");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20al%20actualizar%20el%20sabor:%20".urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido.");
    exit();
}
?>