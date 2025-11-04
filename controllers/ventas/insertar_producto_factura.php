<?php 
require_once("../../config/conexion.php"); 
try { 
    $RELA_factura = $_POST["RELA_factura"]; 
    $RELA_producto_finalizado = $_POST["RELA_producto_finalizado"]; 
    $cant = $_POST["factura_detalle_cantidad"]; 
    $stmt = $conexion->prepare("INSERT INTO factura_detalle 
                                (RELA_factura, RELA_producto_finalizado, factura_detalle_cantidad) 
                                VALUES (:RELA_factura, :RELA_producto_finalizado, :cant)"); 
    $stmt->bindParam(':RELA_factura', $RELA_factura, PDO::PARAM_INT); 
    $stmt->bindParam(':RELA_producto_finalizado', $RELA_producto_finalizado, PDO::PARAM_INT); 
    $stmt->bindParam(':cant', $cant, PDO::PARAM_INT); 
    $stmt->execute(); 
    
    echo json_encode(array('success' => true)); 
} catch (PDOException $e) { 
    // Mensaje de error más detallado para debug
    error_log("Error de inserción de factura detalle: " . $e->getMessage());
    echo json_encode(array('success'=>'false', 'error' => 'Error al insertar datos: ' . $e->getMessage()));
} 