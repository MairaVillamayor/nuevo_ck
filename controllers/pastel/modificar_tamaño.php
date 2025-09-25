<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["ID_tamaño"]) ||
        !isset($_POST["tamaño_nombre"]) ||
        !isset($_POST["tamaño_medidas"]) ||
        !isset($_POST["id_tamaño"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario&redirect_to=../views/pastel/listado_tamaño.php&delay=3");
        exit();
    }

    $id_tamaño = intval($_POST["id_tamaño"]);
    $tamaño_nombre = trim($_POST["tamaño_nombre"]);
    $tamaño_medidas = trim($_POST["tamaño_medidas"]);
    $tamaño_precio = floatval($_POST["tamaño_precio"]);

    if ($tamaño_nombre === "" ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20tamaño%20no%20puede%20estar%20vacío&redirect_to=../views/pastel/listado_tamaño.php&delay=3");
        exit();
    }

    try {
        $conexion = Conexion::getInstance()->getConnection();
        $sql = "UPDATE `tamaño`
                SET `tamaño_nombre` = :tamaño_nombre, 
                    `tamaño_medidas` = :tamaño_medidas
                    `tamaño_precio` = :tamaño_precio
                WHERE `id_tamaño` = :id_tamaño";
        $stmt = $conexion->prepare($sql);
        $result = $stmt->execute([
            ':tamaño_nombre' => $tamaño_nombre,
            ':tamaño_medidas' => $tamaño_medidas,
            ':tamaño_precio' => $tamaño_precio,
            ':id_tamaño' => $id_tamaño
        ]);

        if ($result) {
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tamaño%20modificado&mensaje=El%20tamaño%20fue%20modificado%20correctamente&redirect_to=../views/pastel/listado_tamaño.php&delay=2");
            exit();
        } else {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20modificar%20el%20tamaño&redirect_to=../views/pastel/listado_tamaño.php&delay=3");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error%20de%20base%20de%20datos&mensaje=" . urlencode($e->getMessage()) . "&redirect_to=../views/pastel/listado_tamaño.php&delay=4");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido&redirect_to=../views/pastel/listado_tamaño.php&delay=3");
    exit();
}

?>