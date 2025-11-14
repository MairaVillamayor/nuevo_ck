<?php
require_once ("../../config/conexion.php");
header('Content-Type: application/json');


try {
    if (!isset($_POST["idProducto"])) {
        http_response_code(400);
        echo json_encode(array('error' => 'ID de Producto faltante en la solicitud.'));
        exit;
    }

    $idProducto = $_POST["idProducto"];
    error_log("ID recibido: " . $idProducto);

    $stmt = $conexion->getConnection()->prepare("SELECT id_producto_finalizado, 
                                                        producto_finalizado_nombre, 
                                                        producto_finalizado_precio
     FROM producto_finalizado WHERE id_producto_finalizado = :id");
    $stmt->bindParam(':id', $idProducto, PDO::PARAM_INT);
    $stmt->execute();

    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        echo json_encode($producto);
    } else {
        http_response_code(404);
        echo json_encode(array('error' => 'Producto no encontrado'));
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error de conexión o base de datos: ' . $e->getMessage()));
}