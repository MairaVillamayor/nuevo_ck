<?php
require_once("../../config/conexion.php");

class ProductoFinalizado {

    public function obtenerProductoFinalizado($idProductoFinalizado) {
        $conn = Conexion::getInstance()->getConnection();

        $sql = "SELECT * FROM producto_finalizado WHERE ID_producto_finalizado = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idProductoFinalizado]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarStock($idProductoFinalizado, $nuevoStock) {
        $conn = Conexion::getInstance()->getConnection();

        $sql = "UPDATE producto_finalizado 
                SET stock_actual = :stock 
                WHERE ID_producto_finalizado = :idProd";

        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ":stock"  => $nuevoStock,
            ":idProd" => $idProductoFinalizado
        ]);
    }
}
