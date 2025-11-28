<?php
header('Content-Type: application/json');
require_once("../../config/conexion.php");

try {
    if (isset($conexion) && $stmt = $conexion->prepare($sql)) {

        $stmt->execute();
        $formas_pago = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($formas_pago);

        // Cerrar conexión (opcional en PDO, pero permitido)
        $conexion = null;

    } else {
        // Fallback si la conexión no está definida o la consulta falla
        throw new Exception("Error en la conexión o consulta.");
    }

} catch (Exception $e) {

    // Si hay error, devolvemos un mensaje y datos de prueba (para evitar fallo en el JS)
    error_log("Error al obtener formas de pago: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        ['id' => 1, 'nombre' => 'Efectivo (Fallback)'],
        ['id' => 2, 'nombre' => 'Transferencia (Fallback)'],
    ]);
}
?>
