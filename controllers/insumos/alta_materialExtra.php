<?php
require_once __DIR__ . '/../../config/conexion.php';

if (
    !isset($_POST["material_extra_nombre"]) || 
    !isset($_POST["material_extra_descri"]) || 
    !isset($_POST["material_extra_precio"])
) {
    http_response_code(400); // Bad Request
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20todos%20los%20datos%20requeridos");
    exit();
}


$material_extra_nombre = trim($_POST["material_extra_nombre"]);
$material_extra_descri = trim($_POST["material_extra_descri"]);

// Se usa FILTER_VALIDATE_FLOAT para asegurar que es un número, o se convierte a 0 si falla.
$material_extra_precio = filter_var($_POST["material_extra_precio"], FILTER_VALIDATE_FLOAT);

if ($material_extra_nombre === "" || $material_extra_descri === "" || $material_extra_precio === false || $material_extra_precio <= 0) {
    http_response_code(400); // Bad Request
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Los%20valores%20proporcionados%20no%20son%20v%C3%A1lidos");
    exit();
}

$conexion = getConexion();

try {
    $stmt = $conexion->prepare("
        INSERT INTO material_extra 
        (material_extra_nombre, material_extra_descri, material_extra_precio, RELA_estado_insumos)
        VALUES (?, ?, ?, 1)
    ");
    
    $success = $stmt->execute([$material_extra_nombre, $material_extra_descri, $material_extra_precio]);

    if ($success) {
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Material%20extra%20creado&mensaje=El%20nuevo%20material%20extra%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/insumos/listado_materialExtra.php&delay=2");
        exit();
    } else {
        http_response_code(500); // Internal Server Error
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20material%20extra%20(Error%20de%20ejecuci%C3%B3n)");
        exit();
    }
} catch (PDOException $e) {
   
    http_response_code(500); // Internal Server Error
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20en%20la%20base%20de%20datos");
    exit();
}

?>