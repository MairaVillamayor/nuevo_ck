<?php
// guardar_pastel.php
require_once ('../../config/conexion.php');

// 🔹 Variables del formulario
$color = $_POST['RELA_color_pastel'];
$decoracion = $_POST['RELA_decoracion'];
$base = $_POST['RELA_base_pastel'];
$pisos = $_POST['pisos'] ?? [];

// 🔹 Armamos la descripción automáticamente
$descripcion = "Pastel de " . count($pisos) . " pisos, con base ";

$q = $conexion->query("SELECT base_pastel_nombre FROM base_pastel WHERE id_base_pastel = $base");
if ($q && $row = $q->fetch_assoc()) {
    $descripcion .= $row['base_pastel_nombre'];
}
$descripcion .= ", decorado con ";
$q = $conexion->query("SELECT decoracion_nombre FROM decoracion WHERE id_decoracion = $decoracion");
if ($q && $row = $q->fetch_assoc()) {
    $descripcion .= $row['decoracion_nombre'];
}
$descripcion .= ". Detalle de pisos: ";

// Recorrer pisos y armar detalle
$detalles = [];
foreach ($pisos as $num => $datos) {
    $tamano = $datos['RELA_tamaño'];
    $sabor = $datos['RELA_sabor'];
    $relleno = $datos['RELA_relleno'];

    // Buscar nombres
    $q1 = $conexion->query("SELECT tamaño_nombre FROM tamaño WHERE id_tamaño = $tamano");
    $q2 = $conexion->query("SELECT sabor_nombre FROM sabor WHERE id_sabor = $sabor");
    $q3 = $conexion->query("SELECT relleno_nombre FROM relleno WHERE id_relleno = $relleno");

    $nombreTamano = ($q1 && $r1 = $q1->fetch_assoc()) ? $r1['tamaño_nombre'] : "Tamaño";
    $nombreSabor = ($q2 && $r2 = $q2->fetch_assoc()) ? $r2['sabor_nombre'] : "Sabor";
    $nombreRelleno = ($q3 && $r3 = $q3->fetch_assoc()) ? $r3['relleno_nombre'] : "Relleno";

    $detalles[] = "Piso $num: $nombreTamano de $nombreSabor con relleno de $nombreRelleno";
}
$descripcion .= implode("; ", $detalles);

// 🔹 Insertar en pastel_personalizado
$sql_pastel = "INSERT INTO pastel_personalizado 
    (pastel_personalizado_descripcion, pastel_personalizado_pisos_total, RELA_color_pastel, RELA_decoracion, RELA_base_pastel) 
    VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_pastel);
$pisos_total = count($pisos);
$stmt->bind_param("siiii", $descripcion, $pisos_total, $color, $decoracion, $base);
$stmt->execute();
$id_pastel = $stmt->insert_id;

// 🔹 Insertar cada piso con sabor y relleno
foreach ($pisos as $piso_numero => $datos) {
    $tamano = $datos['RELA_tamaño'];
    $sabor = $datos['RELA_sabor'];
    $relleno = $datos['RELA_relleno'];

    // Insertar piso
    $sql_piso = "INSERT INTO pisos (pisos_numero, RELA_pastel_personalizado, RELA_tamaño) VALUES (?, ?, ?)";
    $stmt_piso = $conexion->prepare($sql_piso);
    $stmt_piso->bind_param("iii", $piso_numero, $id_pastel, $tamano);
    $stmt_piso->execute();
    $id_piso = $stmt_piso->insert_id;

    // Insertar sabor del piso
    $sql_sabor = "INSERT INTO pisos_sabor (RELA_sabor, RELA_pisos) VALUES (?, ?)";
    $stmt_sabor = $conexion->prepare($sql_sabor);
    $stmt_sabor->bind_param("ii", $sabor, $id_piso);
    $stmt_sabor->execute();

    // Insertar relleno del piso
    $sql_relleno = "INSERT INTO pisos_relleno (RELA_pisos, RELA_relleno) VALUES (?, ?)";
    $stmt_relleno = $conexion->prepare($sql_relleno);
    $stmt_relleno->bind_param("ii", $id_piso, $relleno);
    $stmt_relleno->execute();
}

// ✅ Confirmación
echo "<h2>🎉 Tu pastel se guardó correctamente</h2>";
echo "<p>$descripcion</p>";
echo "<a href='../../views/cliente/crear_pastel.php'>Volver</a>";

$conexion->close();
?>
