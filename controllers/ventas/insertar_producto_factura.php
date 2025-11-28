<?php 
require_once("../../config/conexion.php"); 

try { 
    // Recibir datos
    $RELA_factura = $_POST["RELA_factura"] ?? null;
    $RELA_producto_finalizado = $_POST["RELA_producto_finalizado"] ?? null;
    $cant = $_POST["factura_detalle_cantidad"] ?? null;

    // Validación básica (opcional pero recomendable)
    if ($RELA_factura === null || $RELA_producto_finalizado === null || $cant === null) {
        throw new Exception("Faltan datos obligatorios.");
    }

    // Convertir a enteros
    $RELA_factura = (int)$RELA_factura;
    $RELA_producto_finalizado = (int)$RELA_producto_finalizado;
    $cant = (int)$cant;

    // Query
    $sql = "INSERT INTO factura_detalle 
            (RELA_factura, RELA_producto_finalizado, factura_detalle_cantidad) 
            VALUES (:RELA_factura, :RELA_producto_finalizado, :cant)";

    $stmt = $conexion->prepare($sql);

    // Bind
    $stmt->bindParam(':RELA_factura', $RELA_factura, PDO::PARAM_INT); 
    $stmt->bindParam(':RELA_producto_finalizado', $RELA_producto_finalizado, PDO::PARAM_INT); 
    $stmt->bindParam(':cant', $cant, PDO::PARAM_INT); 

    // Ejecutar
    $stmt->execute(); 
    
    // OK
    echo json_encode(['success' => true]); 

} catch (PDOException $e) { 
    // Error SQL
    error_log("Error de inserción de factura_detalle: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Error al insertar datos.'
    ]);

} catch (Exception $e) {
    // Otro tipo de error
    http_response_code(500);
    echo json_encode([
        'success'=> false, 
        'error' => 'Error inesperado: ' . $e->getMessage()
    ]);
}
?>
