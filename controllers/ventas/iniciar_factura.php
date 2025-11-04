<?php
require_once("../../config/conexion.php");
try {
    $RELA_persona  = $_POST["RELA_persona"];
    $RELA_estado_factura = 1;
    $stmt = $conexion->prepare("INSERT INTO factura (RELA_persona, RELA_estado_factura) VALUES (:RELA_persona, :RELA_estado_factura)");
   
    $stmt->bindParam(':RELA_persona', $RELA_persona, PDO::PARAM_INT);
    $stmt->bindParam(':RELA_estado_factura', $RELA_estado_factura, PDO::PARAM_INT);
    $stmt->execute();

    $id_factura = $conexion->getConnection()->lastInsertId();

    echo json_encode(array('success' => true, 'id_factura' => $id_factura));

} catch (PDOException $e) {
    echo json_encode(array('error' => 'Error al iniciar la factura' . $e->getMessage()));
}
