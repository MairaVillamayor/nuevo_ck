<?php
require_once("../../config/conexion.php");
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET["term"])) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET["term"] . '%';

    $conexion = Conexion::getInstance()->getConnection();

    $sql = "SELECT 
                id_producto_finalizado, 
                producto_finalizado_nombre,
                stock_actual 
            FROM producto_finalizado 
            WHERE producto_finalizado_nombre LIKE :term 
            LIMIT 10";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();

    $resultadosDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $productos_formateados = [];
    foreach ($resultadosDB as $producto) {
        $productos_formateados[] = [
            'id'    => $producto['id_producto_finalizado'],
            'value' => $producto['producto_finalizado_nombre'],
            'stock' => $producto['stock_actual']
        ];
    }

    echo json_encode($productos_formateados);

} catch (Exception $e) {
    error_log('Error en buscar_producto: ' . $e->getMessage());
    echo json_encode([]);
}
?>
