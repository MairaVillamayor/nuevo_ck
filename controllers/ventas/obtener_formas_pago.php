<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");

try {
    $conexionDB = new Conexion();

    // 🧁 Consulta a la tabla real
    $sql = "SELECT ID_metodo_pago AS id, 
                    metodo_pago_descri AS nombre FROM metodo_pago";
    $resultado = $conexion->query($sql);

    $formas_pago = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $formas_pago[] = $row;
        }
    }

    echo json_encode($formas_pago);
    $conexion->close();

} catch (Exception $e) {
    // Si hay error, devolvemos mensaje de prueba (para evitar fallo en el JS)
    echo json_encode([
        ['id' => 1, 'nombre' => 'Efectivo'],
        ['id' => 2, 'nombre' => 'Transferencia'],
    ]);
}
?>
